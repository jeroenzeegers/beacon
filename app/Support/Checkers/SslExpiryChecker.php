<?php

declare(strict_types=1);

namespace App\Support\Checkers;

use App\Models\Monitor;
use App\Support\CheckResult;

class SslExpiryChecker implements CheckerInterface
{
    public function check(Monitor $monitor): CheckResult
    {
        $host = $monitor->target;
        $port = $monitor->port ?? 443;
        $timeout = $monitor->timeout;

        $options = $monitor->ssl_options ?? [];
        $warningDays = $options['warning_days'] ?? 30;
        $criticalDays = $options['critical_days'] ?? 7;

        $startTime = microtime(true);

        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'capture_peer_cert_chain' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $stream = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );

            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            if (! $stream) {
                return CheckResult::failure(
                    errorMessage: "SSL connection failed: {$errstr} (#{$errno})",
                    responseTime: $responseTime,
                );
            }

            $params = stream_context_get_params($stream);
            $cert = $params['options']['ssl']['peer_certificate'] ?? null;
            $certChain = $params['options']['ssl']['peer_certificate_chain'] ?? [];

            if (! $cert) {
                fclose($stream);

                return CheckResult::failure(
                    errorMessage: 'Could not retrieve SSL certificate',
                    responseTime: $responseTime,
                );
            }

            $certInfo = openssl_x509_parse($cert);
            fclose($stream);

            if (! $certInfo) {
                return CheckResult::failure(
                    errorMessage: 'Could not parse SSL certificate',
                    responseTime: $responseTime,
                );
            }

            $expiryTimestamp = $certInfo['validTo_time_t'];
            $daysRemaining = (int) round(($expiryTimestamp - time()) / 86400);

            // Build comprehensive SSL info
            $sslInfo = $this->buildSslInfo($cert, $certInfo, $certChain, $host, $daysRemaining);

            // Check configuration issues
            $configurationIssues = $this->checkConfiguration($sslInfo, $host);
            if (! empty($configurationIssues)) {
                $sslInfo['configuration_issues'] = $configurationIssues;
                $sslInfo['is_properly_configured'] = false;
            } else {
                $sslInfo['is_properly_configured'] = true;
            }

            // Check if certificate is already expired
            if ($daysRemaining < 0) {
                return CheckResult::failure(
                    errorMessage: 'SSL certificate expired '.abs($daysRemaining).' days ago',
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            // Check critical threshold
            if ($daysRemaining <= $criticalDays) {
                return CheckResult::failure(
                    errorMessage: "SSL certificate expires in {$daysRemaining} days (critical threshold: {$criticalDays})",
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            // Check warning threshold
            if ($daysRemaining <= $warningDays) {
                return CheckResult::degraded(
                    reason: "SSL certificate expires in {$daysRemaining} days (warning threshold: {$warningDays})",
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            // Check for configuration issues even if certificate is valid
            if (! empty($configurationIssues)) {
                return CheckResult::degraded(
                    reason: 'SSL certificate has configuration issues: '.implode(', ', $configurationIssues),
                    responseTime: $responseTime,
                    sslInfo: $sslInfo,
                );
            }

            return CheckResult::success(
                responseTime: $responseTime,
                sslInfo: $sslInfo,
            );
        } catch (\Exception $e) {
            $responseTime = (int) round((microtime(true) - $startTime) * 1000);

            return CheckResult::failure(
                errorMessage: $e->getMessage(),
                responseTime: $responseTime,
            );
        }
    }

    /**
     * Build comprehensive SSL certificate information.
     *
     * @param  \OpenSSLCertificate  $cert
     * @param  array<string, mixed>  $certInfo
     * @param  array<int, \OpenSSLCertificate>  $certChain
     * @return array<string, mixed>
     */
    private function buildSslInfo($cert, array $certInfo, array $certChain, string $host, int $daysRemaining): array
    {
        // Extract public key details
        $publicKey = openssl_pkey_get_public($cert);
        $keyDetails = $publicKey ? openssl_pkey_get_details($publicKey) : null;

        // Determine key type
        $keyType = 'Unknown';
        $keySize = null;
        if ($keyDetails) {
            $keySize = $keyDetails['bits'] ?? null;
            $keyType = match ($keyDetails['type'] ?? -1) {
                OPENSSL_KEYTYPE_RSA => 'RSA',
                OPENSSL_KEYTYPE_DSA => 'DSA',
                OPENSSL_KEYTYPE_DH => 'DH',
                OPENSSL_KEYTYPE_EC => 'EC',
                default => 'Unknown',
            };

            if ($keyType === 'EC' && isset($keyDetails['ec']['curve_name'])) {
                $keyType = 'EC ('.$keyDetails['ec']['curve_name'].')';
            }
        }

        // Extract Subject Alternative Names
        $subjectAltNames = [];
        if (isset($certInfo['extensions']['subjectAltName'])) {
            $sans = explode(', ', $certInfo['extensions']['subjectAltName']);
            foreach ($sans as $san) {
                if (str_starts_with($san, 'DNS:')) {
                    $subjectAltNames[] = substr($san, 4);
                }
            }
        }

        // Check if self-signed
        $isSelfSigned = $this->isSelfSigned($certInfo);

        // Get certificate fingerprint
        $fingerprint = openssl_x509_fingerprint($cert, 'sha256');

        // Build certificate chain info
        $chainInfo = $this->buildChainInfo($certChain);

        // Check OCSP status
        $ocspStatus = $this->checkOcspStatus($cert, $certChain, $certInfo);

        return [
            // Basic info
            'subject' => $certInfo['subject']['CN'] ?? 'Unknown',
            'valid_from' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t']),
            'valid_to' => date('Y-m-d H:i:s', $certInfo['validTo_time_t']),
            'days_remaining' => $daysRemaining,
            'serial_number' => $certInfo['serialNumberHex'] ?? null,

            // Issuer details
            'issuer' => $this->buildIssuerInfo($certInfo),

            // Certificate details
            'signature_algorithm' => $certInfo['signatureTypeSN'] ?? $certInfo['signatureTypeLN'] ?? 'Unknown',
            'key_type' => $keyType,
            'key_size' => $keySize,
            'fingerprint_sha256' => $fingerprint ?: null,

            // Subject Alternative Names
            'subject_alt_names' => $subjectAltNames,
            'covers_host' => $this->certificateCoversHost($certInfo, $subjectAltNames, $host),

            // Self-signed check
            'is_self_signed' => $isSelfSigned,

            // Certificate chain
            'chain' => $chainInfo,
            'chain_length' => count($certChain),
            'chain_valid' => $this->isChainValid($certChain),

            // OCSP/Revocation status
            'ocsp_status' => $ocspStatus['status'],
            'ocsp_message' => $ocspStatus['message'],
            'is_revoked' => $ocspStatus['status'] === 'revoked',

            // Additional extensions
            'has_ocsp_stapling' => isset($certInfo['extensions']['authorityInfoAccess']),
            'has_must_staple' => $this->hasMustStaple($certInfo),
        ];
    }

    /**
     * Build issuer information.
     *
     * @param  array<string, mixed>  $certInfo
     * @return array<string, mixed>
     */
    private function buildIssuerInfo(array $certInfo): array
    {
        $issuer = $certInfo['issuer'] ?? [];

        return [
            'common_name' => $issuer['CN'] ?? null,
            'organization' => $issuer['O'] ?? null,
            'organizational_unit' => $issuer['OU'] ?? null,
            'country' => $issuer['C'] ?? null,
            'state' => $issuer['ST'] ?? null,
            'locality' => $issuer['L'] ?? null,
            'display_name' => $issuer['O'] ?? $issuer['CN'] ?? 'Unknown',
        ];
    }

    /**
     * Build certificate chain information.
     *
     * @param  array<int, \OpenSSLCertificate>  $certChain
     * @return array<int, array<string, mixed>>
     */
    private function buildChainInfo(array $certChain): array
    {
        $chainInfo = [];

        foreach ($certChain as $index => $chainCert) {
            $info = openssl_x509_parse($chainCert);
            if ($info) {
                $chainInfo[] = [
                    'position' => $index,
                    'subject' => $info['subject']['CN'] ?? 'Unknown',
                    'issuer' => $info['issuer']['O'] ?? $info['issuer']['CN'] ?? 'Unknown',
                    'valid_from' => date('Y-m-d', $info['validFrom_time_t']),
                    'valid_to' => date('Y-m-d', $info['validTo_time_t']),
                    'is_root' => $this->isSelfSigned($info),
                ];
            }
        }

        return $chainInfo;
    }

    /**
     * Check if a certificate is self-signed.
     *
     * @param  array<string, mixed>  $certInfo
     */
    private function isSelfSigned(array $certInfo): bool
    {
        $subject = $certInfo['subject'] ?? [];
        $issuer = $certInfo['issuer'] ?? [];

        // Compare subject and issuer
        return ($subject['CN'] ?? '') === ($issuer['CN'] ?? '')
            && ($subject['O'] ?? '') === ($issuer['O'] ?? '');
    }

    /**
     * Check if certificate chain is valid.
     *
     * @param  array<int, \OpenSSLCertificate>  $certChain
     */
    private function isChainValid(array $certChain): bool
    {
        if (empty($certChain)) {
            return false;
        }

        // Check each certificate in the chain
        foreach ($certChain as $cert) {
            $info = openssl_x509_parse($cert);
            if (! $info) {
                return false;
            }

            // Check if certificate is expired
            if (time() > $info['validTo_time_t'] || time() < $info['validFrom_time_t']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if certificate covers the given host.
     *
     * @param  array<string, mixed>  $certInfo
     * @param  array<int, string>  $subjectAltNames
     */
    private function certificateCoversHost(array $certInfo, array $subjectAltNames, string $host): bool
    {
        // Check CN
        $cn = $certInfo['subject']['CN'] ?? '';
        if ($this->hostMatchesPattern($host, $cn)) {
            return true;
        }

        // Check SANs
        foreach ($subjectAltNames as $san) {
            if ($this->hostMatchesPattern($host, $san)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if host matches a pattern (supports wildcards).
     */
    private function hostMatchesPattern(string $host, string $pattern): bool
    {
        if ($host === $pattern) {
            return true;
        }

        // Handle wildcard certificates
        if (str_starts_with($pattern, '*.')) {
            $patternDomain = substr($pattern, 2);
            $hostParts = explode('.', $host, 2);

            if (count($hostParts) === 2 && $hostParts[1] === $patternDomain) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check OCSP status for certificate revocation.
     *
     * @param  \OpenSSLCertificate  $cert
     * @param  array<int, \OpenSSLCertificate>  $certChain
     * @param  array<string, mixed>  $certInfo
     * @return array{status: string, message: string}
     */
    private function checkOcspStatus($cert, array $certChain, array $certInfo): array
    {
        // Check if OCSP URL is available
        $ocspUrl = $this->extractOcspUrl($certInfo);

        if (! $ocspUrl) {
            return [
                'status' => 'unknown',
                'message' => 'No OCSP responder URL found in certificate',
            ];
        }

        // We need the issuer certificate to make an OCSP request
        if (count($certChain) < 2) {
            return [
                'status' => 'unknown',
                'message' => 'Unable to verify: issuer certificate not in chain',
            ];
        }

        try {
            $issuerCert = $certChain[1] ?? null;
            if (! $issuerCert) {
                return [
                    'status' => 'unknown',
                    'message' => 'Issuer certificate not available',
                ];
            }

            // Perform OCSP check using command line (more reliable than pure PHP)
            $result = $this->performOcspCheck($cert, $issuerCert, $ocspUrl);

            return $result;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'OCSP check failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Extract OCSP URL from certificate.
     *
     * @param  array<string, mixed>  $certInfo
     */
    private function extractOcspUrl(array $certInfo): ?string
    {
        $authorityInfo = $certInfo['extensions']['authorityInfoAccess'] ?? '';

        if (preg_match('/OCSP - URI:(\S+)/', $authorityInfo, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Perform OCSP check using OpenSSL.
     *
     * @param  \OpenSSLCertificate  $cert
     * @param  \OpenSSLCertificate  $issuerCert
     * @return array{status: string, message: string}
     */
    private function performOcspCheck($cert, $issuerCert, string $ocspUrl): array
    {
        // Create temp files for the certificates
        $certFile = tempnam(sys_get_temp_dir(), 'cert_');
        $issuerFile = tempnam(sys_get_temp_dir(), 'issuer_');

        try {
            openssl_x509_export($cert, $certPem);
            openssl_x509_export($issuerCert, $issuerPem);

            file_put_contents($certFile, $certPem);
            file_put_contents($issuerFile, $issuerPem);

            // Run OpenSSL OCSP command with timeout
            $command = sprintf(
                'timeout 5 openssl ocsp -issuer %s -cert %s -url %s -resp_text 2>&1',
                escapeshellarg($issuerFile),
                escapeshellarg($certFile),
                escapeshellarg($ocspUrl)
            );

            $output = shell_exec($command);

            if ($output === null) {
                return [
                    'status' => 'unknown',
                    'message' => 'OCSP check timed out or failed',
                ];
            }

            // Parse the OCSP response
            if (str_contains($output, 'Response verify OK') || str_contains($output, 'good')) {
                if (str_contains($output, 'revoked')) {
                    return [
                        'status' => 'revoked',
                        'message' => 'Certificate has been revoked',
                    ];
                }

                return [
                    'status' => 'good',
                    'message' => 'Certificate is valid (not revoked)',
                ];
            }

            if (str_contains($output, 'revoked')) {
                return [
                    'status' => 'revoked',
                    'message' => 'Certificate has been revoked',
                ];
            }

            return [
                'status' => 'unknown',
                'message' => 'Could not determine OCSP status',
            ];
        } finally {
            @unlink($certFile);
            @unlink($issuerFile);
        }
    }

    /**
     * Check if certificate has OCSP Must-Staple extension.
     *
     * @param  array<string, mixed>  $certInfo
     */
    private function hasMustStaple(array $certInfo): bool
    {
        // OCSP Must-Staple is indicated by the TLS Feature extension (OID 1.3.6.1.5.5.7.1.24)
        // with value status_request (5)
        $extensions = $certInfo['extensions'] ?? [];

        foreach ($extensions as $oid => $value) {
            if ($oid === '1.3.6.1.5.5.7.1.24' || str_contains($value, 'status_request')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for configuration issues.
     *
     * @param  array<string, mixed>  $sslInfo
     * @return array<int, string>
     */
    private function checkConfiguration(array $sslInfo, string $host): array
    {
        $issues = [];

        // Check if certificate covers the host
        if (! ($sslInfo['covers_host'] ?? false)) {
            $issues[] = 'Certificate does not cover this hostname';
        }

        // Check if self-signed
        if ($sslInfo['is_self_signed'] ?? false) {
            $issues[] = 'Certificate is self-signed';
        }

        // Check key size for RSA
        if (($sslInfo['key_type'] ?? '') === 'RSA' && ($sslInfo['key_size'] ?? 0) < 2048) {
            $issues[] = 'RSA key size is less than 2048 bits';
        }

        // Check for weak signature algorithms
        $weakAlgorithms = ['sha1WithRSAEncryption', 'md5WithRSAEncryption', 'sha1', 'md5'];
        $sigAlgo = strtolower($sslInfo['signature_algorithm'] ?? '');
        foreach ($weakAlgorithms as $weak) {
            if (str_contains($sigAlgo, $weak)) {
                $issues[] = 'Uses weak signature algorithm: '.$sslInfo['signature_algorithm'];
                break;
            }
        }

        // Check if chain is valid
        if (! ($sslInfo['chain_valid'] ?? true)) {
            $issues[] = 'Certificate chain validation failed';
        }

        // Check if revoked
        if ($sslInfo['is_revoked'] ?? false) {
            $issues[] = 'Certificate has been revoked';
        }

        return $issues;
    }

    public function supports(string $type): bool
    {
        return $type === Monitor::TYPE_SSL_EXPIRY;
    }
}

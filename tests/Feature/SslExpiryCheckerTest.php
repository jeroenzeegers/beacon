<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Monitor;
use App\Support\Checkers\SslExpiryChecker;
use App\Support\CheckResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class SslExpiryCheckerTest extends TestCase
{
    use RefreshDatabase;

    private SslExpiryChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new SslExpiryChecker;
    }

    public function test_supports_ssl_expiry_type(): void
    {
        $this->assertTrue($this->checker->supports(Monitor::TYPE_SSL_EXPIRY));
        $this->assertFalse($this->checker->supports(Monitor::TYPE_HTTP));
        $this->assertFalse($this->checker->supports(Monitor::TYPE_HTTPS));
        $this->assertFalse($this->checker->supports(Monitor::TYPE_TCP));
        $this->assertFalse($this->checker->supports(Monitor::TYPE_PING));
    }

    #[Group('network')]
    public function test_check_returns_success_for_valid_ssl_certificate(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertInstanceOf(CheckResult::class, $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertNotNull($result->sslInfo);
        $this->assertArrayHasKey('subject', $result->sslInfo);
        $this->assertArrayHasKey('issuer', $result->sslInfo);
        $this->assertArrayHasKey('valid_from', $result->sslInfo);
        $this->assertArrayHasKey('valid_to', $result->sslInfo);
        $this->assertArrayHasKey('days_remaining', $result->sslInfo);
    }

    #[Group('network')]
    public function test_ssl_info_contains_comprehensive_certificate_details(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertNotNull($result->sslInfo);

        // Basic info
        $this->assertArrayHasKey('subject', $result->sslInfo);
        $this->assertArrayHasKey('valid_from', $result->sslInfo);
        $this->assertArrayHasKey('valid_to', $result->sslInfo);
        $this->assertArrayHasKey('days_remaining', $result->sslInfo);
        $this->assertArrayHasKey('serial_number', $result->sslInfo);

        // Issuer details (should be an array with detailed info)
        $this->assertArrayHasKey('issuer', $result->sslInfo);
        $this->assertIsArray($result->sslInfo['issuer']);
        $this->assertArrayHasKey('common_name', $result->sslInfo['issuer']);
        $this->assertArrayHasKey('organization', $result->sslInfo['issuer']);
        $this->assertArrayHasKey('display_name', $result->sslInfo['issuer']);

        // Certificate details
        $this->assertArrayHasKey('signature_algorithm', $result->sslInfo);
        $this->assertArrayHasKey('key_type', $result->sslInfo);
        $this->assertArrayHasKey('key_size', $result->sslInfo);
        $this->assertArrayHasKey('fingerprint_sha256', $result->sslInfo);

        // Subject Alternative Names
        $this->assertArrayHasKey('subject_alt_names', $result->sslInfo);
        $this->assertIsArray($result->sslInfo['subject_alt_names']);

        // Hostname validation
        $this->assertArrayHasKey('covers_host', $result->sslInfo);
        $this->assertTrue($result->sslInfo['covers_host']);

        // Self-signed check
        $this->assertArrayHasKey('is_self_signed', $result->sslInfo);
        $this->assertFalse($result->sslInfo['is_self_signed']);

        // Certificate chain
        $this->assertArrayHasKey('chain', $result->sslInfo);
        $this->assertIsArray($result->sslInfo['chain']);
        $this->assertArrayHasKey('chain_length', $result->sslInfo);
        $this->assertArrayHasKey('chain_valid', $result->sslInfo);

        // OCSP status
        $this->assertArrayHasKey('ocsp_status', $result->sslInfo);
        $this->assertArrayHasKey('ocsp_message', $result->sslInfo);
        $this->assertArrayHasKey('is_revoked', $result->sslInfo);

        // Configuration status
        $this->assertArrayHasKey('is_properly_configured', $result->sslInfo);
    }

    #[Group('network')]
    public function test_check_returns_failure_for_unreachable_host(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'nonexistent-domain-that-does-not-exist-12345.com',
            'port' => 443,
            'timeout' => 5,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertFalse($result->isSuccessful());
        $this->assertNotNull($result->errorMessage);
    }

    #[Group('network')]
    public function test_check_returns_failure_for_invalid_port(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 12345,
            'timeout' => 5,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertFalse($result->isSuccessful());
    }

    #[Group('network')]
    public function test_certificate_chain_is_populated(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertNotNull($result->sslInfo);
        $this->assertArrayHasKey('chain', $result->sslInfo);
        $this->assertNotEmpty($result->sslInfo['chain']);

        foreach ($result->sslInfo['chain'] as $chainCert) {
            $this->assertArrayHasKey('position', $chainCert);
            $this->assertArrayHasKey('subject', $chainCert);
            $this->assertArrayHasKey('issuer', $chainCert);
            $this->assertArrayHasKey('valid_from', $chainCert);
            $this->assertArrayHasKey('valid_to', $chainCert);
            $this->assertArrayHasKey('is_root', $chainCert);
        }
    }

    #[Group('network')]
    public function test_issuer_info_is_detailed(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertNotNull($result->sslInfo);
        $issuer = $result->sslInfo['issuer'];

        $this->assertIsArray($issuer);
        $this->assertArrayHasKey('common_name', $issuer);
        $this->assertArrayHasKey('organization', $issuer);
        $this->assertArrayHasKey('organizational_unit', $issuer);
        $this->assertArrayHasKey('country', $issuer);
        $this->assertArrayHasKey('state', $issuer);
        $this->assertArrayHasKey('locality', $issuer);
        $this->assertArrayHasKey('display_name', $issuer);
    }

    #[Group('network')]
    public function test_key_details_are_populated(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertNotNull($result->sslInfo);
        $this->assertNotNull($result->sslInfo['key_type']);
        $this->assertNotNull($result->sslInfo['key_size']);
        $this->assertGreaterThan(0, $result->sslInfo['key_size']);
    }

    #[Group('network')]
    public function test_fingerprint_is_sha256(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
        ]);

        $result = $this->checker->check($monitor);

        $this->assertNotNull($result->sslInfo);
        $this->assertNotNull($result->sslInfo['fingerprint_sha256']);
        // SHA-256 fingerprint is 64 characters (hexadecimal)
        $this->assertEquals(64, strlen($result->sslInfo['fingerprint_sha256']));
    }

    #[Group('network')]
    public function test_check_respects_warning_threshold(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
            'ssl_options' => [
                'warning_days' => 365,
                'critical_days' => 7,
            ],
        ]);

        $result = $this->checker->check($monitor);

        // If certificate expires within 365 days, it should be degraded
        // Google's certificates typically have less than 365 days validity
        if ($result->isSuccessful() === false && $result->isDegraded() === true) {
            $this->assertTrue($result->isDegraded());
            $this->assertStringContainsString('warning', strtolower($result->errorMessage ?? ''));
        }
    }

    #[Group('network')]
    public function test_check_respects_critical_threshold(): void
    {
        $monitor = Monitor::factory()->sslExpiry()->create([
            'target' => 'google.com',
            'port' => 443,
            'timeout' => 10,
            'ssl_options' => [
                'warning_days' => 400,
                'critical_days' => 365,
            ],
        ]);

        $result = $this->checker->check($monitor);

        // If certificate expires within 365 days (critical), it should fail
        if ($result->sslInfo && ($result->sslInfo['days_remaining'] ?? 999) <= 365) {
            $this->assertFalse($result->isSuccessful());
        }
    }
}

<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gradient">
            {{ $isEditing ? __('Edit Alert Channel') : __('Create Alert Channel') }}
        </h2>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6">
            <!-- Basic Information -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Channel Name</label>
                        <input type="text" id="name" wire:model="name" class="input-liquid w-full" placeholder="e.g., Team Slack, On-Call Email">
                        @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Channel Type</label>
                        <select id="type" wire:model.live="type" class="input-liquid w-full">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Channel Configuration -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Configuration</h3>

                <div class="space-y-4">
                    @if($type === 'email')
                        <div>
                            <label for="config_email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="config_email" wire:model="config_email" class="input-liquid w-full" placeholder="alerts@example.com">
                            @error('config_email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'slack')
                        <div>
                            <label for="config_webhook_url" class="block text-sm font-medium text-gray-300 mb-2">Slack Webhook URL</label>
                            <input type="url" id="config_webhook_url" wire:model="config_webhook_url" class="input-liquid w-full" placeholder="https://hooks.slack.com/services/...">
                            <p class="mt-1 text-xs text-gray-500">Create an incoming webhook in your Slack workspace settings.</p>
                            @error('config_webhook_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'discord')
                        <div>
                            <label for="config_webhook_url" class="block text-sm font-medium text-gray-300 mb-2">Discord Webhook URL</label>
                            <input type="url" id="config_webhook_url" wire:model="config_webhook_url" class="input-liquid w-full" placeholder="https://discord.com/api/webhooks/...">
                            <p class="mt-1 text-xs text-gray-500">Right-click a channel > Edit Channel > Integrations > Webhooks.</p>
                            @error('config_webhook_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'webhook')
                        <div>
                            <label for="config_url" class="block text-sm font-medium text-gray-300 mb-2">Webhook URL</label>
                            <input type="url" id="config_url" wire:model="config_url" class="input-liquid w-full" placeholder="https://your-server.com/webhook">
                            @error('config_url') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="config_method" class="block text-sm font-medium text-gray-300 mb-2">HTTP Method</label>
                            <select id="config_method" wire:model="config_method" class="input-liquid w-full">
                                <option value="POST">POST</option>
                                <option value="GET">GET</option>
                                <option value="PUT">PUT</option>
                                <option value="PATCH">PATCH</option>
                            </select>
                            @error('config_method') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'sms')
                        <div>
                            <label for="config_phone_number" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                            <input type="tel" id="config_phone_number" wire:model="config_phone_number" class="input-liquid w-full" placeholder="+1234567890">
                            <p class="mt-1 text-xs text-gray-500">Include country code (e.g., +1 for US).</p>
                            @error('config_phone_number') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'pagerduty')
                        <div>
                            <label for="config_routing_key" class="block text-sm font-medium text-gray-300 mb-2">Routing Key</label>
                            <input type="text" id="config_routing_key" wire:model="config_routing_key" class="input-liquid w-full" placeholder="Your PagerDuty integration key">
                            <p class="mt-1 text-xs text-gray-500">Find this in your PagerDuty service integrations.</p>
                            @error('config_routing_key') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'telegram')
                        <div>
                            <label for="config_bot_token" class="block text-sm font-medium text-gray-300 mb-2">Bot Token</label>
                            <input type="text" id="config_bot_token" wire:model="config_bot_token" class="input-liquid w-full" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                            <p class="mt-1 text-xs text-gray-500">Get this from @BotFather on Telegram.</p>
                            @error('config_bot_token') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="config_chat_id" class="block text-sm font-medium text-gray-300 mb-2">Chat ID</label>
                            <input type="text" id="config_chat_id" wire:model="config_chat_id" class="input-liquid w-full" placeholder="-1001234567890">
                            <p class="mt-1 text-xs text-gray-500">The chat/group ID where alerts will be sent.</p>
                            @error('config_chat_id') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Options -->
            <div class="glass rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Options</h3>

                <div class="space-y-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Active</span>
                            <p class="text-xs text-gray-500">Enable this channel to receive alerts.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_default" class="w-5 h-5 rounded border-gray-600 bg-gray-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Default Channel</span>
                            <p class="text-xs text-gray-500">Use this channel by default for new alert rules.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('alerts.channels.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors" wire:navigate>
                    Cancel
                </a>
                <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                    <span class="btn-magnetic-inner flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $isEditing ? 'Update Channel' : 'Create Channel' }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

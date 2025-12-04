<x-slot name="header">
    <h2 class="text-2xl font-bold text-gradient">
        {{ $projectId ? __('Edit Project') : __('Create Project') }}
    </h2>
</x-slot>

<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass rounded-2xl overflow-hidden scroll-reveal">
            <form wire:submit="save">
                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div class="scroll-reveal">
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                        <input wire:model="name" type="text" id="name" class="input-liquid block w-full" placeholder="My Project">
                        @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div class="scroll-reveal">
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea wire:model="description" id="description" rows="3" class="input-liquid block w-full" placeholder="A brief description of this project..."></textarea>
                        @error('description') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Environment -->
                    <div class="scroll-reveal">
                        <label for="environment" class="block text-sm font-medium text-gray-300 mb-2">Environment</label>
                        <select wire:model="environment" id="environment" class="input-liquid block w-full">
                            @foreach($environments as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('environment') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Active -->
                    <div class="flex items-center gap-3 scroll-reveal">
                        <input wire:model="is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded bg-white/5 border-white/10 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
                        <label for="is_active" class="text-sm text-gray-300">Active</label>
                    </div>
                </div>

                <div class="px-6 py-4 bg-white/[0.02] border-t border-white/5 flex justify-end gap-3">
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium text-gray-300 bg-white/5 border border-white/10 hover:bg-white/10 transition-colors focus-ring" wire:navigate>
                        Cancel
                    </a>
                    <button type="submit" class="btn-liquid btn-magnetic ripple-effect inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
                        <span class="btn-magnetic-inner flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $projectId ? 'Update Project' : 'Create Project' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

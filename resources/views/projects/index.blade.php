<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Projects') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('projects.index') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">
                    Project List
                </a>
                <a href="{{ route('projects.timeline') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                    Timeline
                </a>
                <a href="{{ route('projects.status') }}" 
                   class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700">
                    Status
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:project.project-list />
            </div>
        </div>
    </div>
</x-app-layout>
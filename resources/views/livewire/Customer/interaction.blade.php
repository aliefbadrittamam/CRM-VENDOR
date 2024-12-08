@if (session()->has('error'))
    <div class="bg-red-100 p-4 mb-4 rounded">
        {{ session('error') }}
    </div>
@endif
<div class="p-6">
    <!-- Header dengan Customer Selection -->
    <div class="mb-6">
        <div class="flex flex-col space-y-4">
            <!-- Customer Dropdown -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                <select wire:model.live="selectedCustomerId" 
                    class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 px-4 py-2">
                    <option value="">Choose a customer...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}">
                            {{ $customer->customer_name }} - {{ $customer->customer_email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Header dan Add Button -->
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">
                    @if($selectedCustomerId)
                        Customer Interactions - {{ $selectedCustomerName }}
                    @else
                        Select a customer to view interactions
                    @endif
                </h2>
                @if($selectedCustomerId)
                    <button wire:click="openModal" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Interaction
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($selectedCustomerId)
        <!-- Interactions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($interactions as $interaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($interaction->interaction_type === 'Call') bg-green-100 text-green-800
                                    @elseif($interaction->interaction_type === 'Email') bg-blue-100 text-blue-800
                                    @elseif($interaction->interaction_type === 'Meeting') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $interaction->interaction_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $interaction->interaction_date->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 line-clamp-2">{{ $interaction->notes }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $interaction->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="openModal({{ $interaction->interaction_id }})" 
                                    class="text-blue-600 hover:text-blue-900">Edit</button>
                                <button wire:click="deleteInteraction({{ $interaction->interaction_id }})" 
                                    class="ml-3 text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No interactions found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $interactions->links() }}
        </div>
    @endif

    <!-- Modal Form - Sederhana tapi tetap bagus -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50">
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-lg">
                        <!-- Form -->
                        <form wire:submit.prevent="save">
                            <div class="p-6">
                                <!-- Interaction Type -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Interaction Type</label>
                                    <select wire:model="interaction_type" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Type</option>
                                        <option value="Call">Call</option>
                                        <option value="Email">Email</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('interaction_type') 
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                    <textarea wire:model="notes" rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    @error('notes') 
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Buttons -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end space-x-3">
                                <button type="button" wire:click="closeModal"
                                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                    {{ $editingInteractionId ? 'Update' : 'Save' }}
                                </button>
                            </div>
                        </form>
                        <div wire:loading wire:target="save">
                            Processing...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
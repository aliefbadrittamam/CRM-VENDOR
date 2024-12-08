<div class="p-6">
    <!-- Header dengan Customer Selection dan Tombol Add -->
    <div class="mb-6">
        <!-- Customer Selection -->
        <div class="flex flex-col space-y-4">
            <!-- Dropdown Customer -->
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

            <!-- Title dan Tombol Add -->
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
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Interaction
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($selectedCustomerId)
        <!-- Search dan Filter Section -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-4 items-center">
                <!-- Search Box -->
                <div class="flex-1">
                    <input type="text" 
                        wire:model.live="search" 
                        placeholder="Search interactions..." 
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date Range Filter -->
                <div class="flex gap-2">
                    <input type="date" 
                        wire:model.live="filters.date_from" 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="date" 
                        wire:model.live="filters.date_to" 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Interaction Type Filter -->
                <select wire:model.live="filters.type" 
                    class="px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="Call">Call</option>
                    <option value="Email">Email</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Other">Other</option>
                </select>

                <!-- Reset Filters -->
                @if($filters['date_from'] || $filters['date_to'] || $filters['type'])
                    <button wire:click="resetFilters" 
                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Interactions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <button wire:click="deleteInteraction({{ $interaction->interaction_id }})"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
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
    @else
        <!-- No Customer Selected Message -->
        <div class="bg-white rounded-lg shadow-sm p-6 text-center text-gray-500">
            Please select a customer to view their interactions
        </div>
    @endif

    <!-- Interaction Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form wire:submit.prevent="save">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Interaction Type
                                    </label>
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

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Notes
                                    </label>
                                    <textarea wire:model="notes" 
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Enter interaction details..."></textarea>
                                    @error('notes') 
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" 
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto disabled:opacity-50"
                                    x-bind:disabled="isSubmitting"
                                    @click="isSubmitting = true">
                                    <span x-show="!isSubmitting">{{ $editingInteractionId ? 'Update' : 'Save' }}</span>
                                    <span x-show="isSubmitting" class="inline-flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                                <button type="button" 
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                    wire:click="closeModal"
                                    x-bind:disabled="isSubmitting">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Styles -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <!-- SweetAlert Scripts -->
    @script
    <script>
        window.addEventListener('showAlert', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.message,
                icon: event.detail.icon,
                timer: event.detail.timer,
                showConfirmButton: false,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
    @endscript
</div>
<div class="p-6">
     <!-- Debug section (temporary) -->
     @if(session()->has('error'))
     <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
         {{ session('error') }}
     </div>
 @endif
 
 @if(!empty($debugMessage))
     <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
         Debug: {{ $debugMessage }}
     </div>
 @endif
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Price Quotations</h2>
        <button wire:click="create"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Quotation
        </button>
    </div>

    

    <!-- Filter Section -->
    
   <!-- Di bagian Filters section price-quotation.blade.php -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
        <input type="text" wire:model.live="search" 
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Search project or vendor...">
    </div>
    <div>
        <select wire:model.live="projectFilter" 
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->project_id }}">{{ $project->project_header }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <select wire:model.live="vendorFilter" 
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Vendors</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <button wire:click="resetFilters" 
            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Reset Filters
        </button>
    </div>
</div>

<!-- Tambahkan indikator loading state -->
<div wire:loading class="fixed top-0 left-0 right-0">
    <div class="bg-blue-500 h-1 w-full animate-pulse"></div>
</div>

    <!-- Quotations Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($quotations as $quotation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $quotation->project->project_header }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $quotation->vendor->vendor_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Rp {{ number_format($quotation->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $quotation->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $quotation->price_quotation_id }})"
                                class="text-blue-600 hover:text-blue-900">Edit</button>
                            <button wire:click="delete({{ $quotation->price_quotation_id }})"
                                wire:confirm="Are you sure you want to delete this quotation?"
                                class="ml-3 text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No quotations found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $quotations->links() }}
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                        <form wire:submit="save">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    {{ $editMode ? 'Edit Price Quotation' : 'Create New Price Quotation' }}
                                </h3>

                                <!-- Project Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Project</label>
                                    <select wire:model="project_id"
                                        class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->project_id }}">{{ $project->project_header }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="text-sm text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Vendor Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Vendor</label>
                                    <select wire:model="vendor_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Select Vendor</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <span class="text-sm text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Amount Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model="amount"
                                            class="mt-1 block w-full pl-12 rounded-md border-gray-300" placeholder="0">
                                    </div>
                                    @error('amount')
                                        <span class="text-sm text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    {{ $editMode ? 'Update' : 'Save' }}
                                </button>
                                <button type="button" wire:click="$set('showModal', false)"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notification -->
    <div x-data="{ show: false, message: '' }"
        x-on:quotation-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        class="fixed bottom-0 right-0 m-6">
        <div x-show="show" x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            class="max-w-sm w-full bg-green-100 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p x-text="message" class="text-sm font-medium text-green-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false"
                            class="rounded-md inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

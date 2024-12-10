<div class="p-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Sales Orders</h2>
            <p class="mt-1 text-sm text-gray-600">Manage customer orders and transactions</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Order
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <input type="text" wire:model.live="search" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Search customer name or email...">
        </div>
        <div>
            <select wire:model.live="statusFilter" 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sale->customer->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $sale->customer->customer_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $sale->sale_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <select wire:change="updateStatus({{ $sale->sale_id }}, $event.target.value)"
                                    class="block w-full text-sm rounded-md border-gray-300
                                    {{ $sale->status === 'Completed' ? 'text-green-800 bg-green-100' : 
                                       ($sale->status === 'Processing' ? 'text-blue-800 bg-blue-100' : 
                                       ($sale->status === 'Cancelled' ? 'text-red-800 bg-red-100' : 
                                       'text-yellow-800 bg-yellow-100')) }}">
                                    <option value="Pending" {{ $sale->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Processing" {{ $sale->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="Completed" {{ $sale->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Cancelled" {{ $sale->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Rp {{ number_format($sale->fixed_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @foreach($sale->details as $detail)
                                    <div class="mb-1">
                                        {{ $detail->product->product_name }} ({{ $detail->quantity }})
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $sale->sale_id }})" 
                                class="text-blue-600 hover:text-blue-900">Edit</button>
                            <button wire:click="delete({{ $sale->sale_id }})" 
                                wire:confirm="Are you sure you want to delete this order?"
                                class="ml-3 text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No orders found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $sales->links() }}
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
                        <form wire:submit="save">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    {{ $editMode ? 'Edit Sales Order' : 'Create New Sales Order' }}
                                </h3>

                                <!-- Customer Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Customer</label>
                                    <select wire:model="customer_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}">
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <!-- Date Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Sale Date</label>
                                    <input type="date" wire:model="sale_date" 
                                        class="mt-1 block w-full rounded-md border-gray-300">
                                    @error('sale_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <!-- Products Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Products</label>
                                    <div class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                                        @foreach($products as $product)
                                            <div class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                                                <input type="checkbox" 
                                                    wire:model="selectedProducts" 
                                                    value="{{ $product->product_id }}"
                                                    class="rounded border-gray-300 text-blue-600">
                                                <label class="flex-1">
                                                    <span class="font-medium">{{ $product->product_name }}</span>
                                                    <span class="text-gray-500"> - Rp {{ number_format($product->product_price, 0, ',', '.') }}</span>
                                                </label>
                                                @if(in_array($product->product_id, $selectedProducts))
                                                    <input type="number" 
                                                        wire:model="quantities.{{ $product->product_id }}"
                                                        wire:change="calculateTotal"
                                                        class="w-20 rounded-md border-gray-300"
                                                        min="1"
                                                        placeholder="Qty">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('selectedProducts') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <!-- Status Field -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="Pending">Pending</option>
                                        <option value="Processing">Processing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    @error('status') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>

                                <!-- Total Amount (Read Only) -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="text" readonly value="{{ number_format($fixed_amount, 0, ',', '.') }}"
                                            class="mt-1 block w-full pl-12 rounded-md border-gray-300 bg-gray-50">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    {{ $editMode ? 'Update Order' : 'Create Order' }}
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
         x-on:order-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         x-on:order-deleted.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         class="fixed bottom-0 right-0 m-6">
        <div x-show="show" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             class="max-w-sm w-full bg-green-100 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p x-text="message" class="text-sm font-medium text-green-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="rounded-md inline-flex text-green-500 hover:text-green-<button @click="show = false" class="rounded-md inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
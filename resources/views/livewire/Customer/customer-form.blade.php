<!-- resources/views/livewire/customer/customer-form.blade.php -->
<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900">
        {{ $customerId ? 'Edit Customer' : 'Add New Customer' }}
    </h2>

    <form wire:submit="save" class="mt-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="customer_name" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('customer_name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="customer_email" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('customer_email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            @if(!$customerId)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" wire:model="customer_phone" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('customer_phone') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea wire:model="customer_address" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('customer_address') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" wire:click="$dispatch('closeModal')" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </button>
            <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                {{ $customerId ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>
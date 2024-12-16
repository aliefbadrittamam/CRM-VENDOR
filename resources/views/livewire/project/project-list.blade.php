<div class="p-6">
    
    <!-- Header with Filter Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Project List</h2>
            <p class="mt-1 text-sm text-gray-600">Manage and track all projects</p>
        </div>
        <button wire:click="openModal"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Project
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" wire:model.live="search"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Search projects...">
        </div>
        <div>
            <select wire:model.live="vendorFilter"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Vendors</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="customerFilter"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Customers</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->customer_id }}">{{ $customer->customer_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="statusFilter"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div>
            <input type="date" wire:model.live="dateFilter"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                        Project
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                        Vendor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                        Duration
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Value
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 100px;">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $project->project_header }}</div>
                            <div class="text-sm text-gray-500">{{ $project->project_detail }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $project->vendor->vendor_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $project->customer->customer_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm space-y-2">
                                <!-- Start Date -->
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">
                                        {{ Carbon\Carbon::parse($project->project_duration_start)->format('d M Y') }}
                                    </span>
                                </div>

                                <!-- End Date -->
                                <div class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">
                                        {{ Carbon\Carbon::parse($project->project_duration_end)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $this->getProjectStatus($project);
                            @endphp
                            <div class="text-sm">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $status['status'] }}
                                </span>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $status['days_remaining'] }}
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                Rp {{ number_format($project->project_value, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-col space-y-2">
                                <!-- Detail Button -->
                                <button wire:click="showDetail({{ $project->project_id }})" 
                                    class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 w-full justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </button>
                        
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $project->project_id }})" 
                                    class="inline-flex items-center px-2 py-1 bg-green-100 text-green-600 rounded hover:bg-green-200 w-full justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                        
                                <!-- Delete Button -->
                                <button wire:click="confirmDelete({{ $project->project_id }})"
                                    class="inline-flex items-center px-2 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 w-full justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No projects found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $projects->links() }}
    </div>

    <!-- Project Form Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <form wire:submit="save">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    {{ $editMode ? 'Edit Project' : 'Create New Project' }}
                                </h3>

                                <div class="space-y-4">
                                    <!-- Vendor Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Vendor</label>
                                        <select wire:model="vendor_id"
                                            class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="">Select Vendor</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Customer Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                                        <select wire:model="customer_id"
                                            class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="">Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->customer_id }}">
                                                    {{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Products Selection -->
                                    <div class="space-y-4">
                                        <label class="block text-sm font-medium text-gray-700">Products</label>
                                        <div class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                                            @foreach ($products as $product)
                                                <div class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                                                    <input type="checkbox"
                                                        wire:model.live="selectedProducts.{{ $product->product_id }}"
                                                        class="rounded border-gray-300">
                                                    <label class="flex-1">
                                                        <span class="font-medium">{{ $product->product_name }}</span>
                                                        <span class="text-gray-500"> - Rp
                                                            {{ number_format($product->product_price, 0, ',', '.') }}</span>
                                                    </label>
                                                    @if (isset($selectedProducts[$product->product_id]) && $selectedProducts[$product->product_id])
                                                        <input type="number"
                                                            wire:model.live="quantities.{{ $product->product_id }}"
                                                            class="w-20 rounded-md border-gray-300" min="1"
                                                            placeholder="Qty">
                                                        <span class="text-gray-600">
                                                            Rp
                                                            {{ number_format($productSubtotals[$product->product_id] ?? 0, 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Products Total -->
                                    <div class="mt-4">
                                        <div class="text-sm font-medium text-gray-700">Products Total</div>
                                        <div class="text-lg font-semibold">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Additional Value -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700">Additional Project
                                            Value</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" wire:model.live="additional_value"
                                                class="mt-1 block w-full pl-12 rounded-md border-gray-300"
                                                placeholder="0">
                                        </div>
                                    </div>

                                    <!-- Total Project Value -->
                                    <div class="mt-4">
                                        <div class="text-sm font-medium text-gray-700">Total Project Value</div>
                                        <div class="text-xl font-bold text-blue-600">
                                            Rp {{ number_format($project_value, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Total Amount (Read Only) -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Total Project
                                            Value</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="text" value="{{ number_format($total, 0, ',', '.') }}"
                                                class="mt-1 block w-full pl-12 rounded-md border-gray-300 bg-gray-50"
                                                readonly>
                                        </div>
                                    </div>

                                    <!-- Project Header Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Project Header</label>
                                        <input type="text" wire:model="project_header"
                                            class="mt-1 block w-full rounded-md border-gray-300">
                                        @error('project_header')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Project Value Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Project Value</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" wire:model="project_value"
                                                class="pl-12 block w-full rounded-md border-gray-300">
                                        </div>
                                        @error('project_value')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Project Duration Fields -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                            <input type="date" wire:model="project_duration_start"
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            @error('project_duration_start')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                                            <input type="date" wire:model="project_duration_end"
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            @error('project_duration_end')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Project Detail Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Project Detail</label>
                                        <textarea wire:model="project_detail" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                        @error('project_detail')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                                    {{ $editMode ? 'Update' : 'Create' }}
                                </button>
                                <button type="button" wire:click="closeModal"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Delete Project</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to delete this project? This action cannot be undone.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button wire:click="delete" type="button"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                Delete
                            </button>
                            <button wire:click="$set('showDeleteModal', false)" type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Project Detail Modal -->
    @if($showDetailModal && $selectedProject)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Project Details</h3>
                                <p class="text-sm text-gray-500">Project ID: #{{ $selectedProject->project_id }}</p>
                            </div>
                            <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
    
                    <div class="px-6 py-4">
                        <!-- Project Main Info Card -->
                        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $selectedProject->project_header }}</h4>
                                    <p class="mt-1 text-sm text-gray-600">{{ $selectedProject->project_detail }}</p>
                                    <div class="mt-4 flex items-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            {{ $this->getProjectStatus($selectedProject)['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 
                                               ($this->getProjectStatus($selectedProject)['status'] === 'In Progress' ? 'bg-blue-100 text-blue-800' : 
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ $this->getProjectStatus($selectedProject)['status'] }}
                                        </span>
                                        <span class="ml-3 text-sm text-gray-500">
                                            {{ $this->getProjectStatus($selectedProject)['days_remaining'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Project Value</div>
                                    <div class="text-2xl font-bold text-blue-600">
                                        Rp {{ number_format($selectedProject->project_value, 0, ',', '.') }}
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-sm text-gray-500">Duration</div>
                                        <div class="text-sm font-medium text-green-600">
                                            Start: {{ Carbon\Carbon::parse($selectedProject->project_duration_start)->format('d M Y') }}
                                        </div>
                                        <div class="text-sm font-medium text-red-600">
                                            End: {{ Carbon\Carbon::parse($selectedProject->project_duration_end)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Stakeholders Info -->
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <!-- Vendor Info -->
                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Vendor Information</h4>
                                        <p class="mt-1 text-base font-semibold">{{ $selectedProject->vendor->vendor_name }}</p>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $selectedProject->vendor->vendor_email }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                {{ $selectedProject->vendor->vendor_phone }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $selectedProject->vendor->vendor_address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Customer Info -->
                            <div class="bg-white rounded-lg shadow-sm border p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Customer Information</h4>
                                        <p class="mt-1 text-base font-semibold">{{ $selectedProject->customer->customer_name }}</p>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $selectedProject->customer->customer_email }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                {{ $selectedProject->customer->customer_phone }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ $selectedProject->customer->customer_address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Products Section -->
                        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-4">Products & Materials</h4>
                            <div class="overflow-hidden ring-1 ring-black ring-opacity-5 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Category</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Price</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Quantity</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($selectedProject->products as $product)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $product->product_name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $product->product_category }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right">
                                                Rp {{ number_format($product->pivot->price_at_time, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $product->pivot->quantity }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">
                                                Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <th colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Total</th>
                                            <th class="px-4 py-3 text-sm font-medium text-gray-900 text-right">
                                                Rp {{ number_format($selectedProject->project_value, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
    
                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t text-right">
                        <button wire:click="closeDetailModal" 
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
   
    {{-- @if ($showDetailModal && $selectedProject)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl">
                        <div class="px-6 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Project Details</h3>
                                <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-4">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Project Info -->
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Project Header</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $selectedProject->project_header }}
                                        </p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Project Value</h4>
                                        <p class="mt-1 text-sm text-gray-900">Rp
                                            {{ number_format($selectedProject->project_value, 0, ',', '.') }}</p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Duration</h4>
                                        <p class="mt-1 text-sm text-green-600">Start:
                                            {{ Carbon\Carbon::parse($selectedProject->project_duration_start)->format('d M Y') }}
                                        </p>
                                        <p class="mt-1 text-sm text-red-600">End:
                                            {{ Carbon\Carbon::parse($selectedProject->project_duration_end)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Client Info -->
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Vendor</h4>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ $selectedProject->vendor->vendor_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $selectedProject->vendor->vendor_email }}
                                        </p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Customer</h4>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ $selectedProject->customer->customer_name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $selectedProject->customer->customer_email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500">Products</h4>
                                <div class="mt-2">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="space-y-2">
                                            @foreach ($selectedProject->products as $product)
                                                <div class="flex justify-between items-center">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $product->product_name }}</p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ $product->pivot->quantity }} x Rp
                                                            {{ number_format($product->pivot->price_at_time, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Project Details -->
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-500">Project Details</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $selectedProject->project_detail }}</p>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t">
                            <div class="flex justify-end">
                                <button wire:click="closeDetailModal"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}
    <!-- Flash Message -->
    <div x-data="{ show: false, message: '' }"
        x-on:project-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        x-on:project-deleted.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        class="fixed bottom-0 right-0 m-6">
        <div x-show="show" x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-2 opacity-0"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span x-text="message" class="block sm:inline"></span>
        </div>
    </div>
</div>
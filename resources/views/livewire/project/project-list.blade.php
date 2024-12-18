<div class="p-6">
    <!-- Header Section -->
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
                    <th wire:click="sortBy('project_header')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Project
                        @if ($sortField === 'project_header')
                            @if ($sortDirection === 'asc')
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7"></path>
                                </svg>
                            @else
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client
                        Info</th>
                    <th wire:click="sortBy('project_duration_start')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Timeline
                        @if ($sortField === 'project_duration_start')
                            @if ($sortDirection === 'asc')
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7"></path>
                                </svg>
                            @else
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress
                    </th>
                    <th wire:click="sortBy('project_value')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        Value
                        @if ($sortField === 'project_value')
                            @if ($sortDirection === 'asc')
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7"></path>
                                </svg>
                            @else
                                <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($projects as $project)
                    @php
                        $status = $this->getProjectStatus($project);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $project->project_header }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($project->project_detail, 50) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $project->customer->customer_name }}</div>
                                <div class="text-gray-500">{{ $project->vendor->vendor_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div>{{ Carbon\Carbon::parse($project->project_duration_start)->format('d M Y') }}</div>
                                <div>{{ Carbon\Carbon::parse($project->project_duration_end)->format('d M Y') }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-{{ $status['color'] }}-600 h-2.5 rounded-full transition-all duration-500" 
                                             style="width: {{ $status['progress'] }}%">
                                        </div>
                                    </div>
                                    <span class="ml-2 text-sm font-medium text-gray-900">
                                        {{ $status['progress'] }}%
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <div class="flex items-center space-x-2">
                                        <select wire:model.live="selectedStatus.{{ $project->project_id }}" 
                                                class="rounded-md border-gray-300 text-sm
                                                @if($this->getProjectStatus($project)['status'] === 'Completed') bg-green-100 text-green-800
                                                @elseif($this->getProjectStatus($project)['status'] === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($this->getProjectStatus($project)['status'] === 'In Progress') bg-blue-100 text-blue-800
                                                @endif">
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        
                                        <!-- Pending Duration Input -->
                                        @if($this->getProjectStatus($project)['status'] === 'Pending')
                                        <div class="flex items-center space-x-2">
                                            <input type="number" 
                                                   wire:model.live="pendingDuration.{{ $project->project_id }}"
                                                   class="w-20 rounded-md border-gray-300 text-sm"
                                                   placeholder="Days"
                                                   min="1">
                                            <span class="text-sm text-gray-500">days</span>
                                        </div>
                                        @endif
                                    </div>
                                
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="h-2 rounded-full transition-all duration-500
                                            @if($this->getProjectStatus($project)['status'] === 'Completed') bg-green-600
                                            @elseif($this->getProjectStatus($project)['status'] === 'Pending') bg-yellow-600
                                            @else bg-blue-600
                                            @endif"
                                            style="width: {{ $this->getProjectStatus($project)['progress'] }}%">
                                        </div>
                                    </div>
                                    
                                    <!-- Status Text -->
                                    <div class="text-sm mt-1
                                        @if($this->getProjectStatus($project)['status'] === 'Completed') text-green-600
                                        @elseif($this->getProjectStatus($project)['status'] === 'Pending') text-yellow-600
                                        @else text-gray-600
                                        @endif">
                                        {{ $this->getProjectStatus($project)['days_remaining'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                Rp {{ number_format($project->project_value, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="showDetail({{ $project->project_id }})"
                                class="text-blue-600 hover:text-blue-900">View</button>
                            <button wire:click="edit({{ $project->project_id }})"
                                class="ml-3 text-green-600 hover:text-green-900">Edit</button>
                            <button wire:click="confirmDelete({{ $project->project_id }})"
                                class="ml-3 text-red-600 hover:text-red-900">Delete</button>
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
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
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
                                            <span class="text-sm text-red-600">{{ $message }}</span>
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
                                            <span class="text-sm text-red-600">{{ $message }}</span>
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
                                        @error('selectedProducts')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Products Value -->
                                    <div class="mt-4">
                                        <div class="text-sm font-medium text-gray-700">Products Value</div>
                                        <div class="text-lg font-semibold">
                                            Rp {{ number_format($total_products, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Project Value Input -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700">Project Value</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" wire:model.live="project_value"
                                                class="mt-1 block w-full pl-12 rounded-md border-gray-300"
                                                placeholder="0">
                                        </div>
                                        @error('project_value')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Total Project Value -->
                                    <div class="mt-4">
                                        <div class="text-sm font-medium text-gray-700">Total Project Value</div>
                                        <div class="text-xl font-bold text-blue-600">
                                            Rp {{ number_format($total_value, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            (Products Value + Project Value)
                                        </div>
                                    </div>

                                    <!-- Project Header Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Project Header</label>
                                        <input type="text" wire:model="project_header"
                                            class="mt-1 block w-full rounded-md border-gray-300">
                                        @error('project_header')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Project Duration Fields -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                            <input type="date" wire:model="project_duration_start"
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            @error('project_duration_start')
                                                <span class="text-sm text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                                            <input type="date" wire:model="project_duration_end"
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            @error('project_duration_end')
                                                <span class="text-sm text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Project Detail Field -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Project Detail</label>
                                        <textarea wire:model="project_detail" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                        @error('project_detail')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto">
                                    {{ $editMode ? 'Update Project' : 'Create Project' }}
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
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
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
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
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
    @if ($showDetailModal && $selectedProject)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-4xl">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Project Details</h3>
                                <button wire:click="$set('showDetailModal', false)"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Project Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Project Header</h4>
                                        <p class="mt-1 text-sm text-gray-900">{{ $selectedProject->project_header }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Project Value</h4>
                                        <p class="mt-1 text-sm text-gray-900">
                                            Rp {{ number_format($selectedProject->project_value, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Products -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Products</h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">
                                                        Product</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">
                                                        Price</th>
                                                    <th
                                                        class="px-4 py-2 text-center text-xs font-medium text-gray-500">
                                                        Quantity</th>
                                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">
                                                        Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach ($selectedProject->products as $product)
                                                    <tr>
                                                        <td class="px-4 py-2 text-sm text-gray-900">
                                                            {{ $product->product_name }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 text-right">
                                                            Rp
                                                            {{ number_format($product->pivot->price_at_time, 0, ',', '.') }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 text-center">
                                                            {{ $product->pivot->quantity }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-900 text-right">
                                                            Rp
                                                            {{ number_format($product->pivot->subtotal, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Start Date</h4>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ Carbon\Carbon::parse($selectedProject->project_duration_start)->format('d M Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">End Date</h4>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ Carbon\Carbon::parse($selectedProject->project_duration_end)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Project Details -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Project Detail</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $selectedProject->project_detail }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" wire:click="$set('showDetailModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    <div x-data="{ show: false, message: '' }"
        x-on:project-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        x-on:project-deleted.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        class="fixed bottom-4 right-4 z-50">
        <div x-show="show" x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-2 opacity-0"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span x-text="message"></span>
        </div>
    </div>

</div>

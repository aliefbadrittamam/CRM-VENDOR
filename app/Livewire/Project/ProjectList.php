<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectList extends Component
{
    use WithPagination;

    // Properties untuk form
    public $vendor_id;
    public $customer_id;
    public $project_header;
    public $project_value = 0;
    public $project_duration_start;
    public $project_duration_end;
    public $project_detail;

    // Properties untuk filter dan pencarian
    public $search = '';
    public $vendorFilter = '';
    public $customerFilter = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Properties untuk modal dan state
    public $showModal = false;
    public $showDeleteModal = false;
    public $showDetailModal = false;
    public $editMode = false;
    public $project_id;
    public $selectedProject = null;

    // Properties untuk multiple products
    public $selectedProducts = [];
    public $quantities = [];
    public $productPrices = [];
    public $productSubtotals = [];
    public $total = 0;

    protected $queryString = [
        'search',
        'vendorFilter',
        'customerFilter',
        'statusFilter',
        'dateFilter'
    ];

    public function rules()
    {
        return [
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'project_header' => 'required|string|max:100',
            'project_duration_start' => 'required|date',
            'project_duration_end' => 'required|date|after:project_duration_start',
            'project_detail' => 'required|string',
            'selectedProducts' => 'required|array|min:1',
        ];
    }

    protected $messages = [
        'vendor_id.required' => 'Please select a vendor',
        'customer_id.required' => 'Please select a customer',
        'project_header.required' => 'Project header is required',
        'project_duration_start.required' => 'Start date is required',
        'project_duration_end.required' => 'End date is required',
        'project_duration_end.after' => 'End date must be after start date',
        'project_detail.required' => 'Project detail is required',
        'selectedProducts.required' => 'Please select at least one product',
        'selectedProducts.min' => 'Please select at least one product',
    ];

    public function mount()
    {
        $this->project_duration_start = now()->format('Y-m-d');
        $this->project_duration_end = now()->addMonths(1)->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSelectedProducts($value, $productId)
    {
        if ($value) {
            if (!isset($this->quantities[$productId])) {
                $this->quantities[$productId] = 1;
                $product = Product::find($productId);
                if ($product) {
                    $this->productPrices[$productId] = $product->product_price;
                    $this->calculateSubtotal($productId);
                }
            }
        } else {
            unset($this->quantities[$productId]);
            unset($this->productPrices[$productId]);
            unset($this->productSubtotals[$productId]);
        }
        $this->calculateTotal();
    }

    public function updatedQuantities($value, $productId)
    {
        if (isset($this->productPrices[$productId])) {
            $this->calculateSubtotal($productId);
            $this->calculateTotal();
        }
    }

    public function calculateSubtotal($productId)
    {
        if (isset($this->quantities[$productId]) && isset($this->productPrices[$productId])) {
            $this->productSubtotals[$productId] = $this->productPrices[$productId] * $this->quantities[$productId];
        }
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->productSubtotals as $subtotal) {
            $this->total += $subtotal;
        }
        $this->project_value = $this->total;
    }

    public function getProjectStatus($project)
    {
        $startDate = Carbon::parse($project->project_duration_start);
        $endDate = Carbon::parse($project->project_duration_end);
        $today = now();

        if ($today < $startDate) {
            return [
                'status' => 'Not Started',
                'color' => 'gray',
                'days_remaining' => $today->diffInDays($startDate) . ' days until start'
            ];
        }

        if ($today > $endDate) {
            $overdue = $today->diffInDays($endDate);
            return [
                'status' => 'Completed',
                'color' => 'green',
                'days_remaining' => 'Completed ' . $overdue . ' days ago'
            ];
        }

        // Project in progress
        $totalDays = $startDate->diffInDays($endDate) ?: 1;
        $daysLeft = $today->diffInDays($endDate);
        $progress = (($totalDays - $daysLeft) / $totalDays) * 100;

        return [
            'status' => 'In Progress',
            'color' => 'blue',
            'days_remaining' => $daysLeft . ' days remaining',
            'progress' => $progress
        ];
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->project_id = $id;

        $project = Project::with('products')->findOrFail($id);

        $this->vendor_id = $project->vendor_id;
        $this->customer_id = $project->customer_id;
        $this->project_header = $project->project_header;
        $this->project_value = $project->project_value;
        $this->project_duration_start = $project->project_duration_start->format('Y-m-d');
        $this->project_duration_end = $project->project_duration_end->format('Y-m-d');
        $this->project_detail = $project->project_detail;

        // Load existing products
        foreach ($project->products as $product) {
            $this->selectedProducts[$product->product_id] = true;
            $this->quantities[$product->product_id] = $product->pivot->quantity;
            $this->productPrices[$product->product_id] = $product->pivot->price_at_time;
            $this->productSubtotals[$product->product_id] = $product->pivot->subtotal;
        }

        $this->calculateTotal();
        $this->showModal = true;
    }

    public function showDetail($id)
    {
        $this->selectedProject = Project::with(['vendor', 'customer', 'products'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function confirmDelete($id)
    {
        $this->project_id = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $project = Project::findOrFail($this->project_id);
            $project->products()->detach();
            $project->delete();

            DB::commit();

            $this->showDeleteModal = false;
            $this->dispatch('project-deleted', 'Project deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error deleting project: ' . $e->getMessage());
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $projectData = [
                'vendor_id' => $this->vendor_id,
                'customer_id' => $this->customer_id,
                'project_header' => $this->project_header,
                'project_value' => $this->total,
                'project_duration_start' => $this->project_duration_start,
                'project_duration_end' => $this->project_duration_end,
                'project_detail' => $this->project_detail,
            ];

            if ($this->editMode) {
                $project = Project::findOrFail($this->project_id);
                $project->update($projectData);
                $project->products()->detach();
            } else {
                $project = Project::create($projectData);
            }

            foreach ($this->selectedProducts as $productId => $selected) {
                if ($selected) {
                    $project->products()->attach($productId, [
                        'quantity' => $this->quantities[$productId],
                        'price_at_time' => $this->productPrices[$productId],
                        'subtotal' => $this->productSubtotals[$productId]
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('project-saved', $this->editMode ? 'Project updated successfully!' : 'Project created successfully!');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving project: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'vendor_id',
            'customer_id',
            'project_header',
            'project_value',
            'project_detail',
            'selectedProducts',
            'quantities',
            'productPrices',
            'productSubtotals',
            'total',
            'editMode',
            'project_id'
        ]);

        $this->project_duration_start = now()->format('Y-m-d');
        $this->project_duration_end = now()->addMonths(1)->format('Y-m-d');
    }

    public function render()
    {
        $query = Project::with(['vendor', 'customer', 'products'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('project_header', 'like', '%' . $this->search . '%')
                          ->orWhere('project_detail', 'like', '%' . $this->search . '%')
                          ->orWhereHas('vendor', function($q) {
                              $q->where('vendor_name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('customer', function($q) {
                              $q->where('customer_name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->vendorFilter, function($q) {
                $q->where('vendor_id', $this->vendorFilter);
            })
            ->when($this->customerFilter, function($q) {
                $q->where('customer_id', $this->customerFilter);
            })
            ->when($this->dateFilter, function($q) {
                $q->whereDate('project_duration_start', '<=', $this->dateFilter)
                  ->whereDate('project_duration_end', '>=', $this->dateFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.project.project-list', [
            'projects' => $query->paginate(10),
            'vendors' => Vendor::orderBy('vendor_name')->get(),
            'customers' => Customer::orderBy('customer_name')->get(),
            'products' => Product::orderBy('product_name')->get()
        ]);
    }
}
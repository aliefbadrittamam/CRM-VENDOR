<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectList extends Component
{
    use WithPagination;

    // Properties untuk form
    public $vendor_id;
    public $customer_id;
    public $product_id;
    public $project_header;
    public $project_value;
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

    protected $queryString = ['search', 'vendorFilter', 'customerFilter', 'statusFilter', 'dateFilter'];

    protected $rules = [
        'vendor_id' => 'required|exists:vendors,vendor_id',
        'customer_id' => 'required|exists:customers,customer_id',
        'product_id' => 'required|exists:products,product_id',
        'project_header' => 'required|string|max:100',
        'project_value' => 'required|numeric|min:0',
        'project_duration_start' => 'required|date',
        'project_duration_end' => 'required|date|after:project_duration_start',
        'project_detail' => 'required|string'
    ];

    protected $messages = [
        'vendor_id.required' => 'Please select a vendor',
        'customer_id.required' => 'Please select a customer',
        'product_id.required' => 'Please select a product',
        'project_header.required' => 'Project header is required',
        'project_value.required' => 'Project value is required',
        'project_duration_start.required' => 'Start date is required',
        'project_duration_end.required' => 'End date is required',
        'project_duration_end.after' => 'End date must be after start date',
        'project_detail.required' => 'Project detail is required'
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
        } elseif ($today > $endDate) {
            $delay = $endDate->diffInDays($today);
            return [
                'status' => 'Completed',
                'color' => $delay > 0 ? 'red' : 'green',
                'days_remaining' => $delay > 0 ? $delay . ' days overdue' : 'Completed on time'
            ];
        } else {
            $totalDays = $startDate->diffInDays($endDate) ?: 1;
            $elapsedDays = $startDate->diffInDays($today);
            $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
            $daysLeft = $today->diffInDays($endDate);

            return [
                'status' => 'In Progress',
                'color' => $progress >= ($elapsedDays / $totalDays) * 100 ? 'blue' : 'yellow',
                'days_remaining' => $daysLeft . ' days remaining',
                'progress' => $progress
            ];
        }
    }

    public function getProjectValue($value)
    {
        return number_format($value, 0, ',', '.');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetExcept(['search', 'vendorFilter', 'customerFilter', 'statusFilter', 'dateFilter']);
        $this->project_duration_start = now()->format('Y-m-d');
        $this->project_duration_end = now()->addMonths(1)->format('Y-m-d');
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

        $project = Project::findOrFail($id);
        
        $this->vendor_id = $project->vendor_id;
        $this->customer_id = $project->customer_id;
        $this->product_id = $project->product_id;
        $this->project_header = $project->project_header;
        $this->project_value = $project->project_value;
        $this->project_duration_start = $project->project_duration_start->format('Y-m-d');
        $this->project_duration_end = $project->project_duration_end->format('Y-m-d');
        $this->project_detail = $project->project_detail;

        $this->showModal = true;
    }

    public function showDetail($id)
    {
        $this->selectedProject = Project::with(['vendor', 'customer', 'product'])->findOrFail($id);
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

            $data = [
                'vendor_id' => $this->vendor_id,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'project_header' => $this->project_header,
                'project_value' => $this->project_value,
                'project_duration_start' => $this->project_duration_start,
                'project_duration_end' => $this->project_duration_end,
                'project_detail' => $this->project_detail
            ];

            if ($this->editMode) {
                $project = Project::findOrFail($this->project_id);
                $project->update($data);
                $message = 'Project updated successfully!';
            } else {
                Project::create($data);
                $message = 'Project created successfully!';
            }

            DB::commit();

            $this->showModal = false;
            $this->resetForm();
            $this->dispatch('project-saved', $message);

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
            'product_id',
            'project_header',
            'project_value',
            'project_detail',
            'editMode',
            'project_id'
        ]);
        $this->project_duration_start = now()->format('Y-m-d');
        $this->project_duration_end = now()->addMonths(1)->format('Y-m-d');
    }

    public function render()
    {
        $query = Project::with(['vendor', 'customer', 'product'])
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
            ->when($this->statusFilter, function($q) {
                $today = now();
                switch($this->statusFilter) {
                    case 'not_started':
                        $q->where('project_duration_start', '>', $today);
                        break;
                    case 'in_progress':
                        $q->where('project_duration_start', '<=', $today)
                          ->where('project_duration_end', '>=', $today);
                        break;
                    case 'completed':
                        $q->where('project_duration_end', '<', $today);
                        break;
                }
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
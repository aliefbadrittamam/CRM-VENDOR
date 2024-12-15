<?php

namespace App\Livewire\Project;

use App\Models\Project;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProjectListVendor extends Component
{
    use WithPagination;

    public $showDetailModal = false;
    public $selectedProject = null;
    public $search = '';
    public $statusFilter = '';
    public $customerFilter = '';
    public $dateFilter = '';
    public $sortField = 'project_duration_start';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search',
        'statusFilter',
        'customerFilter',
        'dateFilter'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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
                'progress' => 0
            ];
        }

        if ($today > $endDate) {
            return [
                'status' => 'Completed',
                'color' => 'green',
                'progress' => 100
            ];
        }

        // Calculate progress for ongoing projects
        $totalDays = $startDate->diffInDays($endDate) ?: 1;
        $daysCompleted = $startDate->diffInDays($today);
        $progress = min(100, round(($daysCompleted / $totalDays) * 100));

        return [
            'status' => 'In Progress',
            'color' => $progress >= 70 ? 'blue' : 'yellow',
            'progress' => $progress
        ];
    }

    public function viewDetail($projectId)
    {
        $this->selectedProject = Project::with([
            'customer',
            'products',
            'priceQuotations'
        ])->findOrFail($projectId);

        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedProject = null;
    }

    public function render()
    {
        $vendor = Vendor::where('user_id', Auth::id())->first();

        if (!$vendor) {
            return view('livewire.project.project-list-vendor', [
                'projects' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10), // Empty paginator
                'totalValue' => 0,
                'activeProjects' => 0,
                'completedProjects' => 0
            ]);
        }

        $query = Project::where('vendor_id', $vendor->vendor_id)
            ->with(['customer', 'products'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('project_header', 'like', '%' . $this->search . '%')
                          ->orWhere('project_detail', 'like', '%' . $this->search . '%')
                          ->orWhereHas('customer', function($q) {
                              $q->where('customer_name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->customerFilter, function($q) {
                $q->where('customer_id', $this->customerFilter);
            })
            ->when($this->dateFilter, function($q) {
                $date = Carbon::parse($this->dateFilter);
                $q->whereDate('project_duration_start', '<=', $date)
                  ->whereDate('project_duration_end', '>=', $date);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $projects = $query->paginate(10);

        // Calculate metrics
        $allProjects = Project::where('vendor_id', $vendor->vendor_id);
        $totalValue = $allProjects->sum('project_value');
        $activeProjects = $allProjects
            ->whereDate('project_duration_start', '<=', now())
            ->whereDate('project_duration_end', '>=', now())
            ->count();
        $completedProjects = $allProjects
            ->whereDate('project_duration_end', '<', now())
            ->count();

        return view('livewire.project.project-list-vendor', [
            'projects' => $projects,
            'totalValue' => $totalValue,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects
        ]);
    }
}
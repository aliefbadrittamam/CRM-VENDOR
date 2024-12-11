<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Customer;
use Illuminate\Support\Carbon;

class ProjectStatus extends Component
{
    use WithPagination;

    public $search = '';
    public $vendorFilter = '';
    public $customerFilter = '';
    public $statusFilter = '';
    public $dateRangeFilter = '';
    public $sortField = 'project_duration_start';
    public $sortDirection = 'asc';
    public $selectedProject = null;
    public $showDetailModal = false;

    protected $queryString = ['search', 'vendorFilter', 'customerFilter', 'statusFilter', 'dateRangeFilter'];

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

    public function showProjectDetail($projectId)
    {
        $this->selectedProject = Project::with(['vendor', 'customer'])
            ->findOrFail($projectId);
        $this->showDetailModal = true;
    }

    public function getProjectStatus($project)
    {
        $startDate = Carbon::parse($project->project_duration_start);
        $endDate = Carbon::parse($project->project_duration_end);
        $today = now();

        $totalDays = $startDate->diffInDays($endDate) ?: 1;
        $elapsedDays = $startDate->diffInDays($today);
        $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));

        if ($today < $startDate) {
            return [
                'status' => 'Not Started',
                'color' => 'gray',
                'progress' => 0,
                'days_remaining' => $today->diffInDays($startDate) . ' days until start',
                'badge_color' => 'bg-gray-100 text-gray-800'
            ];
        } elseif ($today > $endDate) {
            $delay = $endDate->diffInDays($today);
            return [
                'status' => 'Completed',
                'color' => $today->diffInDays($endDate) > 0 ? 'red' : 'green',
                'progress' => 100,
                'days_remaining' => $delay > 0 ? $delay . ' days overdue' : 'On time completion',
                'badge_color' => $delay > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
            ];
        } else {
            $daysLeft = $today->diffInDays($endDate);
            $isOnTrack = ($progress >= ($elapsedDays / $totalDays) * 100);
            return [
                'status' => 'In Progress',
                'color' => $isOnTrack ? 'blue' : 'yellow',
                'progress' => $progress,
                'days_remaining' => $daysLeft . ' days remaining',
                'badge_color' => $isOnTrack ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'
            ];
        }
    }

    public function getProjectMetrics()
    {
        $projects = Project::all();
        $totalProjects = $projects->count();
        $notStarted = 0;
        $inProgress = 0;
        $completed = 0;
        $onTrack = 0;
        $delayed = 0;
        $totalValue = 0;

        foreach ($projects as $project) {
            $status = $this->getProjectStatus($project);
            
            switch ($status['status']) {
                case 'Not Started':
                    $notStarted++;
                    break;
                case 'In Progress':
                    $inProgress++;
                    if ($status['color'] === 'blue') {
                        $onTrack++;
                    } else {
                        $delayed++;
                    }
                    break;
                case 'Completed':
                    $completed++;
                    if ($status['color'] === 'red') {
                        $delayed++;
                    }
                    break;
            }

            $totalValue += $project->project_value;
        }

        return [
            'total' => $totalProjects,
            'not_started' => $notStarted,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'on_track' => $onTrack,
            'delayed' => $delayed,
            'total_value' => $totalValue,
            'completion_rate' => $totalProjects > 0 ? ($completed / $totalProjects) * 100 : 0,
            'on_track_rate' => $inProgress > 0 ? ($onTrack / $inProgress) * 100 : 0
        ];
    }

    public function getDaysRemaining($project)
    {
        $endDate = Carbon::parse($project->project_duration_end);
        $today = now();
        $days = $today->diffInDays($endDate);

        if ($today > $endDate) {
            return ['text' => $days . ' days overdue', 'class' => 'text-red-600'];
        }
        
        if ($days <= 7) {
            return ['text' => $days . ' days left', 'class' => 'text-yellow-600'];
        }
        
        return ['text' => $days . ' days remaining', 'class' => 'text-green-600'];
    }

    public function render()
    {
        $query = Project::with(['vendor', 'customer'])
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
                    case 'delayed':
                        $q->where('project_duration_end', '<', $today);
                        break;
                }
            })
            ->when($this->dateRangeFilter, function($q) {
                [$start, $end] = explode(' to ', $this->dateRangeFilter . ' to ');
                if ($start && $end) {
                    $q->whereBetween('project_duration_start', [$start, $end])
                      ->orWhereBetween('project_duration_end', [$start, $end]);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return view('livewire.project.project-status', [
            'projects' => $query->paginate(10),
            'vendors' => Vendor::orderBy('vendor_name')->get(),
            'customers' => Customer::orderBy('customer_name')->get(),
            'metrics' => $this->getProjectMetrics()
        ]);
    }
}
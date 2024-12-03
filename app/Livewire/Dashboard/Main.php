<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Customer; // Tambahkan ini
use App\Models\CustomerInteraction;
use App\Models\Sale;
use Carbon\Carbon; // Tambahkan ini juga untuk handling date
class Main extends Component
{
    public $startDate;
    public $endDate;
    public $selectedVendor = '';
    public $selectedCustomer = '';
    public $dateRange = 'this_month'; // default filter
    
    public function mount()
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now()->endOfMonth();
    }
    
    public function updatedDateRange($value)
    {
        switch($value) {
            case 'today':
                $this->startDate = now()->startOfDay();
                $this->endDate = now()->endOfDay();
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek();
                $this->endDate = now()->endOfWeek();
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth();
                $this->endDate = now()->endOfMonth();
                break;
            case 'this_year':
                $this->startDate = now()->startOfYear();
                $this->endDate = now()->endOfYear();
                break;
        }
    }
    
    public function getChartData()
    {
        $projects = Project::when($this->selectedVendor, function($query) {
                return $query->where('vendor_id', $this->selectedVendor);
            })
            ->when($this->selectedCustomer, function($query) {
                return $query->where('customer_id', $this->selectedCustomer);
            })
            ->whereBetween('project_duration_start', [$this->startDate, $this->endDate])
            ->selectRaw('MONTH(project_duration_start) as month_num, DATE_FORMAT(project_duration_start, "%b") as month, COUNT(*) as count')
            ->groupBy('month_num', 'month')
            ->orderBy('month_num')
            ->get();
    
            $revenue = Project::selectRaw('MONTH(project_duration_start) as month_num, DATE_FORMAT(project_duration_start, "%b") as month, SUM(project_value) as total')
            ->groupBy('month_num', 'month')
            ->orderBy('month_num')
            ->get();
    
        return [
            'projectLabels' => $projects->pluck('month'),
            'projectData' => $projects->pluck('count'),
            'revenueLabels' => $revenue->pluck('month'),
            'revenueData' => $revenue->pluck('total')
        ];
    }
    
    public function render()
    {
        $chartData = $this->getChartData();
        
        return view('livewire.dashboard.main', [
            'totalProjects' => Project::whereBetween('project_duration_start', [$this->startDate, $this->endDate])->count(),
            'totalVendors' => Vendor::count(),
            'revenue' => Project::whereBetween('project_duration_start', [$this->startDate, $this->endDate])->sum('project_value'),
            'pendingProjects' => Project::whereDate('project_duration_start', '>', now())->count(),
            'recentProjects' => Project::with(['vendor', 'customer'])
                ->orderBy('project_duration_start', 'desc')
                ->take(5)
                ->get(),
            'recentInteractions' => CustomerInteraction::with(['customer'])
                ->orderBy('interaction_date', 'desc')
                ->take(5)
                ->get(),
            'vendors' => Vendor::all(),
            'customers' => Customer::all(),
            'projectLabels' => $chartData['projectLabels'],
            'projectData' => $chartData['projectData'],
            'revenueLabels' => $chartData['revenueLabels'],
            'revenueData' => $chartData['revenueData']
        ]);
    }
    
    
}

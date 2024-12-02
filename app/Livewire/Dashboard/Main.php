<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\CustomerInteraction;
use App\Models\Sale;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectExport;

class Main extends Component
{
        public function getChartData()
{
    $projects = Project::selectRaw('MONTH(project_duration_start) as month_num, DATE_FORMAT(project_duration_start, "%b") as month, COUNT(*) as count')
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
            'totalProjects' => Project::count(),
            'totalVendors' => Vendor::count(),
            'revenue' => Project::sum('project_value'),
            'pendingProjects' => Project::whereDate('project_duration_start', '>', now())->count(),
            'recentProjects' => Project::with(['vendor', 'customer'])
                ->orderBy('project_duration_start', 'desc')
                ->take(5)
                ->get(),
            'recentInteractions' => CustomerInteraction::with(['customer'])
                ->orderBy('interaction_date', 'desc')
                ->take(5)
                ->get(),
            'projectLabels' => $chartData['projectLabels'],
            'projectData' => $chartData['projectData'],
            'revenueLabels' => $chartData['revenueLabels'],
            'revenueData' => $chartData['revenueData']
        ]);
    }

    public $pollInterval = 30000; // 30 detik

    protected $listeners = ['echo:projects,ProjectCreated' => '$refresh'];

    public function getListeners()
    {
        return [
            'echo:projects,ProjectCreated' => '$refresh',
            $this->pollInterval => 'pollData'
        ];
    }

    public function pollData()
    {
        $this->emit('refreshCharts');
    }

    
}

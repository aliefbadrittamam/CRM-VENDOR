<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\CustomerInteraction;
use App\Models\Project;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class Segmentation extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Properties untuk filter dan analisis
    public $dateRange = '30'; // default 30 hari
    public $interactionType = 'all';
    public $minimumInteractions = 0;

    public function getSegmentationData()
    {
        // Mengambil data customer dengan berbagai metrik
        return Customer::select([
            'customers.*',
            DB::raw('COUNT(DISTINCT ci.interaction_id) as interaction_count'),
            DB::raw('COUNT(DISTINCT p.project_id) as project_count'),
            DB::raw('COALESCE(SUM(s.fixed_amount), 0) as total_sales')
        ])
        ->leftJoin('customer_interactions as ci', function($join) {
            $join->on('customers.customer_id', '=', 'ci.customer_id')
                 ->when($this->dateRange != 'all', function($query) {
                     return $query->where('ci.interaction_date', '>=', 
                         now()->subDays($this->dateRange));
                 });
        })
        ->leftJoin('projects as p', 'customers.customer_id', '=', 'p.customer_id')
        ->leftJoin('sales as s', 'customers.customer_id', '=', 's.customer_id')
        ->groupBy('customers.customer_id')
        ->having('interaction_count', '>=', $this->minimumInteractions)
        ->when($this->interactionType != 'all', function($query) {
            return $query->whereExists(function($subquery) {
                $subquery->from('customer_interactions')
                    ->whereColumn('customer_interactions.customer_id', 'customers.customer_id')
                    ->where('interaction_type', $this->interactionType);
            });
        })
        ->orderBy('interaction_count', 'desc')
        ->paginate(10);
    }

    public function getSegmentLabel($metrics)
    {
        // Logic untuk menentukan segment berdasarkan metrik
        if ($metrics['interaction_count'] >= 10 && $metrics['project_count'] >= 2) {
            return ['name' => 'Premium', 'color' => 'bg-purple-100 text-purple-800'];
        } elseif ($metrics['interaction_count'] >= 5 || $metrics['project_count'] >= 1) {
            return ['name' => 'Active', 'color' => 'bg-green-100 text-green-800'];
        } else {
            return ['name' => 'Regular', 'color' => 'bg-gray-100 text-gray-800'];
        }
    }

    public function exportSegmentation()
    {
        // Logic untuk export data
    }

    public function render()
    {
        $customers = $this->getSegmentationData();
        
        // Hitung statistik
        $statistics = [
            'total_customers' => Customer::count(),
            'active_customers' => CustomerInteraction::where('interaction_date', '>=', 
                now()->subDays(30))->distinct('customer_id')->count(),
            'total_interactions' => CustomerInteraction::count(),
        ];

        return view('livewire.customer.segmentation', [
            'customers' => $customers,
            'statistics' => $statistics
        ]);
    }
}
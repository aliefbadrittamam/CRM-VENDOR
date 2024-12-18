<?php

namespace App\Livewire\Marketing;
use App\Models\MarketingDetail;
use Livewire\WithPagination;

use Livewire\Component;


class MessageHistory extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filters = [
        'date_from' => '',
        'date_to' => '',
        'status'=>'',
    ];
  
    public $notification = [
        'show' => false,
        'message' => ''
    ];



    // Reset halaman saat pencarian berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reset halaman saat filter berubah
    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Validasi jika date_to lebih kecil dari date_from
        if (
            isset($this->filters['date_from'], $this->filters['date_to']) && 
            $this->filters['date_to'] < $this->filters['date_from']
        ) {
            // Simpan pesan kesalahan ke sesi untuk ditampilkan di view
            session()->flash('error', 'The "Date To" must be greater than or equal to "Date From".');
            
            // Return view dengan paginator kosong
            return view('livewire.marketing.main', [
                'campaign' => MarketingDetail::query()->paginate(1) 
            ]);
        }


        $campaign = MarketingDetail::query()
            ->when($this->search, function($query) {
                $query->where(function($query) {
                    $query->where('campaign_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filters['date_from'], function($query) {
                $query->whereDate('send_date', '>=', $this->filters['date_from']);
            })
            ->when($this->filters['status'], function($query){
                $query->where('status', 'like', $this->filter['status']);
            })            
            ->when($this->filters['date_to'], function($query) {
                $query->whereDate('send_date', '<=', $this->filters['date_to']);
            })            
            ->orderBy('send_date', 'desc')
            ->paginate(10);

        return view('livewire.marketing.message-history', ['campaigns' => $campaign]);
    }
}

<?php
// app/Livewire/Customer/Main.php
namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Main extends Component
{
    use WithPagination;
    
    public $search = '';
    public $filters = [
        'date_from' => '',
        'date_to' => ''
    ];
    
    public $showModal = false;
    public $editingCustomerId = null;
    public $notification = [
        'show' => false,
        'message' => ''
    ];

    protected $listeners = [
        'customerSaved' => 'handleCustomerSaved'
    ];

    public function openModal($customerId = null)
    {
        $this->editingCustomerId = $customerId;
        $this->showModal = true;
    }

    public function handleCloseModal()
    {
        $this->showModal = false;
        $this->editingCustomerId = null;
    }

    public function handleCustomerSaved($message)
    {
        $this->handleCloseModal();
        $this->notification['show'] = true;
        $this->notification['message'] = $message;
    }

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function($query) {
                $query->where(function($query) {
                    $query->where('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_email', 'like', "%{$this->search}%")
                        ->orWhere('customer_phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filters['date_from'], function($query) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            })
            ->when($this->filters['date_to'], function($query) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.customer.main', [
            'customers' => $customers
        ]);
    }
}
<?php
namespace App\Livewire\Customer;
use Illuminate\Support\Facades\Auth;

use App\Models\Customer;
use Livewire\Component;

class CustomerForm extends Component
{
    public $customerId = null;
    public $customer_name = '';
    public $customer_email = '';
    public $customer_phone = '';
    public $customer_address = '';

    protected $rules = [
        'customer_name' => 'required|min:3',
        'customer_email' => 'required|email',
        'customer_phone' => 'required',
        'customer_address' => 'required'
    ];

    public function mount($customerId = null)
    {
        if ($customerId) {
            $customer = Customer::find($customerId);
            $this->customerId = $customer->customer_id;
            $this->customer_name = $customer->customer_name;
            $this->customer_email = $customer->customer_email;
            $this->customer_phone = $customer->customer_phone;
            $this->customer_address = $customer->customer_address;
        }
    }

    public function closeModal()
    {
        $this->dispatch('closeModal');
    }

    

public function save()
{
    $this->validate();

    if ($this->customerId) {
        Customer::find($this->customerId)->update([
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
        ]);
        $message = 'Customer updated successfully!';
    } else {
        Customer::create([
            'user_id' => Auth::id(),
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
        ]);
        $message = 'Customer created successfully!';
    }

    $this->dispatch('customerSaved', $message);
}
    public function render()
    {
        return view('livewire.customer.customer-form');
    }
}
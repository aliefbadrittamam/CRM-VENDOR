<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\CustomerInteraction;
use Illuminate\Support\Facades\Auth;

class Interaction extends Component
{
    use WithPagination;

    // Property dasar yang dibutuhkan
    public $selectedCustomerId = null;
    public $selectedCustomerName = null;
    public $showModal = false;
    public $interaction_type = '';
    public $notes = '';
    public $editingInteractionId = null;

    // Aturan validasi dasar
    protected function rules()
    {
        return [
            'interaction_type' => 'required',
            'notes' => 'required'
        ];
    }

    // Method untuk memilih customer
    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->selectedCustomerId = $customer->customer_id;
            $this->selectedCustomerName = $customer->customer_name;
        }
    }

    // Method untuk membuka modal
    public function openModal($interactionId = null)
    {
        if ($interactionId) {
            $interaction = CustomerInteraction::find($interactionId);
            if ($interaction) {
                $this->editingInteractionId = $interactionId;
                $this->interaction_type = $interaction->interaction_type;
                $this->notes = $interaction->notes;
            }
        }
        $this->showModal = true;
    }

    // Method untuk menutup modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetExcept(['selectedCustomerId', 'selectedCustomerName']);
    }

    // Method untuk menyimpan data
    public function save()
    {
        $this->validate();

        try {
            if ($this->editingInteractionId) {
                // Update existing record
                $interaction = CustomerInteraction::find($this->editingInteractionId);
                if ($interaction) {
                    $interaction->update([
                        'interaction_type' => $this->interaction_type,
                        'notes' => $this->notes
                    ]);
                }
            } else {
                // Create new record
                CustomerInteraction::create([
                    'customer_id' => $this->selectedCustomerId,
                    'user_id' => Auth::id(),
                    'interaction_type' => $this->interaction_type,
                    'interaction_date' => now(),
                    'notes' => $this->notes
                ]);
            }

            // Reset form dan tutup modal
            $this->showModal = false;
            $this->interaction_type = '';
            $this->notes = '';
            $this->editingInteractionId = null;

        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            logger()->error($e->getMessage());
        }
    }

    // Method untuk menghapus interaksi
    public function deleteInteraction($id)
    {
        $interaction = CustomerInteraction::find($id);
        if ($interaction) {
            $interaction->delete();
        }
    }

    // Method render untuk menampilkan data
    public function render()
    {
        $customers = Customer::orderBy('customer_name')->get();
        
        $interactions = [];
        if ($this->selectedCustomerId) {
            $interactions = CustomerInteraction::where('customer_id', $this->selectedCustomerId)
                ->orderBy('interaction_date', 'desc')
                ->paginate(10);
        }

        return view('livewire.customer.interaction', [
            'customers' => $customers,
            'interactions' => $interactions
        ]);
    }
}
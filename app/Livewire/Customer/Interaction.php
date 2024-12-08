<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\CustomerInteraction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class Interaction extends Component
{
    use WithPagination;

    // Properties untuk data form
    public $selectedCustomerId = null;
    public $selectedCustomerName = null;
    public $showModal = false;
    public $interaction_type = '';
    public $notes = '';
    public $editingInteractionId = null;
    public $search = '';
    public $filters = [
        'date_from' => '',
        'date_to' => '',
        'type' => ''
    ];

    // Aturan validasi
    protected $rules = [
        'interaction_type' => 'required|in:Call,Email,Meeting,Other',
        'notes' => 'required|min:10'
    ];

    // Proses inisialisasi komponen
    public function mount()
    {
        // Jika ada customer ID di parameter URL, otomatis pilih customer tersebut
        if (request()->has('customerId')) {
            $this->selectCustomer(request()->get('customerId'));
        }
    }

    // Memilih customer untuk ditampilkan interaksinya
    public function selectCustomer($customerId)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            $this->selectedCustomerId = $customer->customer_id;
            $this->selectedCustomerName = $customer->customer_name;
            $this->resetPage(); // Reset pagination ketika ganti customer
            $this->resetFilters(); // Reset filter ketika ganti customer
        }
    }

    // Reset filter ke nilai default
    public function resetFilters()
    {
        $this->filters = [
            'date_from' => '',
            'date_to' => '',
            'type' => ''
        ];
    }

    // Membuka modal tambah/edit interaksi
    public function openModal($interactionId = null)
    {
        // Validasi apakah customer sudah dipilih
        if (!$this->selectedCustomerId) {
            $this->dispatch('showAlert', [
                'title' => 'Error!',
                'message' => 'Please select a customer first',
                'icon' => 'error',
                'timer' => 3000,
            ]);
            return;
        }

        // Jika mode edit, ambil data yang akan diedit
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

    // Menutup modal dan reset form
    public function closeModal()
{
    $this->showModal = false;
    $this->reset(['interaction_type', 'notes', 'editingInteractionId']);
    $this->dispatch('modalClosed');
}

    // Menyimpan data interaksi (create/update)
    public function save()
    {
        if (!$this->selectedCustomerId) {
            $this->dispatch('showAlert', [
                'title' => 'Error!',
                'message' => 'Please select a customer first',
                'icon' => 'error',
                'timer' => 3000,
            ]);
            return;
        }

        $validatedData = $this->validate();

        try {
            DB::beginTransaction(); // Sekarang DB bisa digunakan karena sudah diimpor

            if ($this->editingInteractionId) {
                $interaction = CustomerInteraction::findOrFail($this->editingInteractionId);
                $interaction->update([
                    'interaction_type' => $this->interaction_type,
                    'notes' => $this->notes,
                ]);
                $message = 'Interaction updated successfully';
            } else {
                CustomerInteraction::create([
                    'customer_id' => $this->selectedCustomerId,
                    'user_id' => Auth::id(),
                    'interaction_type' => $this->interaction_type,
                    'interaction_date' => now(),
                    'notes' => $this->notes,
                ]);
                $message = 'New interaction recorded successfully';
            }

            DB::commit();
            
            // Reset form dan tutup modal
            $this->reset(['interaction_type', 'notes', 'editingInteractionId']);
            $this->showModal = false;
            
            $this->dispatch('showAlert', [
                'title' => 'Success!',
                'message' => $message,
                'icon' => 'success',
                'timer' => 2000,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer Interaction Error:', [
                'message' => $e->getMessage(),
                'customer_id' => $this->selectedCustomerId,
                'user_id' => Auth::id()
            ]);
            
            $this->dispatch('showAlert', [
                'title' => 'Error!',
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000,
            ]);
        }
    }



    // Menghapus interaksi
    public function deleteInteraction($interactionId)
    {
        try {
            $interaction = CustomerInteraction::findOrFail($interactionId);
            $interaction->delete();

            // Tampilkan notifikasi sukses
            $this->dispatch('showAlert', [
                'title' => 'Success!',
                'message' => 'Interaction deleted successfully',
                'icon' => 'success',
                'timer' => 2000,
            ]);
        } catch (\Exception $e) {
            // Tampilkan notifikasi error
            $this->dispatch('showAlert', [
                'title' => 'Error!',
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'icon' => 'error',
                'timer' => 3000,
            ]);
        }
    }

    // Render view dengan data yang diperlukan
    public function render()
    {
        // Ambil daftar customer untuk dropdown selection
        $customers = Customer::when($this->search, function($query) {
            $query->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $this->search . '%');
        })->orderBy('customer_name')->get();

        // Ambil daftar interaksi hanya jika ada customer yang dipilih
        $interactions = $this->selectedCustomerId 
            ? CustomerInteraction::with('user')
                ->where('customer_id', $this->selectedCustomerId)
                ->when($this->filters['date_from'], function ($query) {
                    $query->whereDate('interaction_date', '>=', $this->filters['date_from']);
                })
                ->when($this->filters['date_to'], function ($query) {
                    $query->whereDate('interaction_date', '<=', $this->filters['date_to']);
                })
                ->when($this->filters['type'], function ($query) {
                    $query->where('interaction_type', $this->filters['type']);
                })
                ->orderBy('interaction_date', 'desc')
                ->paginate(10) 
            : collect();

        // Return view dengan data yang diperlukan
        return view('livewire.customer.interaction', [
            'customers' => $customers,
            'interactions' => $interactions,
            // Kirim selectedCustomerName ke view untuk menampilkan nama customer yang dipilih
            'customerName' => $this->selectedCustomerName
        ]);
    }
}
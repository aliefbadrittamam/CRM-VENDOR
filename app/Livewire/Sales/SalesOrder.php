<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sales;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Component
{
   use WithPagination;

   public $editMode = false;
   public $showModal = false;
   public $customer_id;
   public $sale_date;
   public $status = 'Pending'; 
   public $selectedProducts = [];
   public $quantities = [];
   public $fixed_amount = 0;
   public $search = '';
   public $statusFilter = '';
   public $sale_id;
   public $total = 0;

   protected $rules = [
       'customer_id' => 'required|exists:customers,customer_id',
       'sale_date' => 'required|date',
       'selectedProducts' => 'required|array|min:1',
       'quantities.*' => 'required|numeric|min:1'
   ];

   public function updatingSearch()
   {
       $this->resetPage();
   }

   public function updatingStatusFilter()
   {
       $this->resetPage();
   }

   public function mount()
   {
       $this->sale_date = date('Y-m-d');
       $this->status = 'Pending';
   }

   public function openModal()
   {
       $this->resetForm();
       $this->sale_date = date('Y-m-d');
       $this->status = 'Pending';
       $this->showModal = true;
   }

   public function closeModal()
   {
       $this->showModal = false;
       $this->resetForm();
   }

   public function calculateTotal()
   {
       $this->total = 0;
       $this->fixed_amount = 0;
       
       foreach ($this->selectedProducts as $productId) {
           if (isset($this->quantities[$productId])) {
               $product = Product::find($productId);
               if ($product) {
                   $subtotal = $product->product_price * $this->quantities[$productId];
                   $this->total += $subtotal;
                   $this->fixed_amount += $subtotal;
               }
           }
       }
   }

   public function updatedSelectedProducts($value)
   {
       if(!empty($value)) {
           foreach($this->selectedProducts as $productId) {
               if(!isset($this->quantities[$productId])) {
                   $this->quantities[$productId] = 1;
               }
           }
           $this->calculateTotal();
       }
   }

   public function updatedQuantities($value, $key)
   {
       $this->calculateTotal();
   }

   public function edit($id)
   {
       $this->resetForm();
       $this->editMode = true;
       $this->sale_id = $id;

       $sale = Sales::with('details')->find($id);
       if ($sale) {
           $this->customer_id = $sale->customer_id;
           $this->sale_date = $sale->sale_date;
           $this->status = $sale->status;
           $this->fixed_amount = $sale->fixed_amount;
           $this->total = $sale->fixed_amount;

           foreach ($sale->details as $detail) {
               $this->selectedProducts[] = $detail->product_id;
               $this->quantities[$detail->product_id] = $detail->quantity;
           }
       }

       $this->showModal = true;
   }

   public function updateStatus($id, $newStatus)
   {
       try {
           $sale = Sales::findOrFail($id);
           $sale->update(['status' => $newStatus]);
           
           $this->dispatch('order-updated', 'Order status updated successfully!');
       } catch (\Exception $e) {
           session()->flash('error', 'Error updating status: ' . $e->getMessage());
       }
   }

   public function save()
{
    $this->validate();

    try {
        DB::beginTransaction();

        $sale = Sales::create([
            'customer_id' => $this->customer_id,
            'fixed_amount' => $this->fixed_amount,
            'sale_date' => $this->sale_date,
            'status' => 'Pending'
        ]);

        foreach ($this->selectedProducts as $productId) {
            if (isset($this->quantities[$productId])) {
                $product = Product::find($productId);
                $quantity = $this->quantities[$productId];
                
                $sale->details()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'subtotal' => $product->product_price * $quantity,
                    'updated_at' => now() // PENTING! Karena ini required di database
                ]);
            }
        }

        DB::commit();
        
        $this->showModal = false;
        $this->dispatch('order-saved', 'Sales order created successfully!');
        $this->resetForm();

    } catch (\Exception $e) {
        DB::rollBack();
        logger('Error saving order: ' . $e->getMessage());
        session()->flash('error', 'Error saving order: ' . $e->getMessage());
    }
}

   public function delete($id)
   {
       try {
           DB::beginTransaction();

           $sale = Sales::findOrFail($id);
           $sale->details()->delete();
           $sale->delete();

           DB::commit();
           
           $this->dispatch('order-deleted', 'Sales order deleted successfully!');
       } catch (\Exception $e) {
           DB::rollBack();
           session()->flash('error', 'Error deleting order: ' . $e->getMessage());
       }
   }

   private function resetForm()
   {
       $this->reset([
           'customer_id',
           'selectedProducts',
           'quantities',
           'fixed_amount',
           'total',
           'editMode',
           'sale_id'
       ]);
       $this->sale_date = date('Y-m-d');
       $this->status = 'Pending';
   }

   public function render()
   {
       $query = Sales::with(['customer', 'details.product'])
           ->when($this->search, function($q) {
               $q->whereHas('customer', function($query) {
                   $query->where('customer_name', 'like', '%' . $this->search . '%');
               });
           })
           ->when($this->statusFilter, function($q) {
               $q->where('status', $this->statusFilter);
           })
           ->latest();

       return view('livewire.sales.sales-order', [
           'sales' => $query->paginate(10),
           'customers' => Customer::all(),
           'products' => Product::all()
       ]);
   }
}
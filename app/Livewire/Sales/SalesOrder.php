<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Component
{
    use WithPagination;

    // Properties untuk form
    public $customer_id;
    public $sale_date;
    public $status = 'Pending';
    public $fixed_amount = 0;

    // Properties untuk sales detail
    public $selectedProducts = [];
    public $quantities = [];

    // Properties untuk state
    public $showModal = false;
    public $editMode = false;
    public $sale_id;
    public $search = '';
    public $statusFilter = '';

    protected function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,customer_id',
            'sale_date' => 'required|date',
            'status' => 'required|in:Pending,Processing,Completed,Cancelled',
            'selectedProducts' => 'required|array|min:1',
            'selectedProducts.*' => 'exists:products,product_id',
            'quantities.*' => 'required|numeric|min:1'
        ];
    }

    public function mount()
    {
        $this->sale_date = date('Y-m-d');
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->editMode = true;
        $this->sale_id = $id;

        $sale = Sales::with('details')->findOrFail($id);
        
        $this->customer_id = $sale->customer_id;
        $this->sale_date = $sale->sale_date;
        $this->status = $sale->status;
        $this->fixed_amount = $sale->fixed_amount;

        foreach ($sale->details as $detail) {
            $this->selectedProducts[] = $detail->product_id;
            $this->quantities[$detail->product_id] = $detail->quantity;
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

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->selectedProducts as $productId) {
            if (isset($this->quantities[$productId])) {
                $product = Product::find($productId);
                $total += $product->product_price * $this->quantities[$productId];
            }
        }
        $this->fixed_amount = $total;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $this->calculateTotal();

            if ($this->editMode) {
                $sale = Sales::findOrFail($this->sale_id);
                $sale->update([
                    'customer_id' => $this->customer_id,
                    'sale_date' => $this->sale_date,
                    'status' => $this->status,
                    'fixed_amount' => $this->fixed_amount
                ]);

                // Delete existing details
                $sale->details()->delete();

            } else {
                $sale = Sales::create([
                    'customer_id' => $this->customer_id,
                    'sale_date' => $this->sale_date,
                    'status' => $this->status,
                    'fixed_amount' => $this->fixed_amount
                ]);
            }

            // Create new details
            foreach ($this->selectedProducts as $productId) {
                $product = Product::find($productId);
                $quantity = $this->quantities[$productId] ?? 1;
                
                $sale->details()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'subtotal' => $product->product_price * $quantity
                ]);
            }

            DB::commit();
            
            $this->showModal = false;
            $this->resetForm();
            
            $message = $this->editMode ? 'Sales order updated successfully!' : 'Sales order created successfully!';
            $this->dispatch('order-saved', $message);

        } catch (\Exception $e) {
            DB::rollBack();
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
            'status',
            'fixed_amount',
            'selectedProducts',
            'quantities',
            'editMode',
            'sale_id'
        ]);
        $this->sale_date = date('Y-m-d');
    }

    public function render()
    {
        $query = Sales::with(['customer', 'details.product'])
            ->when($this->search, function($q) {
                $q->whereHas('customer', function($query) {
                    $query->where('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_email', 'like', '%' . $this->search . '%');
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
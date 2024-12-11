<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shipping;
use App\Models\PurchaseDetail;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class ShippingStatus extends Component
{
    use WithPagination;

    // Properties untuk form
    public $purchase_detail_id;
    public $project_id;
    public $vendor_id;
    public $customer_id;
    public $shipping_status = 'Pending';
    public $number_receipt;

    // Properties untuk state
    public $showModal = false;
    public $editMode = false;
    public $shipping_id;

    // Properties untuk filter
    public $search = '';
    public $statusFilter = '';
    public $projectFilter = '';
    public $vendorFilter = '';

    protected function rules()
    {
        return [
            'purchase_detail_id' => 'required|exists:purchase_details,purchase_detail_id',
            'project_id' => 'required|exists:projects,project_id',
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'shipping_status' => 'required|in:Pending,Completed,Cancelled',
            'number_receipt' => 'required|integer'
        ];
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
        $this->shipping_id = $id;

        $shipping = Shipping::findOrFail($id);
        
        $this->purchase_detail_id = $shipping->purchase_detail_id;
        $this->project_id = $shipping->project_id;
        $this->vendor_id = $shipping->vendor_id;
        $this->customer_id = $shipping->customer_id;
        $this->shipping_status = $shipping->shipping_status;
        $this->number_receipt = $shipping->Number_receipt;

        $this->showModal = true;
    }

    public function updateStatus($id, $newStatus)
    {
        try {
            $shipping = Shipping::findOrFail($id);
            $shipping->update(['shipping_status' => $newStatus]);
            
            $this->dispatch('shipping-updated', 'Shipping status updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'purchase_detail_id' => $this->purchase_detail_id,
                'project_id' => $this->project_id,
                'vendor_id' => $this->vendor_id,
                'customer_id' => $this->customer_id,
                'shipping_status' => $this->shipping_status,
                'Number_receipt' => $this->number_receipt
            ];

            if ($this->editMode) {
                $shipping = Shipping::findOrFail($this->shipping_id);
                $shipping->update($data);
                $message = 'Shipping updated successfully!';
            } else {
                Shipping::create($data);
                $message = 'Shipping created successfully!';
            }

            DB::commit();

            $this->showModal = false;
            $this->resetForm();
            
            $this->dispatch('shipping-saved', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving shipping: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $shipping = Shipping::findOrFail($id);
            $shipping->delete();
            
            $this->dispatch('shipping-deleted', 'Shipping deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting shipping: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'purchase_detail_id',
            'project_id',
            'vendor_id',
            'customer_id',
            'shipping_status',
            'number_receipt',
            'editMode',
            'shipping_id'
        ]);
    }

    public function render()
    {
        $query = Shipping::with(['purchaseDetail', 'project', 'vendor', 'customer'])
            ->when($this->search, function($q) {
                $q->whereHas('customer', function($query) {
                    $query->where('customer_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('vendor', function($query) {
                    $query->where('vendor_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function($q) {
                $q->where('shipping_status', $this->statusFilter);
            })
            ->when($this->projectFilter, function($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->vendorFilter, function($q) {
                $q->where('vendor_id', $this->vendorFilter);
            })
            ->latest();

        return view('livewire.sales.shipping-status', [
            'shippings' => $query->paginate(10),
            'purchaseDetails' => PurchaseDetail::all(),
            'projects' => Project::all(),
            'vendors' => Vendor::all(),
            'customers' => Customer::all()
        ]);
    }
}
<?php
namespace App\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PriceQuotation as PriceQuotationModel;
use App\Models\Project;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class PriceQuotation extends Component
{
    use WithPagination;

    public $project_id;
    public $vendor_id;
    public $amount;
    public $showModal = false;
    public $editMode = false;
    public $quotation_id; // Tambahkan ini untuk tracking id yang diedit

    // Method untuk edit
    public function edit($id)
    {
        $this->editMode = true;
        $this->quotation_id = $id;
        
        $quotation = PriceQuotationModel::findOrFail($id);
        
        $this->project_id = $quotation->project_id;
        $this->vendor_id = $quotation->vendor_id;
        $this->amount = $quotation->amount;
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->editMode) {
                $quotation = PriceQuotationModel::findOrFail($this->quotation_id);
                $quotation->update([
                    'project_id' => $this->project_id,
                    'vendor_id' => $this->vendor_id,
                    'amount' => $this->amount
                ]);
                $message = 'Price quotation updated successfully!';
            } else {
                PriceQuotationModel::create([
                    'project_id' => $this->project_id,
                    'vendor_id' => $this->vendor_id,
                    'amount' => $this->amount
                ]);
                $message = 'Price quotation created successfully!';
            }

            DB::commit();

            $this->showModal = false;
            $this->reset(['project_id', 'vendor_id', 'amount', 'editMode', 'quotation_id']);
            
            $this->dispatch('quotation-saved', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $quotation = PriceQuotationModel::findOrFail($id);
            $quotation->delete();
            
            $this->dispatch('quotation-saved', 'Price quotation deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting quotation: ' . $e->getMessage());
        }
    }
    protected function rules()
    {
        return [
            'project_id' => 'required|exists:projects,project_id', // Ubah 'project' menjadi 'projects'
            'vendor_id' => 'required|exists:vendors,vendor_id',
            'amount' => 'required|numeric|min:0',
        ];
    }
    public function create()
    {
        $this->reset(['project_id', 'vendor_id', 'amount', 'editMode', 'quotation_id']);
        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.sales.price-quotation', [
            'quotations' => PriceQuotationModel::with(['project', 'vendor'])->latest()->paginate(10),
            'projects' => Project::all(),
            'vendors' => Vendor::all()
        ]);
    }
}
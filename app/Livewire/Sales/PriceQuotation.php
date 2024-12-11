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
    public $quotation_id;

    // Properties untuk filtering
    public $search = '';
    public $projectFilter = '';
    public $vendorFilter = '';



      // Reset pagination ketika filter berubah
      public function updatingSearch()
      {
          $this->resetPage();
      }
  
      public function updatingProjectFilter()
      {
          $this->resetPage();
      }
  
      public function updatingVendorFilter()
      {
          $this->resetPage();
      }

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
        $query = PriceQuotationModel::query()
            ->with(['project', 'vendor'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->whereHas('project', function($q) {
                        $q->where('project_header', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('vendor', function($q) {
                        $q->where('vendor_name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->projectFilter, function($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->vendorFilter, function($q) {
                $q->where('vendor_id', $this->vendorFilter);
            });

        $quotations = $query->latest()->paginate(10);

        // Debug info
        // dd([
        //     'search' => $this->search,
        //     'projectFilter' => $this->projectFilter,
        //     'vendorFilter' => $this->vendorFilter,
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings()
        // ]);

        return view('livewire.sales.price-quotation', [
            'quotations' => $quotations,
            'projects' => Project::orderBy('project_header')->get(),
            'vendors' => Vendor::orderBy('vendor_name')->get()
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['search', 'projectFilter', 'vendorFilter']);
        $this->resetPage();
    }
}
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\StockWriteOff;

class WarehouseOperations extends Component
{
    public $selectedWarehouse; // Текущий выбранный склад
    public $categoryFilter;    // Фильтр по категории
    public $stockData = [];    // Данные о стоке

    public function mount()
    {
        $this->selectedWarehouse = null;
        $this->categoryFilter = null;
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadStockData();
    }

    public function updatedCategoryFilter()
    {
        $this->loadStockData();
    }

    public function loadStockData()
    {
        $query = WarehouseStock::query()
            ->with(['product', 'warehouse'])
            ->when($this->selectedWarehouse, function ($q) {
                $q->where('warehouse_id', $this->selectedWarehouse);
            });

        if ($this->categoryFilter) {
            $query->whereHas('product.category', function ($q) {
                $q->where('id', $this->categoryFilter);
            });
        }

        $this->stockData = $query->get();
    }

    public function render()
    {
        return view('livewire.admin.warehouse-operations', [
            'warehouses' => Warehouse::all(),
            'categories' => \App\Models\Category::all(),
        ]);
    }
}

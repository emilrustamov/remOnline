<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockMovement;
use App\Models\WarehouseStockWriteOff;
use App\Models\Category;

class WarehouseOperations extends Component
{
    public $selectedWarehouse; // Текущий выбранный склад
    public $categoryFilter;    // Фильтр по категории
    public $stockData = [];    // Данные о стоке
    public $writeOffModal = false;    // Управляет видимостью модального окна
    public $writeOffProductId = null; // Идентификатор товара для списания
    public $writeOffQuantity = 1;     // Количество для списания
    public $writeOffReason = '';      // Причина списания

    public function mount()
    {
        $this->selectedWarehouse = null;
        $this->categoryFilter = null;
        $this->loadStockData();
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
            'categories' => Category::all(),
            'stockData' => $this->stockData,
        ]);
    }
}

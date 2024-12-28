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
            ->with(['product.category', 'warehouse'])
            ->when($this->selectedWarehouse, function ($q) {
                $q->where('warehouse_id', $this->selectedWarehouse);
            });

        if ($this->categoryFilter) {
            $query->whereHas('product.category', function ($q) {
                $q->where('id', $this->categoryFilter);
            });
        }

        $this->stockData = $query->get()->map(function ($stock) {
            return [
                'sku' => $stock->product->sku,
                'name' => $stock->product->name,
                'stock' => $stock->stock,
                'category' => $stock->product->category->name ?? 'Без категории',
            ];
        });
    }

    public function render()
    {
        $this->loadStockData();

        return view('livewire.admin.warehouse-operations', [
            'warehouses' => Warehouse::all(),
            'categories' => Category::all(), // Ensure this returns an array of category objects
        ]);
    }
}

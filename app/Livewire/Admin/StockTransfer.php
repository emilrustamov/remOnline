<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;

class StockTransfer extends Component
{
    public $products = [];
    public $selectedWarehouseFrom;
    public $selectedWarehouseTo;
    public $selectedProducts = [];
    public $note;
    public $productModal = false;
    public $productQuantity = 1;
    public $currentProductId;
    public $stockMovements = [];

    public function updatedSelectedWarehouseFrom()
    {
        $this->loadProductsFromWarehouse();
    }

    public function updatedSelectedWarehouseTo()
    {
        if ($this->selectedWarehouseFrom == $this->selectedWarehouseTo) {
            session()->flash('error', 'Склад-отправитель и склад-получатель не могут быть одинаковыми.');
            $this->selectedWarehouseTo = null; // Сбросим склад-получатель
        }
    }

    public function loadProductsFromWarehouse()
    {
        if ($this->selectedWarehouseFrom) {
            $this->products = WarehouseStock::where('warehouse_id', $this->selectedWarehouseFrom)
                ->with('product')
                ->get();
        } else {
            $this->products = [];
        }
    }

    public function addProduct($productId)
    {
        if (!isset($this->selectedProducts[$productId])) {
            $product = Product::findOrFail($productId);
            $this->selectedProducts[$productId] = [
                'name' => $product->name,
                'quantity' => 1,
            ];
        }
        $this->openProductModal($productId);
    }

    public function openProductModal($productId)
    {
        $this->currentProductId = $productId;
        $this->productQuantity = $this->selectedProducts[$productId]['quantity'];
        $this->productModal = true;
    }

    public function saveProductModal()
    {
        $this->validate([
            'productQuantity' => 'required|integer|min:1',
        ]);

        $this->selectedProducts[$this->currentProductId]['quantity'] = $this->productQuantity;
        $this->productModal = false;
    }

    public function removeProduct($productId)
    {
        unset($this->selectedProducts[$productId]);
    }

    public function saveTransfer()
    {
        $this->validate([
            'selectedWarehouseFrom' => 'required|exists:warehouses,id',
            'selectedWarehouseTo' => 'required|exists:warehouses,id|different:selectedWarehouseFrom',
            'selectedProducts' => 'required|array|min:1',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            // Получаем товар на складе-отправителе
            $stockFrom = WarehouseStock::where('warehouse_id', $this->selectedWarehouseFrom)
                ->where('product_id', $productId)
                ->first();

            // Если товар не найден на складе или его недостаточно
            if (!$stockFrom || $stockFrom->quantity < $details['quantity']) {
                session()->flash('error', "Недостаточно товара на складе для перемещения: {$details['name']}. Доступно: {$stockFrom->quantity}");
                return;  // Останавливаем выполнение метода, чтобы не создавать запись о перемещении
            }

            // Уменьшаем количество на складе-отправителе
            $stockFrom->update(['quantity' => $stockFrom->quantity - $details['quantity']]);

            // Получаем или создаем запись для склада-получателя
            $stockTo = WarehouseStock::where('warehouse_id', $this->selectedWarehouseTo)
                ->where('product_id', $productId)
                ->first();

            if ($stockTo) {
                $stockTo->update(['quantity' => $stockTo->quantity + $details['quantity']]);
            } else {
                WarehouseStock::create([
                    'warehouse_id' => $this->selectedWarehouseTo,
                    'product_id' => $productId,
                    'quantity' => $details['quantity'],
                ]);
            }

            // Создаем запись о перемещении
            StockMovement::create([
                'product_id' => $productId,
                'warehouse_from' => $this->selectedWarehouseFrom,
                'warehouse_to' => $this->selectedWarehouseTo,
                'note' => $this->note,
            ]);
        }

        session()->flash('success', 'Перемещение успешно выполнено.');
        $this->reset(['selectedWarehouseFrom', 'selectedWarehouseTo', 'selectedProducts', 'note']);
    }


    public function render()
    {
        $this->stockMovements = StockMovement::with(['product', 'warehouseFrom', 'warehouseTo'])->latest()->get();

        return view('livewire.admin.stock-transfer', [
            'warehouses' => Warehouse::all(),
            'products' => $this->products,
            'stockMovements' => $this->stockMovements,
        ]);
    }
}

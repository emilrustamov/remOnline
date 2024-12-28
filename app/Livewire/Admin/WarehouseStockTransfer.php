<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockMovement;

class WarehouseStockTransfer extends Component
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
    public $showForm = false;
    public $showConfirmationModal = false;
    public $transferId;

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

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function closeProductModal()
    {
        $this->productModal = false;
    }

    // Функция для закрытия модального окна
    public function closeForm()
    {
        if ($this->isFormChanged()) {
            $this->showConfirmationModal = true;
        } else {
            $this->resetForm();
        }
    }


    public function closeModal($confirm = false)
    {
        if ($confirm) {
            $this->resetForm();
        }
        $this->showConfirmationModal = false;
    }

    public function isFormChanged()
    {

        return $this->selectedWarehouseFrom;
    }


    public function resetForm()
    {
        $this->reset(['selectedWarehouseFrom', 'selectedWarehouseTo', 'selectedProducts', 'note', 'productModal', 'productQuantity', 'currentProductId']);
        $this->transferId = null;
        $this->showForm = false;
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
            if (!$stockFrom || $stockFrom->quantity < $details['quantity'] || $details['quantity'] <= 0) {
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
            $movementData = [
                'product_id' => $productId,
                'warehouse_from' => $this->selectedWarehouseFrom,
                'warehouse_to' => $this->selectedWarehouseTo,
                'quantity' => $details['quantity'], // Ensure quantity is set correctly
                'note' => $this->note,
            ];

            if ($this->transferId) {
                $movement = WarehouseStockMovement::findOrFail($this->transferId);
                $movement->update($movementData);
            } else {
                WarehouseStockMovement::create($movementData);
            }
        }

        session()->flash('success', 'Перемещение успешно выполнено.');
        $this->resetForm();
        $this->closeForm();
        $this->loadProductsFromWarehouse(); // Reload products to update stock data
    }

    public function editTransfer($transferId)
    {
        $movement = WarehouseStockMovement::findOrFail($transferId);
        $this->selectedWarehouseFrom = $movement->warehouse_from;
        $this->selectedWarehouseTo = $movement->warehouse_to;
        $this->note = $movement->note;
        $this->selectedProducts = [
            $movement->product_id => [
                'name' => $movement->product->name,
                'quantity' => $movement->quantity,
            ],
        ];
        $this->transferId = $transferId;
        $this->showForm = true;
    }

    public function deleteTransfer()
    {
        if ($this->transferId) {
            $movement = WarehouseStockMovement::findOrFail($this->transferId);
            $stockFrom = WarehouseStock::where('warehouse_id', $movement->warehouse_from)
                ->where('product_id', $movement->product_id)
                ->first();

            if ($stockFrom) {
                $stockFrom->update(['quantity' => $stockFrom->quantity + $movement->quantity]);
            }

            $stockTo = WarehouseStock::where('warehouse_id', $movement->warehouse_to)
                ->where('product_id', $movement->product_id)
                ->first();

            if ($stockTo) {
                $stockTo->update(['quantity' => $stockTo->quantity - $movement->quantity]);
            }

            $movement->delete();
            session()->flash('success', 'Перемещение успешно удалено.');
            $this->resetForm();
            $this->loadProductsFromWarehouse();
        }
    }

    public function render()
    {
        $this->stockMovements = WarehouseStockMovement::with(['product', 'warehouseFrom', 'warehouseTo'])->latest()->get();

        return view('livewire.admin.stock-transfer', [
            'warehouses' => Warehouse::all(),
            'products' => $this->products,
            'stockMovements' => $this->stockMovements,
        ]);
    }
}

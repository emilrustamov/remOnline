<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockWriteOff;

class WarehouseStockWriteOffs extends Component
{
    public $selectedWarehouse;
    public $selectedProducts = [];
    public $reason;
    public $date;
    public $stockWriteOffs;
    public $productModal = false;
    public $productQuantity = 1;
    public $currentProductId;
    public $warehouseProducts = [];
    public $productSearch = '';
    public $showForm = false;
    public $showConfirmationModal = false;
    public $writeOffId; // Declare as public property

    // Функция для открытия модального окна
    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
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
        $originalWarehouse = Warehouse::find($this->selectedWarehouse);
        $originalProducts = WarehouseStockWriteOff::where('warehouse_id', $this->selectedWarehouse)
            ->pluck('product_id')
            ->toArray();
        $originalReason = WarehouseStockWriteOff::where('warehouse_id', $this->selectedWarehouse)
            ->pluck('reason')
            ->first();

        return $this->selectedWarehouse !== ($originalWarehouse->id ?? null) ||
            $this->selectedProducts !== $originalProducts ||
            $this->reason !== ($originalReason ?? '');
    }


    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->loadWriteOffs();
    }

    public function updatedSelectedWarehouse()
    {
        $this->loadWarehouseProducts();
    }

    public function loadWriteOffs()
    {
        $this->stockWriteOffs = WarehouseStockWriteOff::with(['product', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function loadWarehouseProducts()
    {
        if ($this->selectedWarehouse) {
            $query = WarehouseStock::where('warehouse_id', $this->selectedWarehouse)
                ->with('product');

            // Фильтрация по имени или артикулу товара
            if ($this->productSearch != "") {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
                });
            }

            $this->warehouseProducts = $query->get();
        } else {
            $this->warehouseProducts = [];
        }
    }



    public function addProduct($productId)
    {
        $stock = WarehouseStock::where('warehouse_id', $this->selectedWarehouse)
            ->where('product_id', $productId)
            ->with('product') // Подгружаем товар
            ->first();

        if (!$stock) {
            session()->flash('error', 'Товар не найден на складе.');
            return;
        }

        $this->selectedProducts[$productId] = [
            'name' => $stock->product->name ?? 'Название недоступно',
            'quantity' => 1,
        ];

        $this->openProductModal($productId);
    }


    public function openProductModal($productId)
    {
        $this->currentProductId = $productId;
        $this->productQuantity = $this->selectedProducts[$productId]['quantity'];
        $this->productModal = true;
    }

    // Функция для закрытия модального окна для товара
    public function closeProductModal()
    {
        $this->productModal = false;
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

    public function saveWriteOff()
    {
        $this->validate([
            'selectedWarehouse' => 'required|exists:warehouses,id',
            'reason' => 'required|string|max:255',
            'selectedProducts' => 'required|array|min:1',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            $stock = WarehouseStock::where('warehouse_id', $this->selectedWarehouse)
                ->where('product_id', $productId)
                ->first();

            if (!$stock || $stock->quantity < $details['quantity']) {
                session()->flash('error', "Недостаточно товара на складе для списания: {$details['name']}");
                return;
            }

            $writeOffData = [
                'warehouse_id' => $this->selectedWarehouse,
                'product_id' => $productId,
                'reason' => $this->reason,
                'quantity' => $details['quantity'],
            ];

            if ($this->writeOffId) {
                $writeOff = WarehouseStockWriteOff::findOrFail($this->writeOffId);
                // Revert the stock changes made by the original write-off
                $stock->update(['quantity' => $stock->quantity + $writeOff->quantity]);

                $writeOff->update($writeOffData);
                $stock->update(['quantity' => $stock->quantity - $details['quantity']]);
            } else {
                WarehouseStockWriteOff::create($writeOffData);
                $stock->update(['quantity' => $stock->quantity - $details['quantity']]);
            }
        }

        session()->flash('success', 'Списание успешно выполнено.');
        $this->resetForm();
        $this->loadWriteOffs();
        $this->showForm = false;
    }

    public function editWriteOff($writeOffId)
    {
        $writeOff = WarehouseStockWriteOff::findOrFail($writeOffId);
        $this->selectedWarehouse = $writeOff->warehouse_id;
        $this->reason = $writeOff->reason;
        $this->selectedProducts = [
            $writeOff->product_id => [
                'name' => $writeOff->product->name,
                'quantity' => $writeOff->quantity,
            ],
        ];
        $this->writeOffId = $writeOffId; // Ensure writeOffId is set correctly
        $this->showForm = true;
    }

    public function deleteWriteOff()
    {
        if ($this->writeOffId) {
            $writeOff = WarehouseStockWriteOff::findOrFail($this->writeOffId);
            $stock = WarehouseStock::where('warehouse_id', $writeOff->warehouse_id)
                ->where('product_id', $writeOff->product_id)
                ->first();

            if ($stock) {
                // Revert the stock changes made by the deleted write-off
                $stock->update(['quantity' => $stock->quantity + $writeOff->quantity]);
            }

            $writeOff->delete();
            session()->flash('success', 'Списание успешно удалено.');
            $this->resetForm();
            $this->loadWriteOffs();
        } else {
            session()->flash('error', 'Не удалось найти списание для удаления.');
        }
    }

    public function render()
    {
        if ($this->selectedWarehouse != null && $this->selectedWarehouse != '') {
            $this->updatedSelectedWarehouse();
        }
        return view('livewire.admin.stock-write-offs', [
            'warehouses' => Warehouse::all(),
            'products' => $this->warehouseProducts,
        ]);
    }


    public function resetForm()
    {
        $this->reset(['selectedWarehouse', 'selectedProducts', 'reason', 'productSearch', 'warehouseProducts']);
        $this->writeOffId = null;
        $this->showForm = false;
    }
}

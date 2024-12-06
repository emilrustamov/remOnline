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
    public $selectedWarehouse; // Выбранный склад
    public $selectedProducts = []; // Товары для списания
    public $reason; // Причина списания
    public $date; // Дата списания
    public $stockWriteOffs; // Список всех списаний
    public $productModal = false; // Модальное окно для добавления товаров
    public $productQuantity = 1; // Количество товара
    public $currentProductId; // Текущий выбранный товар
    public $warehouseProducts = []; // Доступные товары со склада
    public $productSearch = '';
    // В Livewire компоненте

    public $showWriteOffModal = false;  // Флаг для показа модального окна

    // Функция для открытия модального окна
    public function openWriteOffModal()
    {
        $this->showWriteOffModal = true;
    }

    // Функция для закрытия модального окна
    public function closeWriteOffModal()
    {
        $this->showWriteOffModal = false;
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

            // Создание записи списания
            WarehouseStockWriteOff::create([
                'warehouse_id' => $this->selectedWarehouse,
                'product_id' => $productId,
                'reason' => $this->reason,
                'quantity' => $details['quantity'],
            ]);

            // Обновление количества в стоке
            $stock->update(['quantity' => $stock->quantity - $details['quantity']]);
        }

        session()->flash('success', 'Списание успешно выполнено.');

        $this->reset(['selectedWarehouse', 'selectedProducts', 'reason']);
        $this->loadWriteOffs();
        $this->showWriteOffModal = false;
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
}

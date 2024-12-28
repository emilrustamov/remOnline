<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Client;
use App\Models\Warehouse;
use App\Models\WarehouseProductReceipt;
use App\Models\WarehouseStock;

class WarehouseStockReception extends Component
{
    public $selectedWarehouse;
    public $selectedProducts = [];
    public $supplierId;
    public $warehouseId;
    public $invoiceNumber;
    public $date;
    public $comments;
    public $priceInputModal = false;
    public $currentProductId;
    public $productQuantity = 1;
    public $productPrice;
    public $productModal = false;
    public $productSearch;
    public $editingProductId = null;
    public $invoiceString = ''; // Поле для строки
    public $invoiceDate;
    public $showConfirmationModal = false;
    public $products = []; // Доступные товары для выбранного склада
    public $showForm = false; 
    public $receptionId = null;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        // $this->loadWriteOffs();
    }

    public function openForm()
    {
        $this->resetForm(); 
        $this->showForm = true;
    }

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
        $warehouse = Warehouse::find($this->warehouseId);
        $supplier = Client::find($this->supplierId);

        return $this->invoiceNumber || $this->comments || $this->selectedProducts || 
               $this->invoiceString || $this->invoiceDate || 
               $this->warehouseId !== ($warehouse->id ?? null) || 
               $this->supplierId !== ($supplier->id ?? null);
    }

    public function updatedWarehouseId()
    {
        $this->loadWarehouseProducts();
    }


    public function loadWarehouseProducts()
    {
        // Начинаем с запроса всех товаров
        $query = Product::query();

        // Фильтрация по имени или артикулу
        if (!empty($this->productSearch)) {
            $query->where('name', 'like', '%' . $this->productSearch . '%')
                ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
        }

        // Загружаем список товаров
        $this->products = $query->get();
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
        $this->editingProductId = $productId;

        $product = Product::findOrFail($productId);

        $this->productQuantity = $this->selectedProducts[$productId]['quantity'] ?? 1;
        $this->productPrice = $this->selectedProducts[$productId]['price'] ?? null;

        $this->productModal = true;
    }

    public function closeProductModal()
    {
        $this->productModal = false;
        $this->currentProductId = null;
        $this->editingProductId = null; // Сбрасываем
    }

    public function saveProductModal()
    {
        $this->validate([
            'productQuantity' => 'required|integer|min:1',
            'productPrice' => 'required|numeric|min:0',
        ]);

        $this->selectedProducts[$this->currentProductId] = [
            'name' => Product::find($this->currentProductId)->name,
            'quantity' => $this->productQuantity,
            'price' => $this->productPrice,
        ];

        $this->closeProductModal();
    }

    public function saveReception()
    {
        $this->validate([
            'supplierId' => 'required|exists:clients,id',
            'warehouseId' => 'required|exists:warehouses,id',
            'invoiceNumber' => 'nullable|string|max:255',
            'selectedProducts' => 'required|array|min:1',
            'comments' => 'nullable|string|max:255',
        ]);

        foreach ($this->selectedProducts as $productId => $details) {
            $invoice = $this->invoiceDate ?? now()->toDateString();
            if (!empty($this->invoiceString)) {
                $invoice = $this->invoiceString . ' ' . $invoice;
            }

            if ($this->receptionId) {
                $reception = WarehouseProductReceipt::findOrFail($this->receptionId);
                $reception->update([
                    'invoice' => $invoice,
                    'supplier_id' => $this->supplierId,
                    'warehouse_id' => $this->warehouseId,
                    'product_id' => $productId,
                    'note' => $this->comments,
                    'purchase_price' => $details['price'],
                    'quantity' => $details['quantity'],
                ]);
            } else {
                WarehouseProductReceipt::create([
                    'invoice' => $invoice,
                    'supplier_id' => $this->supplierId,
                    'warehouse_id' => $this->warehouseId,
                    'product_id' => $productId,
                    'note' => $this->comments,
                    'purchase_price' => $details['price'],
                    'quantity' => $details['quantity'],
                ]);
            }

            // Update or create the stock record
            WarehouseStock::updateOrCreate(
                [
                    'warehouse_id' => $this->warehouseId,
                    'product_id' => $productId,
                ],
                [
                    'quantity' => \DB::raw('quantity + ' . $details['quantity']),
                ]
            );
        }

        session()->flash('success', 'Оприходование успешно сохранено.');
        $this->saveInvoice();
        $this->resetForm();
        $this->closeForm(); // Закрываем модальное окно после сохранения
    }

    public function saveInvoice()
    {
        $this->validate([
            'invoiceDate' => 'nullable|date',
            'invoiceString' => 'nullable|string',
        ]);

        $invoice = $this->invoiceDate;
        if (!empty($this->invoiceString)) {
            $invoice = $this->invoiceString . ' ' . $invoice;
        }


        $this->reset(['invoiceString', 'invoiceDate', 'comments']);

        session()->flash('message', 'Накладная успешно сохранена!');
    }

    public function editReception($receptionId)
    {
        $reception = WarehouseProductReceipt::findOrFail($receptionId);

        $this->receptionId = $receptionId;
        $this->supplierId = $reception->supplier_id;
        $this->warehouseId = $reception->warehouse_id;
        $this->invoiceString = $reception->invoice;
        $this->comments = $reception->note;
        $this->selectedProducts = [
            $reception->product_id => [
                'name' => $reception->product->name,
                'quantity' => $reception->quantity,
                'price' => $reception->purchase_price,
            ]
        ];

        $this->showForm = true;
    }

    public function deleteReception()
    {
        if ($this->receptionId) {
            $reception = WarehouseProductReceipt::findOrFail($this->receptionId);
            $reception->delete();

            session()->flash('success', 'Оприходование успешно удалено.');
            $this->resetForm();
            $this->closeForm();
        }
    }

    public function resetForm()
    {
        $this->supplierId = null;
        $this->warehouseId = null;
        $this->invoiceNumber = null;
        $this->selectedProducts = [];
        $this->comments = null;
        $this->productSearch = null;
        $this->products = [];
        $this->showForm = false;
    }

    public function render()
    {
        $this->loadWarehouseProducts();
        if ($this->supplierId != null && $this->supplierId != '' && $this->warehouseId != null && $this->warehouseId != '') {
            $this->updatedWarehouseId();
        }
        return view('livewire.admin.stock-reception', [
            'suppliers' => Client::where('is_supplier', true)->get(),
            'warehouses' => Warehouse::all(),
            'products' => $this->products,
            'stockReceptions' => WarehouseProductReceipt::with(['supplier', 'warehouse', 'product'])->latest()->get(),
        ]);
    }
}

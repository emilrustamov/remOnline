<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ProductBatch;

class ProductBatches extends Component
{


    public $productId, $batchNumber, $manufactureDate, $expireDate, $quantity;

    public function createBatch()
    {
        $this->validate([
            'productId' => 'required|exists:products,id',
            'batchNumber' => 'required|string|unique:product_batches,batch_number',
            'manufactureDate' => 'nullable|date',
            'expireDate' => 'nullable|date',
            'quantity' => 'required|integer|min:1',
        ]);

        $batch = ProductBatch::create([
            'product_id' => $this->productId,
            'batch_number' => $this->batchNumber,
            'manufacture_date' => $this->manufactureDate,
            'expire_date' => $this->expireDate,
        ]);

        $batch->generateSerialNumbers($this->quantity);

        session()->flash('success', 'Партия и серийные номера успешно созданы.');
        $this->resetForm();
    }


    public function render()
    {
        return view('livewire.admin.product-batches', [
            'products' => \App\Models\Product::all(), // Загрузка всех продуктов
        ]);
    }
}

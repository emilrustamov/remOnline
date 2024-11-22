<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Livewire\WithFileUploads;

class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name, $description, $sku, $articul, $stock_quantity, $status = true, $productId, $images = [], $newImages = [], $barcode, $showForm = false, $category_id;


    public function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->description = '';
        $this->sku = '';
        $this->articul = '';
        $this->stock_quantity = '';
        $this->status = true;
        $this->images = [];
        $this->barcode = null;
        $this->category_id = null;


        $this->showForm = false; // Закрываем форму при сбросе
    }

    public function createProduct()
    {
        $this->resetForm();
        $this->showForm = true; // Открываем форму для создания
    }

    public function saveProduct()
    {
        // Валидация только для новых загружаемых файлов
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,' . ($this->productId ?? 'NULL'),
            'articul' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'boolean',
            'newImages.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPaths = $this->images; // Сохраняем уже существующие пути

        foreach ($this->newImages as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $photoPaths[] = $image->store('products', 'public'); // Сохраняем новые изображения
            }
        }
        

        // Сохранение продукта
        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'name' => $this->name,
                'category_id' => $this->category_id,
                'description' => $this->description,
                'sku' => $this->sku,
                'articul' => $this->articul,
                'stock_quantity' => $this->stock_quantity,
                'status' => $this->status,
                'images' => json_encode($photoPaths), // Преобразуем массив в JSON
                'barcode' => $this->barcode,
            ]
        );

        session()->flash('success', $this->productId ? 'Товар успешно обновлен.' : 'Товар успешно добавлен.');
        $this->resetForm();
    }


    public function generateBarcode()
    {
        $ean = substr(str_pad(rand(1, 999999999999), 12, '0', STR_PAD_LEFT), 0, 12);
        $checksum = $this->calculateEAN13Checksum($ean);
        return $ean . $checksum;
    }
    public function generateBarcodeManually()
    {
        $this->barcode = $this->generateBarcode();
        session()->flash('success', 'Штрих-код успешно сгенерирован.');
    }


    private function calculateEAN13Checksum($ean)
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 === 0 ? 1 : 3) * $ean[$i];
        }
        return (10 - ($sum % 10)) % 10;
    }


    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->sku = $product->sku;
        $this->articul = $product->articul;
        $this->stock_quantity = $product->stock_quantity;
        $this->status = $product->status;
        $this->showForm = true;
        $this->images = $product->images ? json_decode($product->images, true) : [];
        $this->barcode = $product->barcode;
        $this->category_id = $product->category_id;
    }

    public function update()
    {
        $this->validate();

        $product = Product::findOrFail($this->productId);

        $product->update([
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'articul' => $this->articul,
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Товар успешно обновлен.');

        $this->resetForm();
    }

    public function deleteProduct($id)
    {
        Product::findOrFail($id)->delete();
        $this->showForm = false;
        session()->flash('success', 'Товар успешно удален.');
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images); // Переиндексация массива
    }


    public function render()
    {
        return view('livewire.admin.products', [
            'products' => Product::paginate(10),
            'categories' => \App\Models\Category::all(),
        ]);
    }
}

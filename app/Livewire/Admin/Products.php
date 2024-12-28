<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Price;
use App\Models\Currency;
use App\Models\Category;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\QueryException;

class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name, $description, $sku, $articul, $stock_quantity, $status = true, $productId, $images = [], $newImages = [], $prices = [], $barcode,  $category_id;
    public $defaultCurrencyId;
    public $showForm = false;
    public $showCategoryForm = false;
    public $showConfirmationModal = false;
    public $categoryName;
    public $parentCategoryId;
    public $columns = [
        'name',
        'sku',
        'stock_quantity'
    ];

    public function mount()
    {
        // Устанавливаем дефолтную валюту при загрузке компонента
        $defaultCurrency = Currency::where('is_default', true)->first();
        $this->defaultCurrencyId = $defaultCurrency ? $defaultCurrency->id : null;
    }


    // Метод для сохранения категории
    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'parentCategoryId' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $this->categoryName,
            'parent_id' => $this->parentCategoryId,
        ]);

        session()->flash('success', 'Категория успешно добавлена.');
        $this->resetCategoryForm();

        // Обновляем список категорий
        $this->dispatch('updated');
    }

    public function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->description = '';
        $this->sku = '';
        $this->articul = '';
        // $this->stock_quantity = '';
        // $this->status = true;
        $this->images = [];
        $this->barcode = null;
        $this->category_id = null;

        $this->showForm = false; 
    }


    public function openForm()
    {
        $this->reset();
        $this->showForm = true; // Открываем форму для создания
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
        $product = Product::find($this->productId);
        return
            $this->name !== ($product->name ?? null) ||
            $this->description !== ($product->description ?? null) ||
            $this->sku !== ($product->sku ?? null) ||
            $this->articul !== ($product->articul ?? null) ||
            // $this->status !== ($product->status ?? null) ||
            $this->barcode !== ($product->barcode ?? null)  ||
            $this->category_id !== ($product->category_id ?? null);
            
    }


    public function saveProduct()
    {
        // Валидация только для новых загружаемых файлов
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,' . ($this->productId ?? 'NULL'),
            'articul' => 'nullable|string|max:255',
            'status' => 'boolean',
            'newImages.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'prices.Розничная' => 'nullable|numeric|min:0',
            'prices.Оптовая' => 'nullable|numeric|min:0',
        ]);

        $photoPaths = $this->images; // Сохраняем уже существующие пути

        foreach ($this->newImages as $image) {
            if ($image instanceof UploadedFile) {
                $photoPaths[] = $image->store('products', 'public'); // Сохраняем новые изображения
            }
        }

        try {
            // Сохранение продукта
            $product = Product::updateOrCreate(
                ['id' => $this->productId],
                [
                    'name' => $this->name,
                    'category_id' => $this->category_id,
                    'description' => $this->description,
                    'sku' => $this->sku,
                    'articul' => $this->articul,
                    'stock_quantity' => 0, // Устанавливаем 0 по умолчанию
                    'status' => $this->status,
                    'images' => json_encode($photoPaths),
                    'barcode' => $this->barcode,
                ]
            );
            // dd($this->prices);

            foreach ($this->prices as $type => $price) {
                Price::updateOrCreate(
                    ['item_id' => $product->id, 'item_type' => 'product', 'price_type' => $type],
                    ['price' => $price, 'currency_id' => $this->defaultCurrencyId]
                );
            }

            session()->flash('success', $this->productId ? 'Товар успешно обновлен.' : 'Товар успешно добавлен.');
            $this->dispatch('updated');
            $this->resetForm();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                session()->flash('error', 'Штрих-код уже существует. Пожалуйста, используйте другой.');
            } else {
                session()->flash('error', 'Произошла ошибка при сохранении товара: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Произошла ошибка при сохранении товара: ' . $e->getMessage());
        }
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
        $prices = Price::where('item_id', $product->id)
            ->where('item_type', 'product')
            ->get();

        foreach ($prices as $price) {
            $this->prices[$price->price_type] = $price->price;
        }
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

        $this->reset();
    }

    public function deleteProduct($id)
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('deleted');
        $this->showForm = false;
        session()->flash('success', 'Товар успешно удален.');
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images); // Переиндексация массива
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


    public function createCategory()
    {
        $this->resetCategoryForm();
        $this->showCategoryForm = true;
    }

    // Метод для сброса формы категории
    public function resetCategoryForm()
    {
        $this->categoryName = '';
        $this->parentCategoryId = null;
        $this->showCategoryForm = false;
    }


    public function render()
    {
        return view('livewire.admin.products', [
            'products' => Product::paginate(10),
            'categories' => Category::all(),
        ]);
    }
}

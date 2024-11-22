<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;

class Categories extends Component
{
    public $name, $parent_id, $categoryId;
    public $showForm = false;


    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->parent_id = null;
        $this->showForm = false;
    }

    public function createCategory()
    {
        $this->resetForm();
        $this->showForm = true; // Открываем форму для создания
    }

    public function saveCategory()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ];

        $this->validate($rules);

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'parent_id' => $this->parent_id,
            ]
        );

        session()->flash('success', $this->categoryId ? 'Категория обновлена.' : 'Категория добавлена.');

        $this->resetForm();
    }


    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->parent_id = $category->parent_id;
        $this->showForm = true;
    }
    public function hasProducts($categoryId)
    {
        return \App\Models\Product::where('category_id', $categoryId)->exists();
    }


    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->showForm = false;
        session()->flash('success', 'Категория удалена.');
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'categories' => Category::with('parent')->get(),
            'allCategories' => Category::all(),
        ]);
    }
}

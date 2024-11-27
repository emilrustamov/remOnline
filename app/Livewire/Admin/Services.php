<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;

class Services extends Component
{
    use WithPagination;

    public $name, $description, $category_id, $serviceId, $status = true, $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'boolean',
        'category_id' => 'required|exists:categories,id',
    ];

    public function resetForm()
    {
        $this->serviceId = null;
        $this->name = '';
        $this->description = '';
        $this->status = true;
        $this->showForm = false;
        $this->category_id = null;
    }

    public function createService()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function saveService()
    {
        $this->validate();

        Service::updateOrCreate(
            ['id' => $this->serviceId],
            [
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status,
                'category_id' => $this->category_id,

            ]
        );

        session()->flash('success', $this->serviceId ? 'Услуга обновлена.' : 'Услуга добавлена.');
        $this->resetForm();
    }

    public function editService($id)
    {
        $service = Service::findOrFail($id);
        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->description = $service->description;
        $this->status = $service->status;
        $this->showForm = true;
        $this->category_id = $service->category_id;
    }

    public function deleteService($id)
    {
        Service::findOrFail($id)->delete();
        $this->dispatch('deleted');
        session()->flash('success', 'Услуга удалена.');
    }

    public function render()
    {
        return view('livewire.admin.services', [
            'services' => Service::paginate(10),
            'categories' => \App\Models\Category::all(),
        ]);
    }
}

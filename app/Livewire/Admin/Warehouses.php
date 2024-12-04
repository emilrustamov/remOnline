<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Warehouse;
use App\Models\User;

class Warehouses extends Component
{
    public $name, $warehouseId, $accessUsers = [], $showForm = false;

    public function resetForm()
    {
        $this->warehouseId = null;
        $this->name = '';
        $this->accessUsers = [];
        $this->showForm = false;
    }

    public function createWarehouse()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function saveWarehouse()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'accessUsers' => 'nullable|array',
            'accessUsers.*' => 'exists:users,id',
        ]);

        $warehouse = Warehouse::updateOrCreate(
            ['id' => $this->warehouseId],
            [
                'name' => $this->name,
                'access_users' => $this->accessUsers,
            ]
        );

        session()->flash('success', $this->warehouseId ? 'Склад обновлён.' : 'Склад создан.');
        $this->resetForm();
    }

    public function editWarehouse($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->warehouseId = $warehouse->id;
        $this->name = $warehouse->name;
        $this->accessUsers = $warehouse->access_users ?? [];
        $this->showForm = true;
    }

    public function deleteWarehouse($id)
    {
        Warehouse::findOrFail($id)->delete();
        session()->flash('success', 'Склад удалён.');
    }

    public function render()
    {
        return view('livewire.admin.warehouses', [
            'warehouses' => Warehouse::paginate(10),
            'users' => User::all(),
        ]);
    }
}

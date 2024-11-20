<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Role;
use App\Models\Permission;

class Roles extends Component
{
    public $roles;
    public $permissions;
    public $roleId;
    public $name;
    public $selectedPermissions = [];
    public $showForm = false;

    public function mount()
    {
        $this->roles = Role::with('permissions')->get();
        $this->permissions = Permission::all();
    }

    public function createRole()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->showForm = true;
    }

    public function saveRole()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->roleId,
            'selectedPermissions' => 'array',
        ]);

        $role = Role::updateOrCreate(
            ['id' => $this->roleId],
            ['name' => $this->name]
        );

        // Синхронизируем пермишены для роли
        $role->permissions()->sync($this->selectedPermissions);

        $this->resetForm();
        $this->roles = Role::with('permissions')->get(); // Обновляем список ролей
        session()->flash('message', 'Роль успешно сохранена.');
        session()->flash('type', 'success');
        $this->dispatch('refreshPage');
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach(); // Удаляем все пермишены, связанные с ролью
        $role->delete();

        $this->roles = Role::with('permissions')->get(); // Обновляем список ролей
        session()->flash('message', 'Роль успешно удалена.');
        session()->flash('type', 'success');
        $this->dispatch('refreshPage');
    }

    public function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->selectedPermissions = [];
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.admin.roles');
    }
}

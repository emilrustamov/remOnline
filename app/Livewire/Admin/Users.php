<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;

class Users extends Component
{
    public $users;
    public $roles;
    public $userId;
    public $showForm = false;
    public $name;
    public $email;
    public $password;
    public $roleId;
    public $hire_date; // Новое поле для даты приёма
    public $position;  // Новое поле для должности

    protected $listeners = ['editUser'];
    public $columns = [
        'id',
        'name',
        'email',
        'hire_date',
        'position',
        'role',
        'is_active',
        'created_at',
        'updated_at',
    ];


    public function mount()
    {
        $this->users = User::with('roles')->get();
        $this->roles = Role::all();
    }

    public function createUser()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roleId = $user->roles->first()->id ?? null; // Получаем ID первой роли
        $this->hire_date = $user->hire_date;
        $this->position = $user->position;
        $this->showForm = true;
    }

    public function saveUser()
    {
        $data = $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|min:8',
            'hire_date' => 'required|date',
            'position' => 'required|string',
            'roleId' => 'required|exists:roles,id',
        ]);

        $data['password'] = $this->password ? bcrypt($this->password) : User::find($this->userId)->password;

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $data['password'],
                'hire_date' => $this->hire_date,
                'position' => $this->position,
            ]
        );

        $this->dispatch('updated');
        $user->roles()->sync([$this->roleId]); // Синхронизируем с одной ролью
        $this->resetForm();
        $this->users = User::with('roles')->get(); // Обновляем список пользователей
    }

    public function deleteUser($userId)
    {
        if (!auth()->user()->hasPermission('delete_users')) {
            session()->flash('error', 'У вас нет прав для удаления пользователей.');
            return;
        }

        User::findOrFail($userId)->delete();
        $this->users = User::with('roles')->get(); // Обновляем список
        session()->flash('success', 'Пользователь успешно удален.');
        $this->dispatch('deleted');

        $this->dispatch('refreshPage');
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_active = !$user->is_active;
        $user->save();
        $this->users = User::with('roles')->get(); // Обновляем список пользователей
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->roleId = null; // Обнуляем роль
        $this->hire_date = null;
        $this->position = null;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.admin.users');
    }
}

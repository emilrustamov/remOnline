<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

class UsersTable extends DataTableComponent
{

    public $some = 'abc';

    public function changesome($id)
    {
        $this->some = 'das' . $id;
    }
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
                Column::make("Name", "name")->format(
                    fn($value, $row, Column $column) => '<a href="#" wire:click="$dispatch(\'editUser\', { userId: ' . $row->id . ' })">' . $row->name . '</a>'
                )->html()->sortable(),                
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Is active", "is_active")
                ->sortable(),
            Column::make("Hire date", "hire_date")
                ->sortable(),
            Column::make("Position", "position")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }


}

<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Role;

class RolesTable extends DataTableComponent
{
    // Указываем модель для таблицы
    protected $model = Role::class;

    // Конфигурируем таблицу (например, устанавливаем основной ключ)
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    // Определяем колонки таблицы
    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    // return '<a href="#">' . $row->name . '</a>';
                    return '<a href="#" wire:click="$dispatch(\'editRole\', { id: ' . $row->id . ' })">' . $row->name . '</a>';
                })
                ->html()
                // ->searchable()
                


            // Дополнительные колонки (если нужно)
            // Здесь вы можете добавить другие колонки, если они необходимы
        ];
    }

}

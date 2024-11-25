<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Client;

class ClientsTable extends DataTableComponent
{
    protected $model = Client::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),
                Column::make("First Name", "first_name")
                ->sortable()
                ->searchable()
                ->format(
                    fn($value, $row, Column $column) => '<a href="#" wire:click="$dispatch(\'editClient\', { id: ' . $row->id . ' })">' . $row->first_name . '</a>'
                )
                ->html(),
            Column::make("Last Name", "last_name")
                ->sortable()
                ->searchable(),
            Column::make("Contact Person", "contact_person")
                ->sortable()
                ->searchable(),
            Column::make("Address", "address")
                ->sortable(),
            Column::make("Note", "note")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable(),
            Column::make("Client Type", "client_type")
                ->sortable(),
            Column::make("Is Supplier", "is_supplier")
                ->sortable(),
            Column::make("Is Conflict", "is_conflict")
                ->sortable(),
        ];
    }
}

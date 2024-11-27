<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ClientsTable extends DataTableComponent
{
    protected $model = Client::class;
    // public ?string $defaultSortColumn = 'sort';
    // public string $defaultSortDirection = 'asc';
    // public bool $reorderEnabled = true;

    // public array $columnOrder = ['id', 'first_name', 'last_name', 'contact_person', 'address', 'note', 'status', 'client_type', 'is_supplier', 'is_conflict', 'sort'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        // $this->setReorderEnabled();
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

    // public function columns(): array
    // {
    //     $columns = [];

    //     foreach ($this->columnOrder as $column) {
    //         $columns[] = Column::make(ucwords(str_replace('_', ' ', $column)), $column)
    //             ->sortable()
    //             ->searchable();
    //     }

    //     return $columns;
    // }

    // public function reorder(array $items): void
    // {
    //     foreach ($items as $item) {
    //         // Обновляем поле 'sort' для каждого клиента на основе его 'id' и нового порядка
    //         optional(Client::find((int) $item['id']))->update(['sort' => (int) $item['sort']]);
    //     }

    //     // После обновления данных вызываем render для перерисовки таблицы
    //     $this->render();
    // }


    // public function updateColumnOrder(array $newOrder): void
    // {
    //     $this->columnOrder = $newOrder;
    //     session()->put('column_order', $newOrder); // Сохраняем порядок колонок в сессии
    //     $this->dispatch('columnOrderUpdated', $newOrder); // Отправляем событие для клиента
    //     $this->render();
    // }

    // protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    // {
    //     return Client::query()->orderBy('sort');
    // }
}

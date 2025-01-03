<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Client;
use App\Models\ClientsPhones;
use App\Models\ClientsEmails;
use Illuminate\Support\Facades\Auth;

class Clients extends Component
{
    public $clients;
    public $clientTypeFilter = 'all';
    public $supplierFilter = 'all';
    public $address;
    public $isSupplier = false;
    public $isConflict = false;
    public $contact_person;
    public $clientId;
    public $first_name;
    public $last_name;
    public $client_type;
    public $note;
    public $phones = [['number' => '', 'sms' => false]];
    public $emails = [];
    public $showForm = false;
    public $showConfirmationModal = false;
    protected $listeners = ['editClient'];
    public $columns = [
        'id',
        'first_name',
        'last_name',
        'client_type',
        'contact_person',
        'address',
        'note',
        'is_supplier',
        'is_conflict',
        'status',
    ];
    public function mount()
    {
        $this->clients = Client::with(['phones', 'emails'])->get();

        $this->loadClients();
    }

    public function openForm()
    {
        $this->resetForm();
        $this->showForm = true;
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
        $client = Client::find($this->clientId);

        return $this->first_name !== ($client->first_name ?? '') ||
            $this->last_name !== ($client->last_name ?? '') ||
            $this->client_type !== ($client->client_type ?? '') ||
            $this->address !== ($client->address ?? '') ||
            $this->contact_person !== ($client->contact_person ?? '') ||
            $this->note !== ($client->note ?? '') ||
            $this->isSupplier !== ($client->is_supplier ?? false) ||
            $this->isConflict !== ($client->is_conflict ?? false) ||
            $this->phones !== ($client->phones->map(function ($phone) {
                return [
                    'number' => $phone->phone,
                    'sms' => $phone->is_sms,
                ];
            })->toArray() ?? []) ||
            $this->emails !== ($client->emails->pluck('email')->toArray() ?? []);
    }



    public function saveClient()
    {
        $validatedData = $this->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'client_type' => 'required|string',
            'address' => 'nullable|string',
            'phones.*.number' => 'required|distinct|min:6',
            'emails.*' => 'nullable|email|distinct',
            'note' => 'nullable|string',
        ]);

        if (!Auth::user()->hasPermission('create_clients')) {
            $this->dispatch('error');
            return;
        }

        // Check for duplicate phone numbers
        foreach ($this->phones as $phone) {
            if (!empty($phone['number']) && ClientsPhones::where('phone', $phone['number'])->exists()) {
                $this->addError('phones.' . array_search($phone, $this->phones) . '.number', 'Phone number already exists.');
                return;
            }
        }

        $client = Client::updateOrCreate(
            ['id' => $this->clientId],
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'address' => $this->address,
                'client_type' => $this->client_type,
                'is_conflict' => $this->isConflict,
                'is_supplier' => $this->isSupplier,
                'contact_person' => $this->contact_person,
                'note' => $this->note,
            ]
        );

        $client->phones()->delete();
        $client->emails()->delete();

        foreach ($this->phones as $phone) {
            if (!empty($phone['number'])) {
                ClientsPhones::create([
                    'client_id' => $client->id,
                    'phone' => $phone['number'],
                    'is_sms' => $phone['sms'] ?? false,
                ]);
            }
        }

        foreach ($this->emails as $email) {
            if (!empty($email)) {
                ClientsEmails::create([
                    'client_id' => $client->id,
                    'email' => $email,
                ]);
            }
        }

        $this->resetForm();
        $this->clients = Client::with(['phones', 'emails'])->get();
        $this->dispatch('created');
        $this->dispatch('refreshPage');
    }

    public function deleteClient($id)
    {
        if (!Auth::user()->hasPermission('delete_clients')) {
            $this->dispatch('error');
            return;
        }

        $client = Client::findOrFail($id);
        $client->phones()->delete();
        $client->emails()->delete();
        $client->delete();

        $this->clients = Client::with(['phones', 'emails'])->get();
        $this->dispatch('deleted');
        $this->dispatch('refreshPage');
    }


    public function editClient($id)
    {
        if (!Auth::user()->hasPermission('edit_clients')) {
            $this->dispatch('error');
            session()->flash('message', 'У вас нет прав для редактирования клиентов.');
            session()->flash('type', 'error');
            return;
        }

        $client = Client::with(['phones', 'emails'])->findOrFail($id);
        $this->clientId = $client->id;
        $this->first_name = $client->first_name;
        $this->last_name = $client->last_name;
        $this->client_type = $client->client_type;
        $this->address = $client->address;
        $this->isConflict = $client->is_conflict;
        $this->isSupplier = $client->is_supplier;
        $this->contact_person = $client->contact_person;
        $this->note = $client->note;

        $this->phones = $client->phones->map(function ($phone) {
            return [
                'number' => $phone->phone,
                'sms' => $phone->is_sms,
            ];
        })->toArray();

        $this->emails = $client->emails->pluck('email')->toArray();

        $this->showForm = true;
    }

    public function addPhone()
    {
        $this->phones[] = ['number' => '', 'sms' => false];
    }

    public function addEmail()
    {
        $this->emails[] = '';
    }

    public function removePhone($index)
    {
        unset($this->phones[$index]);
        $this->phones = array_values($this->phones);
    }

    public function removeEmail($index)
    {
        unset($this->emails[$index]);
        $this->emails = array_values($this->emails);
    }

    public function resetForm()
    {
        $this->clientId = null;
        $this->first_name = '';
        $this->isSupplier = false;
        $this->isConflict = false;
        $this->last_name = '';
        $this->address = '';
        $this->client_type = '';
        $this->phones = [['number' => '', 'sms' => false]];
        $this->emails = [];
        $this->showForm = false;
        $this->contact_person = '';
        $this->note = '';
        $this->showForm = false;
    }


    public function loadClients()
    {
        $query = Client::query();

        if ($this->clientTypeFilter !== 'all') {
            $query->where('client_type', $this->clientTypeFilter);
        }

        if ($this->supplierFilter === 'suppliers') {
            $query->where('is_supplier', true);
        } elseif ($this->supplierFilter === 'clients') {
            $query->where('is_supplier', false);
        }

        $this->clients = $query->with(['phones', 'emails'])->get();

        // Отладка
        logger('Клиенты загружены:', $this->clients->toArray());
    }


    public function filterClients($type)
    {
        // Устанавливаем фильтры
        if (in_array($type, ['individual', 'company', 'all'])) {
            $this->clientTypeFilter = $type;
        }

        if (in_array($type, ['suppliers', 'clients', 'all'])) {
            $this->supplierFilter = $type;
        }

        // Загружаем клиентов
        $this->loadClients();

        // Обновляем видимость колонок
        $this->updateColumns();
    }

    public function updateColumns()
    {
        // Пример: скрывать/показывать колонки в зависимости от фильтров
        if ($this->clientTypeFilter === 'individual') {
            $this->columns = [
                'id',
                'first_name',
                'last_name',
                'contact_person',
                'address',
                'note',
            ];
        } elseif ($this->clientTypeFilter === 'company') {
            $this->columns = [
                'id',
                'first_name',
                'contact_person',
                'address',
                'is_supplier',
                'note',
            ];
        } else {
            $this->columns = [
                'id',
                'first_name',
                'last_name',
                'client_type',
                'contact_person',
                'address',
                'note',
                'is_supplier',
                'is_conflict',
                'status',
            ];
        }
    }




    public function render()
    {
        return view('livewire.admin.clients');
    }
}

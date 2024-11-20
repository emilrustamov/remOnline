<?php

namespace App\Livewire;

use Livewire\Component;

class ToastManager extends Component
{
    public $toast = [
        'show' => false,

    ];

    public function showToast($message, $type)
    {
        $this->toast['show'] = true;
    

        $this->dispatch('toast'); // Событие для отображения тоста
    }

    public function render()
    {
        return view('livewire.toast-manager');
    }
}

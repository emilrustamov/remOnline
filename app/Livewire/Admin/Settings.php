<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads; // Добавляем трейт для загрузки файлов
use App\Models\Setting;

class Settings extends Component
{
    use WithFileUploads; // Используем трейт

    public $companyName;
    public $companyLogo;

    public function mount()
    {
        $this->companyName = Setting::where('setting_name', 'company_name')->value('setting_value');
        $this->companyLogo = Setting::where('setting_name', 'company_logo')->value('setting_value');
    }

    public function saveSettings()
    {
        $this->validate([
            'companyName' => 'nullable|string|max:255',
            'companyLogo' => 'nullable|image|max:2048',
        ]);
    
        // Сохраняем название компании, если оно изменено или задано
        Setting::updateOrCreate(
            ['setting_name' => 'company_name'],
            ['setting_value' => $this->companyName]
        );
    
        // Сохраняем логотип, если он загружается
        if ($this->companyLogo && $this->companyLogo instanceof \Illuminate\Http\UploadedFile) {
            $logoPath = $this->companyLogo->store('logos', 'public');
    
            Setting::updateOrCreate(
                ['setting_name' => 'company_logo'],
                ['setting_value' => $logoPath]
            );
    
            // Обновляем путь к логотипу в компоненте для отображения
            $this->companyLogo = $logoPath;
        }
    
      
    
        // Перезагрузка страницы для обновления данных
        return redirect()->route('admin.settings.index'); // Поправьте маршрут, если он отличается
    }
    
    
    
    public function render()
    {
        return view('livewire.admin.settings');
    }
}

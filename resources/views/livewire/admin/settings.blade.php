<div class="container mx-auto p-4">
    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-4">Настройки</h1>

    <div class="mb-4">
        <label for="companyName" class="block text-sm font-medium">Название компании:</label>
        <input type="text" id="companyName" wire:model.defer="companyName" class="w-full p-2 border rounded">
        @error('companyName') <span class="text-red-500">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label for="companyLogo" class="block text-sm font-medium">Логотип компании:</label>
        <input type="file" id="companyLogo" wire:model="companyLogo" class="w-full p-2 border rounded">
        @if ($companyLogo instanceof \Illuminate\Http\UploadedFile)
            <img src="{{ $companyLogo->temporaryUrl() }}" alt="Предпросмотр логотипа" class="mt-2 h-20">
        @elseif ($companyLogo)
            <img src="{{ asset('storage/' . $companyLogo) }}" alt="Логотип компании" class="mt-2 h-20">
        @endif
        @error('companyLogo') <span class="text-red-500">{{ $message }}</span> @enderror
    </div>

    <button wire:click="saveSettings" class="bg-green-500 text-white px-4 py-2 rounded">Сохранить</button>
</div>

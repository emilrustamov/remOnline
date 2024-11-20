<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all(); // Получаем всех клиентов из базы данных
        return response()->json($clients);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'client_type' => 'required|string',
            // Добавьте правила валидации для телефонов и email, если нужно
        ]);

        $client = Client::create($validatedData);

        // Дополнительная логика для сохранения телефонов и email, если нужно
        
        return response()->json(['message' => 'Клиент успешно сохранен', 'client' => $client], 201);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // Первичный ключ
            $table->string('client_type'); // Тип клиента (например, индивидуальный, корпоративный)
            $table->boolean('is_supplier')->default(false); // Флаг поставщика
            $table->boolean('is_conflict')->default(false); // Флаг конфликта
            $table->string('first_name'); // Имя клиента
            $table->string('last_name'); // Фамилия клиента
            $table->string('contact_person')->nullable(); // Контактное лицо (может быть NULL)
            $table->text('address')->nullable(); // Адрес клиента
            $table->text('note')->nullable(); // Примечание
            $table->string('status')->default('active'); // Статус клиента
            $table->integer('order')->default(0);
            $table->timestamps(); // created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};

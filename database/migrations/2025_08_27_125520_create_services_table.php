<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id(); // PK
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // tukang
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // kategori jasa
            $table->string('title'); // nama jasa
            $table->text('description'); // deskripsi jasa
            $table->decimal('price', 12, 2)->nullable(); // harga jasa (nullable dulu)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
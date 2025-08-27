<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id(); // PK
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // jasa yang dinilai
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // user yang memberi rating
            $table->tinyInteger('rating'); // nilai rating, misal 1-5
            $table->text('comment')->nullable(); // komentar opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
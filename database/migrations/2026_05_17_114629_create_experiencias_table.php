<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('valoracion_web'); // 1-5 estrellas
            $table->json('secciones_visitadas'); // checkboxes
            $table->json('eventos_interes'); // select multiple
            $table->string('como_nos_encontraste'); // select simple
            $table->boolean('recomendaria'); // radio si/no
            $table->json('mejoras'); // select multiple
            $table->text('comentario')->nullable(); // textarea
            $table->boolean('volveria_comprar'); // radio
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiencias');
    }
};
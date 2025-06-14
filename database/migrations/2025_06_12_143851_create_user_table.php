<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            // $table->id();
            $table->string('username', 15)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->string('password', 15)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->string('email', 30)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->integer('usia', false, true)->length(10);
            $table->enum('jenis_kelamin', ['L', 'P'])->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->integer('tb', false, true)->length(10);
            $table->integer('bb', false, true)->length(10);
            $table->string('aktivitas', 30)->charset('utf8mb4')->collation('utf8mb4_general_ci');
            $table->integer('id_user', false, true)->length(10)->autoIncrement();
            $table->timestamps();

            // Index untuk kolom yang sering dicari
            $table->unique('username');
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // 'personal' или 'team'
            $table->timestamps();
        });

        // Вставляем базовые статусы
        DB::table('task_statuses')->insert([
            ['name' => 'Новая', 'slug' => 'new', 'description' => 'Новая задача', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'В работе', 'slug' => 'in_progress', 'description' => 'Задача в процессе выполнения', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Выполнено', 'slug' => 'completed', 'description' => 'Задача выполнена', 'type' => 'personal', 'created_at' => now(), 'updated_at' => now()],
            
            ['name' => 'Новая', 'slug' => 'team_new', 'description' => 'Новая командная задача', 'type' => 'team', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'В работе', 'slug' => 'team_in_progress', 'description' => 'Командная задача в процессе выполнения', 'type' => 'team', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Отправить на проверку', 'slug' => 'team_reviewing', 'description' => 'Командная задача отправлена на проверку', 'type' => 'team', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Выполнено', 'slug' => 'team_completed', 'description' => 'Командная задача выполнена', 'type' => 'team', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_statuses');
    }
}; 
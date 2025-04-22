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
        Schema::table('tasks', function (Blueprint $table) {
            // Добавляем поле для отслеживания прогресса задачи
            $table->integer('progress')->default(0)->after('status_id');
            
            // Добавляем поле для назначения задач отдельным пользователям
            $table->unsignedBigInteger('assigned_user_id')->nullable()->after('user_id');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
            
            // Добавляем поле для комментариев при отправке на доработку
            $table->text('feedback')->nullable()->after('progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['progress', 'assigned_user_id', 'feedback']);
        });
    }
}; 
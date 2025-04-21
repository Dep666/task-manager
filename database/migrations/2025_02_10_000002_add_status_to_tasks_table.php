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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id')->references('id')->on('task_statuses');
        });

        // Устанавливаем дефолтные статусы для существующих задач
        // Для персональных задач
        DB::table('tasks')
            ->whereNull('team_id')
            ->update(['status_id' => DB::table('task_statuses')->where('slug', 'new')->value('id')]);

        // Для командных задач
        DB::table('tasks')
            ->whereNotNull('team_id')
            ->update(['status_id' => DB::table('task_statuses')->where('slug', 'team_new')->value('id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
}; 
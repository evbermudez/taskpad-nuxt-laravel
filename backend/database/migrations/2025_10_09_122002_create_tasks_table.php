<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('statement');                 // short text
            $table->date('task_date');                   // group by date
            $table->unsignedInteger('position')->default(0);     // drag order
            $table->boolean('is_done')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id','task_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
        {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('completion_percentage')->default(0);
                $table->text('challenges')->nullable();
                $table->foreignId('manager_id')->constrained();
                $table->timestamps();
            });
        }

    
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

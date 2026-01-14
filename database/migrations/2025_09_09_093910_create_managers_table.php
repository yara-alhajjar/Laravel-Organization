<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
{
    Schema::create('managers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('number')->unique();
        $table->string('location');
        $table->foreignId('admin_id')->constrained();
        $table->timestamps();
    });
}

    
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};

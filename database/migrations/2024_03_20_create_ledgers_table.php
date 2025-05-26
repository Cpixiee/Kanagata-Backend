<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['COST OPERATION', 'REVENUE PROJECT', 'COST PROJECT', 'KAS MARGIN']);
            $table->string('budget')->nullable();
            $table->date('date');
            $table->enum('status', ['LISTING', 'PAID'])->default('LISTING');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledgers');
    }
}; 
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
            $table->foreignId('budget_id')->constrained('projects')->onDelete('cascade');
            $table->enum('sub_budget', [
                'BY PAYROLL',
                'BY PROJECT',
                'BY PAJAK',
                'SHAREHOLDER',
                'BY INVENTARIS',
                'BY SEWA',
                'BY TUTOR',
                'BY TAKIS'
            ]);
            $table->enum('recipient', [
                'rizal ramdhanu',
                'andar rahman',
                'fariz dandy',
                'adam',
                'wirakusuma'
            ]);
            $table->date('date');
            $table->string('month'); // Format: 'Jan 2025'
            $table->enum('status', ['listing', 'paid']);
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ledgers');
    }
}; 
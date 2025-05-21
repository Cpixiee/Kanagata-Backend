<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logsheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Basic Information
            $table->string('coa');
            $table->string('customer');
            $table->string('activity');
            $table->string('prodi');
            $table->string('grade');
            $table->integer('seq');

            // School/Revenue Details
            $table->integer('quantity_1');
            $table->decimal('rate_1', 12, 2);
            $table->decimal('revenue', 12, 2)->storedAs('quantity_1 * rate_1');
            $table->enum('ar_status', ['Listing', 'Paid', 'Pending']);

            // Tutor/Cost Details
            $table->string('tutor');
            $table->integer('quantity_2');
            $table->decimal('rate_2', 12, 2);
            $table->decimal('cost', 12, 2)->storedAs('quantity_2 * rate_2');
            $table->enum('ap_status', ['Listing', 'Paid', 'Pending']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logsheets');
    }
}; 
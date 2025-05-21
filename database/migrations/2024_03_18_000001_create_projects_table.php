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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('coa');
            $table->enum('customer', ['SMKN 20', 'SMKN 59', 'SMKN 43', 'SMKN 70', 'SMKN 22', 'SMKN 18', 'SMKN 37']);
            $table->enum('activity', ['INKUBASI', 'WORKSHOP', 'Kelas SDNR', 'Seminar', 'Sinkronisasi']);
            $table->enum('prodi', ['BD', 'RPL', 'MM', 'TKJ', 'GNRL']);
            $table->enum('grade', ['kelas 10', 'kelas 11', 'kelas 12', 'guru']);
            
            // Revenue Details
            $table->tinyInteger('quantity_1')->comment('Number of sessions/meetings');
            $table->integer('rate_1')->comment('Rate per session');
            $table->integer('gt_rev')->comment('Total revenue (quantity_1 * rate_1)');
            
            // Cost Details
            $table->integer('quantity_2')->comment('Number of sessions for cost');
            $table->integer('rate_2')->comment('Rate per session for cost');
            $table->integer('gt_cost')->comment('Total cost (quantity_2 * rate_2)');
            $table->integer('gt_margin')->comment('Profit margin (gt_rev - gt_cost)');
            
            // Accounts Receivable (AR)
            $table->integer('sum_ar')->default(0)->comment('Total AR from logsheet');
            $table->integer('ar_paid')->default(0)->comment('Paid AR from logsheet');
            $table->integer('ar_os')->default(0)->comment('Outstanding AR from logsheet');
            
            // Accounts Payable (AP)
            $table->integer('sum_ap')->default(0)->comment('Total AP from logsheet');
            $table->integer('ap_paid')->default(0)->comment('Paid AP from logsheet');
            $table->integer('ap_os')->default(0)->comment('Outstanding AP from logsheet');
            
            // Additional Fields
            $table->integer('todo')->comment('Todo status');
            $table->integer('ar_ap')->comment('AR AP value');
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('coa');
            $table->index('customer');
            $table->index('activity');
            $table->index('prodi');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

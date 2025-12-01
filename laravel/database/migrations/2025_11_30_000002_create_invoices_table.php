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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            // Match legacy table types: users.id and users_company.id are INT
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();
            // Link to legacy user_jobs table (job.id is INT)
            $table->integer('job_id')->nullable();
            $table->enum('status', ['Draft', 'Sent', 'Paid', 'Partial', 'Overdue', 'Cancelled'])->default('Draft');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->json('line_items')->nullable(); // Array of {description, qty, rate, amount}
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();

            // Indexes for foreign key lookups
            $table->index('user_id');
            $table->index('company_id');
            $table->index('job_id');
            $table->index('status');
            $table->index('due_date');
        });

        // Add invoice_id column to existing payment_history table
        Schema::table('payment_history', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('id');
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_history', function (Blueprint $table) {
            $table->dropIndex(['invoice_id']);
            $table->dropColumn('invoice_id');
        });

        Schema::dropIfExists('invoices');
    }
};

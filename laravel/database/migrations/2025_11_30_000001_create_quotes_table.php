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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number', 50)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('project_name', 255)->nullable();
            $table->enum('status', ['Draft', 'Review', 'Sent', 'Accepted', 'Rejected'])->default('Draft');
            // Print specs
            $table->string('stock', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->json('finishing')->nullable();
            // Price options (array of price breaks)
            $table->json('options')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('customer_message')->nullable();
            $table->timestamps();

            // Foreign keys (without cascade delete for safety)
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('company_id')->references('id')->on('users_company')->nullOnDelete();
        });

        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('project_name', 255)->nullable();
            $table->text('specs')->nullable();
            $table->enum('status', ['New', 'In Progress', 'Converted'])->default('New');
            $table->unsignedBigInteger('converted_quote_id')->nullable();
            $table->timestamps();

            $table->foreign('converted_quote_id')->references('id')->on('quotes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
        Schema::dropIfExists('quotes');
    }
};

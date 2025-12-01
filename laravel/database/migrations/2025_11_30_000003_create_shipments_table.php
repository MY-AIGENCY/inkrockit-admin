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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 100)->nullable();
            // Match legacy table types: user_jobs.id is INT
            $table->integer('job_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('carrier', 50)->nullable(); // UPS, FedEx, USPS, etc.
            $table->enum('status', ['Pending', 'Manifested', 'In Transit', 'Out for Delivery', 'Delivered', 'Exception'])->default('Pending');
            $table->string('service_type', 100)->nullable(); // Ground, 2-Day, Overnight, etc.
            // Address fields
            $table->string('recipient_name', 255)->nullable();
            $table->string('address_line1', 255)->nullable();
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 50)->default('US');
            // Package details
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions', 50)->nullable(); // LxWxH
            $table->text('contents_description')->nullable();
            // Dates
            $table->date('ship_date')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            // Cost
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for foreign key lookups
            $table->index('job_id');
            $table->index('user_id');
            $table->index('company_id');
            $table->index('tracking_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

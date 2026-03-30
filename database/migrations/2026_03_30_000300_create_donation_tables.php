<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name');
            $table->string('donor_email')->nullable();
            $table->string('donor_phone', 32)->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('note')->nullable();
            $table->enum('payment_method', ['midtrans', 'transfer', 'cash'])->default('midtrans');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->json('payment_payload')->nullable();
            $table->string('bank_transfer_proof_path')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('receipt_number')->nullable()->unique();
            $table->string('receipt_pdf_path')->nullable();
            $table->dateTime('donated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('donation_verification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('acted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action', ['approve', 'reject']);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_verification_logs');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_categories');
    }
};

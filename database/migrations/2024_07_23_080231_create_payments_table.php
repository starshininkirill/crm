<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->decimal('value', 15, 2);
            $table->string('inn')->nullable();
            $table->integer('status')->default(Payment::STATUS_WAIT);
            $table->integer('type')->nullable();
            $table->integer('order')->nullable()->default(1);
            $table->string('operation_id')->nullable()->unique();
            $table->boolean('is_technical')->nullable()->default(false);
            $table->string('description')->nullable();
            $table->string('receipt_url')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
 
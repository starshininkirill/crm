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
            $table->float('value');
            $table->integer('status')->default(Payment::STATUS_WAIT);
            $table->integer('type')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('is_technical')->nullable()->default(false);
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // Временное решение
            // заменить
            $table->integer('descr')->nullable();
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
 
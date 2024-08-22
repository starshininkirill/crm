<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public const OPEN = 'open';
    public const CLOSE = 'close';

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('value');
            $table->integer('order');
            $table->string('status')->default(self::OPEN);
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('responsible_id')->nullable()->constrained('users')->onDelete('cascade');
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

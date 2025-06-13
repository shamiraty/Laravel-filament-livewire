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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Jina la asset
            $table->string('asset_type'); // Aina ya asset (electronic, bango, generator, fire extinguisher, etc.)
            $table->string('location')->nullable(); // Location au ofisi ilipo
            $table->string('registration_number')->unique()->nullable(); // Registration number (hakikisha ni unique)
            $table->date('purchase_date')->nullable(); // Tarehe iliyonunuliwa
            $table->decimal('purchase_price', 10, 2)->nullable(); // Bei iliyonunuliwa
            $table->string('custodian')->nullable(); // Jina la anaesimamia
            $table->string('status')->default('Good'); // Status yake (nzima, mbovu, Under Maintenance)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

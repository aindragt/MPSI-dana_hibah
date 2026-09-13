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
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('organization_name');
            $table->text('address');
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->string('field_of_activity')->nullable();
            $table->string('chairman_name')->nullable();
            $table->string('secretary_name')->nullable();
            $table->string('treasurer_name')->nullable();
            $table->string('organization_phone', 30)->nullable();
            $table->string('organization_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};

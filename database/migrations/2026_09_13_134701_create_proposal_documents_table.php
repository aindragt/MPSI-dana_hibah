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
        Schema::create('proposal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained('document_types')->onDelete('restrict');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 50);
            $table->unsignedInteger('file_size');
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamps();

            $table->unique(['proposal_id', 'document_type_id', 'version']);
            $table->index('proposal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_documents');
    }
};

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
        Schema::create('document_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_document_id')->constrained('proposal_documents')->onDelete('cascade');
            $table->foreignId('verified_by')->constrained('users')->onDelete('restrict');
            $table->string('verification_type', 20);
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['proposal_document_id', 'verification_type'], 'doc_verifications_doc_id_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_verifications');
    }
};

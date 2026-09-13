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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('submission_window_id')->constrained('submission_windows')->onDelete('restrict');
            $table->string('activity_title');
            $table->text('activity_description')->nullable();
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->date('execution_start_date')->nullable();
            $table->date('execution_end_date')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_online_at')->nullable();
            $table->timestamp('physical_docs_received_at')->nullable();
            $table->timestamp('final_verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('lpj_file')->nullable();
            $table->string('lpj_status')->nullable();
            $table->text('lpj_catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'submission_window_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};

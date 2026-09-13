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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->string('nama_ketua')->nullable();
            $table->string('no_wa', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('file_akta')->nullable();
            $table->string('file_kesbangpol')->nullable();
            $table->string('rekening_lembaga')->nullable();
            $table->string('npwp_lembaga')->nullable();

            $table->index('role_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['created_by']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'role_id',
                'created_by',
                'is_active',
                'nama_ketua',
                'no_wa',
                'alamat',
                'foto_profil',
                'file_akta',
                'file_kesbangpol',
                'rekening_lembaga',
                'npwp_lembaga',
            ]);
        });
    }
};

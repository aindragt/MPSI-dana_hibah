<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'id' => 1,
                'name' => 'Surat Permohonan Bantuan Hibah',
                'slug' => 'surat-permohonan',
                'description' => 'TTD Ketua & Sekretaris',
                'sort_order' => 1,
                'is_required' => true,
            ],
            [
                'id' => 2,
                'name' => 'Fotokopi Bukti Legalitas / SK',
                'slug' => 'bukti-legalitas',
                'description' => 'SK dari instansi berwenang',
                'sort_order' => 2,
                'is_required' => true,
            ],
            [
                'id' => 3,
                'name' => 'Akta Menkumham / Akta Pendiri',
                'slug' => 'akta-pendirian',
                'description' => 'Bukti legalitas pendirian',
                'sort_order' => 3,
                'is_required' => true,
            ],
            [
                'id' => 4,
                'name' => 'Fotokopi Rekening Bank',
                'slug' => 'rekening-bank',
                'description' => 'Atas nama lembaga',
                'sort_order' => 4,
                'is_required' => true,
            ],
            [
                'id' => 5,
                'name' => 'Fotokopi KTP Pengurus',
                'slug' => 'fotokopi-ktp',
                'description' => 'KTP Ketua, Sekretaris, Bendahara',
                'sort_order' => 5,
                'is_required' => true,
            ],
            [
                'id' => 6,
                'name' => 'RAB (Rencana Anggaran Biaya)',
                'slug' => 'rab',
                'description' => 'TTD Ketua & Bendahara',
                'sort_order' => 6,
                'is_required' => true,
            ],
            [
                'id' => 7,
                'name' => 'Fotokopi NPWP',
                'slug' => 'npwp',
                'description' => 'NPWP lembaga',
                'sort_order' => 7,
                'is_required' => true,
            ],
            [
                'id' => 8,
                'name' => 'Surat Pernyataan Tanggung Jawab Permohonan',
                'slug' => 'sptjp',
                'description' => 'Bermaterai Rp10.000',
                'sort_order' => 8,
                'is_required' => true,
            ],
            [
                'id' => 9,
                'name' => 'Surat Pernyataan Tanggung Jawab Penggunaan',
                'slug' => 'sptj-penggunaan',
                'description' => 'Bermaterai Rp10.000',
                'sort_order' => 9,
                'is_required' => true,
            ],
            [
                'id' => 10,
                'name' => 'Pakta Integritas',
                'slug' => 'pakta-integritas',
                'description' => null,
                'sort_order' => 10,
                'is_required' => true,
            ],
            [
                'id' => 11,
                'name' => 'Surat Keterangan Domisili Lembaga',
                'slug' => 'sk-domisili',
                'description' => 'Diketahui Desa/Kelurahan',
                'sort_order' => 11,
                'is_required' => true,
            ],
        ];

        DB::table('document_types')->upsert($documents, ['id'], ['name', 'slug', 'description', 'sort_order', 'is_required']);
    }
}

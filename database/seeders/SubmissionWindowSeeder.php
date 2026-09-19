<?php

namespace Database\Seeders;

use App\Models\SubmissionWindow;
use Illuminate\Database\Seeder;

class SubmissionWindowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubmissionWindow::updateOrCreate(
            ['year' => 2026],
            [
                'open_date' => '2026-01-01',
                'close_date' => '2026-05-31',
                'is_active' => true,
            ]
        );
    }
}

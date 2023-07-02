<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ["locale" => "en", "language" => "English"],
            ["locale" => "hr", "language" => "Croatian"],
        ];
        Language::factory()
            ->count(count($languages))
            ->sequence(...$languages)
            ->create();
    }
}

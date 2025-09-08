<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SideEffectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sideEffects = [
            ['name' => '頭痛', 'category' => '神経系'],
            ['name' => '吐き気', 'category' => '消化器系'],
            ['name' => 'めまい', 'category' => '神経系'],
            ['name' => '疲労感', 'category' => '全身症状'],
            ['name' => '発疹', 'category' => '皮膚症状'],
            ['name' => '下痢', 'category' => '消化器系'],
            ['name' => '便秘', 'category' => '消化器系'],
            ['name' => '食欲不振', 'category' => '消化器系'],
            ['name' => '不眠', 'category' => '神経系'],
            ['name' => '眠気', 'category' => '神経系'],
            ['name' => '口の渇き', 'category' => '全身症状'],
            ['name' => '動悸', 'category' => '循環器系'],
            ['name' => '息切れ', 'category' => '呼吸器系'],
            ['name' => '筋肉痛', 'category' => '筋骨格系'],
            ['name' => '関節痛', 'category' => '筋骨格系'],
            ['name' => 'その他', 'category' => 'その他'],
        ];

        foreach ($sideEffects as $sideEffect) {
            DB::table('side_effect_types')->insert([
                'name' => $sideEffect['name'],
                'category' => $sideEffect['category'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

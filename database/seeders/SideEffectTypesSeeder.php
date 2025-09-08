<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SideEffectTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sideEffects = [
            // 消化器系
            ['name' => '吐き気', 'category' => '消化器系', 'description' => '胃の不快感や嘔吐感'],
            ['name' => '下痢', 'category' => '消化器系', 'description' => '軟便や水様便'],
            ['name' => '便秘', 'category' => '消化器系', 'description' => '排便困難'],
            ['name' => '腹痛', 'category' => '消化器系', 'description' => '腹部の痛みや不快感'],
            ['name' => '食欲不振', 'category' => '消化器系', 'description' => '食欲の減退'],
            
            // 神経系
            ['name' => '頭痛', 'category' => '神経系', 'description' => '頭部の痛み'],
            ['name' => 'めまい', 'category' => '神経系', 'description' => 'ふらつきや平衡感覚の異常'],
            ['name' => '眠気', 'category' => '神経系', 'description' => '強い眠気や倦怠感'],
            ['name' => '不眠', 'category' => '神経系', 'description' => '睡眠障害'],
            ['name' => '集中力低下', 'category' => '神経系', 'description' => '注意力や集中力の低下'],
            
            // 皮膚系
            ['name' => '発疹', 'category' => '皮膚系', 'description' => '皮膚の赤みやかゆみ'],
            ['name' => 'かゆみ', 'category' => '皮膚系', 'description' => '皮膚のかゆみ'],
            ['name' => '乾燥', 'category' => '皮膚系', 'description' => '皮膚の乾燥'],
            
            // 循環器系
            ['name' => '動悸', 'category' => '循環器系', 'description' => '心拍数の増加や不整脈'],
            ['name' => '血圧低下', 'category' => '循環器系', 'description' => '血圧の低下'],
            ['name' => '血圧上昇', 'category' => '循環器系', 'description' => '血圧の上昇'],
            
            // その他
            ['name' => '疲労感', 'category' => 'その他', 'description' => '体のだるさや疲れ'],
            ['name' => '発熱', 'category' => 'その他', 'description' => '体温の上昇'],
            ['name' => '筋肉痛', 'category' => 'その他', 'description' => '筋肉の痛みやこわばり'],
            ['name' => '関節痛', 'category' => 'その他', 'description' => '関節の痛みや腫れ'],
        ];

        foreach ($sideEffects as $effect) {
            DB::table('side_effect_types')->insert([
                'name' => $effect['name'],
                'category' => $effect['category'],
                'description' => $effect['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "Side effect types seeded successfully!\n";
    }
}

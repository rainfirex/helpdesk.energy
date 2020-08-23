<?php

use Illuminate\Database\Seeder;

class CategoryTicket extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_tickets')->insert([
            ['title' => 'Компьютеры'],
            ['title' => 'Сеть'],
            ['title' => 'Телефония'],
            ['title' => 'Программы'],
            ['title' => 'MS Office'],
            ['title' => 'Расходники'],
            ['title' => 'Другое']
        ]);
    }
}

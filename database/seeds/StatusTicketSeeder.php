<?php

use Illuminate\Database\Seeder;

class StatusTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_tickets')->insert([
            [
                'status' => 'untouched',
                'title'  => 'Не начато'
            ],
            [
                'status' => 'performed',
                'title'  => 'Выполняется'
            ],
            [
                'status' => 'completed',
                'title'  => 'Завершено'
            ],
            [
                'status' => 'rejected',
                'title'  => 'Отклонено'
            ]
        ]);
    }
}

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
                'title'  => 'Не начато',
                'description' => 'Заявка была создана, но не назначена на исполнение. Позволяет писать комментарии и сменить статус на другой.'
            ],
            [
                'status' => 'performed',
                'title'  => 'Выполняется',
                'description' => 'Исполнение заявки, назначает сотрудника ОИТ как исполнителя. Позволяет писать комментарии и сменить статус на другой.'
            ],
            [
                'status' => 'completed',
                'title'  => 'Завершено',
                'description' => 'Завершает заявку. Запрещает писать комментарии и менять статус.'
            ],
            [
                'status' => 'rejected',
                'title'  => 'Отклонено',
                'description' => 'Игнорировании заявки. Запрещает писать комментарии, но можно сменить статус на другой.'
            ]
        ]);
    }
}

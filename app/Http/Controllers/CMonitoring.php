<?php namespace App\Http\Controllers {

    use App\Ticket;

    class CMonitoring extends Controller
    {
        public function __invoke(int $number = 000000)
        {
            $err = '';
            $ticket = Ticket::where('number', $number)->first();
            if (!empty($ticket)) {
                $ticket->load([
                    'performerUser' => function ($query) {
                        $query->select('id', 'email', 'name', 'phone', 'title');
                    },
                    'statusTicket' => function ($query) {
                        $query->select('title', 'status', 'id');
                    },
                    'comments' => function ($query) {
                        $query->select('created_at', 'description', 'ticket_id');
                        $query->orderBy('created_at', 'desc');
                        $query->limit(20);
                    }]);

            } else
                $err = 'Заявка не найдена.';

            return view('monitoring', compact('ticket', 'err'));
        }
    }
}
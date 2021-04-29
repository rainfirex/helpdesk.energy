<?php namespace App\Services {

    use App\CommentTicket;
    use App\StatusTicket;
    use App\Ticket;
    use App\User;
    use Illuminate\Http\Request;

    class HandlerTicketService
    {
        /**
         * @var
         */
        private $message;

        /**
         * @return string
         */
        public function getMessage(): string {
            return $this->message;
        }

        /**
         * @param Request $request
         * @param User $user
         * @return Ticket|null
         */
        public function changeStatusTicket(Request $request, User $user): ?Ticket {
            $ticket = Ticket::find($request->input('ticket_id'));

            if ($ticket->status_id === StatusTicket::ST_COMPLETED){
                $this->message = 'Эта заявка уже завершена.';
                return null;
            }

            // Если мастера заявки не тек. пользователь тогда
            if ($ticket->master_user_id != $user->id) {
                // Если нет исполнителя заявки тогда,
                if ($ticket->performer_user_id != null) {
                    // Если исполнитель не тек. пользователь - Отказ
                    if ($ticket->performer_user_id != $user->id) {
                        $this->message = 'Отказ, вы не можете менять статус т.к. заявка вам не назначена.';
                        return null;
                    }
                }
            }

            $status = StatusTicket::find($request->input('status'));
            $ticket->status_id = $status->id;
            $ticket->performer_user_id = $user->id;
            $ticket->save();
            $ticket->load([
                'statusTicket' => function ($query) {
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone', 'title');
                },
                'performerUser' => function ($query) {
                    $query->select('id', 'name', 'email');
                },
                'masterUser' => function ($query) {
                    $query->select('id', 'name', 'email');
                },
                'comments' => function ($query) {
                    $query->select('ticket_id', 'description', 'created_at', 'user_id');
                    $query->orderBy('id', 'DESC');
                }
            ]);

            CommentTicket::create([
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'description' => sprintf('Статус заявки изменен на: "%s"', $status->title),
                'is_handler' => true,
                'is_new' => true
            ]);

            return $ticket;
        }
    }
}
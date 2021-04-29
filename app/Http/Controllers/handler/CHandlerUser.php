<?php namespace App\Http\Controllers\handler {

    use App\Mail\FeedMail;
    use App\Mail\MailHandlerAssign;
    use App\Ticket;
    use App\User;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;

    class CHandlerUser extends Controller
    {
        /**
         * CHandlerUser constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('handlers', 'assign');
            $this->middleware('check.handler');
        }

        /**
         * Пользоват. зарегистр. в системе, которые могут обрабатывать заявки
         * @return JsonResponse
         */
        public function handlers(): JsonResponse{
            $handlers = User::select('id','name','email','department','title')
                ->where('is_handler','=', 1)
                ->whereNotIn('id', [Auth::user()->id])
                ->get();

            return response()->json(compact('handlers'));
        }

        /**
         * Назначить исполнителя на заявку
         * @param int $handlerId
         * @param int $ticketId
         * @return JsonResponse
         */
        public function assign(int $handlerId, int $ticketId): JsonResponse{
            $currUser = Auth::user();
            $ticket = Ticket::find($ticketId);
            $ticket->performer_user_id = $handlerId; //Наз. исполнитель
            $ticket->master_user_id = $currUser->id; //Указ. кто назначил
            $result = $ticket->save();

            if($result) {
                $handler = User::find($handlerId);
                //Уведомление автору заявки
                if (!empty($ticket->user->email)) {
                    $msg = sprintf('%s, вашей заявке № %s назначен новый исполнитель: %s.', $ticket->user->name, $ticket->number, $handler->name);
                    Mail::to($ticket->user->email)->send(new FeedMail('mail', 'Назначен новый исполнитель', [
                        'title' => 'Новый исполнитель',
                        'body'  => $msg,
                        'file'  =>  null
                    ]));
                }

                //Уведомление исполнителю заявки
                if(!empty($handler->email)){
                    Mail::to($handler->email)->send(new MailHandlerAssign('mail.handler-assign', 'Вы назначены новым исполнителем', [
                        'title' => null,
                        'body'  => null,
                        'file'  => null,
                        'currUser' => $currUser->name,
                        'ticketNumber' => $ticket->number,
                        'ticketId' => $ticketId
                    ]));
                }
            }
            $performerUser = User::select('id', 'name', 'email')
                ->where('id','=', $handlerId)
                ->first();
            $masterUser = User::select('id', 'name', 'email')
                ->where('id','=', $currUser->id)
                ->first();

            return response()->json([
                'success' => $result,
                'performer_user' => $performerUser,
                'master_user' => $masterUser
            ]);
        }
    }
}
<?php namespace App\Services {

    use App\CommentTicket;
    use App\DocFile;
    use App\ScreenshotFile;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\Request;

    class UserTicketService
    {
        /**
         * @param Request $request
         * @param $user
         * @return Ticket
         */
        public function storeTicket(Request $request, $user): Ticket {
                $ticket = Ticket::create([
                    'user_id'     => $user->id,
                    'number'      => uniqid('ticket.', true),
                    'phone'       => $request->input('phone'),
                    'department'  => $request->input('department'),
                    'category'    => $request->input('category'),
                    'title'       => $request->input('title'),
                    'description' => $request->input('description'),
                    'status_id'   => StatusTicket::ST_UNTOUCHED,
                    'is_new'      => true
                ]);

                $ticket->number = Ticket::CreateNumber($ticket->id);
                $result = $ticket->save();

                if ($result){
                    $files = $request->file();
                    if (!empty($files)) {
                        $dirStorageApp = storage_path('app/public/');
                        $dirScreenshots = 'screenshots/';
                        $dirDocs = 'docs/';
                        $dirScreenshotToday = $dirStorageApp.$dirScreenshots.date('d.m.Y');
                        $dirDocToday = $dirStorageApp.$dirDocs.date('d.m.Y');

                        if (!file_exists($dirStorageApp.$dirScreenshots)) {
                            mkdir($dirStorageApp.$dirScreenshots);
                            chmod($dirStorageApp.$dirScreenshots, 0777);
                        }

                        if (!file_exists($dirStorageApp.$dirDocs)) {
                            mkdir($dirStorageApp.$dirDocs);
                            chmod($dirStorageApp.$dirDocs, 0777);
                        }

                        if (!file_exists($dirScreenshotToday)) {
                            mkdir($dirScreenshotToday);
                            chmod($dirScreenshotToday, 0777);
                        }

                        if (!file_exists($dirDocToday)) {
                            mkdir($dirDocToday);
                            chmod($dirDocToday, 0777);
                        }

                        foreach ($files as $file){
                            if (in_array($file->extension(),['png', 'jpg', 'jpeg'])) {
                                $path = $file->store('public/'.$dirScreenshots.date('d.m.Y'));
                                chmod(storage_path('app/').$path, 0777);

                                ScreenshotFile::create([
                                    'path' => $path,
                                    'url' => asset('storage/'.$dirScreenshots.date('d.m.Y').'/'.$file->hashName()),
                                    'name' => $file->getClientOriginalName(),
                                    'mime_type' => $file->getMimeType(),
                                    'user_id'   => $user->id,
                                    'ticket_id' => $ticket->id
                                ]);
                            }

                            if(in_array($file->extension(), ['txt', 'text', 'pdf', 'docx', 'xlsx', 'pptpx'])){
                                $path = $file->store('public/'.$dirDocs.date('d.m.Y'));
                                chmod(storage_path('app/').$path, 0777);

                                DocFile::create([
                                    'path' => $path,
                                    'url' => asset('storage/'.$dirDocs.date('d.m.Y').'/'.$file->hashName()),
                                    'name' => $file->getClientOriginalName(),
                                    'mime_type' => $file->getMimeType(),
                                    'user_id'   => $user->id,
                                    'ticket_id' => $ticket->id
                                ]);
                            }
                        }
                    }
                }
                return $ticket;
        }
    }
}
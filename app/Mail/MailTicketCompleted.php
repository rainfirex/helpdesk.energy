<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTicketCompleted extends Mailable
{
    use Queueable, SerializesModels;

    private $username;
    private $ticket_number;
    private $title;

    public function __construct($username, $title, $ticket_number)
    {
        $this->username = $username;
        $this->title = $title;
        $this->ticket_number = $ticket_number;
    }

    public function build() {
        return $this->view('mail.user-completed-ticket')->subject("Заявка в OIT, завершена автором")->with([
            'username'      => $this->username,
            'title'         => $this->title,
            'ticket_number' => $this->ticket_number
        ]);
    }
}

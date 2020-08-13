<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTicketCreate extends Mailable
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
        return $this->view('mail.create-ticket')->subject("Создана заявка в OIT")->with([
            'username'      => $this->username,
            'title'         =>  $this->title,
            'ticket_number' => $this->ticket_number
        ]);
    }
}

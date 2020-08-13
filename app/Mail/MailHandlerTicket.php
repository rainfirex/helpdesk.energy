<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailHandlerTicket extends Mailable
{
    use Queueable, SerializesModels;

    private $username;
    private $ticket_number;
    private $title;
    private $status_name;
    private $performer_user;

    public function __construct($username, $title, $ticket_number, $status_name, $performer_user)
    {
        $this->username       = $username;
        $this->title          = $title;
        $this->ticket_number  = $ticket_number;
        $this->status_name    = $status_name;
        $this->performer_user = $performer_user;
    }

    public function build() {
        return $this->view('mail.handler-change-status-ticket')->subject("Заявка в OIT, новый статус")->with([
            'username'       => $this->username,
            'title'          => $this->title,
            'ticket_number'  => $this->ticket_number,
            'status_name'    => $this->status_name,
            'performer_user' => $this->performer_user
        ]);
    }
}

<?php


namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailHandlerCommentNew extends Mailable
{
    use Queueable, SerializesModels;

    private $username;
    private $ticket_number;
    private $title;
    private $status_name;
    private $user_comment;
    private $description;

    public function __construct($username, $title, $ticket_number, $user_comment, $description)
    {
        $this->username       = $username;
        $this->title          = $title;
        $this->ticket_number  = $ticket_number;
        $this->user_comment   = $user_comment;
        $this->description   = $description;
    }

    public function build() {
        return $this->view('mail.handler-comment-new')->subject("Новый комментарий к заявке")->with([
            'username'      => $this->username,
            'title'         => $this->title,
            'ticket_number' => $this->ticket_number,
            'user_comment'  => $this->user_comment,
            'description'   => $this->description
        ]);
    }
}

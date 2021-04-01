<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailHandlerAssign extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    public $view;

    public $subject;

    /**
     * MailHandlerAssign constructor.
     * @param $view
     * @param $subject
     * @param $feedback
     */
    public function __construct($view, $subject, $feedback)
    {
        $this->view = $view;
        $this->subject = $subject;

        $this->feedback = $feedback;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (isset($this->feedback['file'])){
            $email = $this->view($this->view)->subject($this->subject)->with([
                'title' => $this->feedback['title'],
                'body'  => $this->feedback['body'],
                'currUser' => $this->feedback['currUser'],
                'ticketNumber' => $this->feedback['ticketNumber'],
                'ticketId' => $this->feedback['ticketId']
            ]);

            foreach($this->feedback['file'] as $filePath){
                $email->attach($filePath);
            }
            return $email;
        }
        else
        {
            return $this->view($this->view)->subject($this->subject)->with([
                'title' => $this->feedback['title'],
                'body'  => $this->feedback['body'],
                'currUser' => $this->feedback['currUser'],
                'ticketNumber' => $this->feedback['ticketNumber'],
                'ticketId' => $this->feedback['ticketId']
            ]);
        }
    }
}
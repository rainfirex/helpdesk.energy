<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    public $view;

    public $subject;

    /**
     * * Create a new message instance.
     * @param $feedback
     * FeedMail constructor.
     * @param $view
     * @param $subject
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
                'body'  => $this->feedback['body']
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
                'body'  => $this->feedback['body']
            ]);
        }
    }
}

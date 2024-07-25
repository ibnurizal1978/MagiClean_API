<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailOtp extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@magicleanfortunetown.com')
                   ->view('email/emailOtp')
                   ->subject('OTP Alert –Running Tiger, Hidden Dirt- Do Not Reply')
                   ->with(
                    [
                        'otp_code' => $this->data,
                    ]);
    }
}

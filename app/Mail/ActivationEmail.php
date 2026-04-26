<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ActivationEmail extends Mailable
{
    public $nama;
    public $activationUrl;

    public function __construct($nama, $activationUrl)
    {
        $this->nama = $nama;
        $this->activationUrl = $activationUrl;
    }

    public function build()
    {
        return $this->subject('Aktivasi Akun Centrivo')
                    ->view('auth.activation');
    }
}
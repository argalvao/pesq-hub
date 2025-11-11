<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActivatedMail extends Mailable
{
    use SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Conta Ativada - PesqHub UEFS')
                    ->view('emails.user-activated')
                    ->with([
                        'nome_usuario' => $this->user->nome,
                        'email_usuario' => $this->user->email,
                        'tipo_permissao' => $this->user->tipo_permissao,
                        'data_ativacao' => now()->format('d/m/Y'),
                        'hora_ativacao' => now()->format('H:i'),
                        'login_url' => config('app.url') . '/login'
                    ]);
    }
}

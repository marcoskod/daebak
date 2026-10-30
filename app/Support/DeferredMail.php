<?php

namespace App\Support;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Envoie un email APRÈS l'envoi de la réponse HTTP au client : le formulaire répond
 * instantanément au lieu d'attendre le serveur SMTP, et une panne SMTP est journalisée
 * sans jamais faire échouer une commande/un diagnostic déjà enregistré.
 */
class DeferredMail
{
    public static function send(string $email, ?string $name, Mailable $mailable): void
    {
        app()->terminating(function () use ($email, $name, $mailable) {
            try {
                Mail::to($email, $name)->send($mailable);
            } catch (\Throwable $e) {
                report($e);
            }
        });
    }
}

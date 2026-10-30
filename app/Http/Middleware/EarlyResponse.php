<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Permet au navigateur de considérer la réponse comme terminée dès réception du corps,
 * même si du travail différé (envoi d'emails) se poursuit ensuite côté serveur.
 */
class EarlyResponse
{
    public function handle(Request $request, Closure $next)
    {
        ignore_user_abort(true);

        $response = $next($request);

        if (! $response instanceof StreamedResponse && ! $response->headers->has('Content-Length')) {
            $content = $response->getContent();
            if (is_string($content)) {
                $response->headers->set('Content-Length', (string) strlen($content));
                $response->headers->set('Connection', 'close');
            }
        }

        return $response;
    }
}

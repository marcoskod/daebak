<?php

namespace App\Services;

use App\Models\Order;

/**
 * Passerelle de paiement. Tant qu'aucune clé n'est configurée (STRIPE_SECRET_KEY / PayPal), le service
 * fonctionne en mode DÉMO : aucun appel réseau, aucune donnée bancaire — le parcours est simulé.
 * Pour brancher Stripe : implémenter createStripeSession() puis renvoyer son URL dans createCheckout().
 */
class PaymentService
{
    public function isLive(): bool
    {
        return filled(config('services.stripe.secret_key'));
    }

    /** @return array{url: string, reference: string, simulated: bool} */
    public function createCheckout(Order $order): array
    {
        // if ($this->isLive()) { return $this->createStripeSession($order); }

        return [
            'url' => route('checkout.demo', $order),
            'reference' => 'DEMO-'.$order->order_number,
            'simulated' => true,
        ];
    }
}

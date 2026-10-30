<?php

return [
    // Multiplie les limites anti-spam des formulaires (1 en production ; élevé pour les tests de charge).
    'throttle_multiplier' => max(1, (int) env('SHOP_THROTTLE_MULTIPLIER', 1)),
];

# Daebak 대박 — boutique print-on-demand d'inspiration coréenne

Laravel 12 + Filament 3 · Blade · CSS maison (aucun build front) · SQLite en dev / MySQL en prod.
Basé sur le projet Be.Bright (même palette de couleurs), entièrement refondu : mise en page **sidebar en cards**, nouvelles pages, nouveaux composants, catalogue de produits à la demande (Printful, Printify, Gelato, Gooten).

## Démarrage rapide

```bash
composer install
cp .env.example .env && php artisan key:generate   # DB_CONNECTION=sqlite pour tester
touch database/daebak.sqlite                        # et DB_DATABASE=<chemin absolu> dans .env
php artisan migrate --seed                          # catalogue, avis de démo, comptes de démo
php artisan serve
```

Comptes de démonstration (créés par `DatabaseSeeder`, **à changer avant la mise en ligne**) :
admin `admin@daebak.test` / `daebak-admin-2026` — client `demo@daebak.test` / `daebak-demo-2026` · admin : `/admin`.

## Instructions de base (skill `/webapp-base`)

| Règle | Où c'est implémenté |
|---|---|
| Identité regroupée (dupliquer le projet = modifier 1 fichier) | `config/site.php` (+ `.env`, `public/images/brand/*`, `lang/*`) |
| 100 % des textes en JSON (en · fr · ko) | `lang/{locale}/*.json`, helpers `t()` / `tg()` (`app/Support/I18n.php`), clés JS exposées via `window.DAEBAK.i18n` |
| Sidebar card + drawer responsive (boutons ouvrir/fermer), toggles thème & langue | `resources/views/partials/sidebar.blade.php`, `public/css/daebak.css` |
| Hero landing élastique (≈ 340–400 px) / pages 60–150 px | `.hero`, `.page-hero` + `<x-page-hero>` |
| Chat IA + mode manuel, bouton auto-masquable (5 s) | `partials/chat.blade.php`, `ChatController`, `ChatAssistant`, admin « Live chat » |
| Toasts, skeletons, progress bar, pagination, tableaux, états vides | `public/js/daebak.js`, `daebak.css` |
| Icônes / drapeaux flat | Font Awesome 6 + flag-icons (cdnjs) |

## Templates de description produit (nouveau)

8 mises en page responsive : `classic`, `editorial`, `cards`, `tabs`, `accordion`, `spec-sheet`, `timeline`, `showcase`.
- Choix par produit dans l'admin (onglet *Description layout*, + action groupée « Set description layout »).
- Aperçu : ajouter `?tpl=nom` à l'URL du produit (les admins ont aussi un sélecteur sur la fiche).
- Contenu : texte avec syntaxe légère — `## Titre` (section), `- puce`, `Clé: valeur` (tableau de specs), `> citation`, `**gras**`.
- Fichiers : `config/description_templates.php`, `app/Support/ProductDescription.php`, `resources/views/components/product-description.blade.php`, `resources/views/description-templates/*`, `public/css/desc-templates.css`, `public/js/desc-templates.js`. Même module installé dans `../bebright` (Nitha / Be.Bright).

## Blog (Journal)

- Public : `/blog` (article à la une, filtres par catégorie, recherche, pagination) et `/blog/{slug}` (sommaire, temps de lecture, partage, produits liés, article précédent/suivant, JSON-LD).
- Admin : menu **Blog** (contenu en en/fr/ko avec la même syntaxe légère que les descriptions, catégorie, couverture, tags, produits liés, publication planifiée, mise en avant).
- Données de démo : `database/data/blog.php` + `BlogSeeder` (6 articles, couvertures SVG générées dans `public/images/blog`).

## Catalogue & visuels

- Données de démo : `database/data/catalog.php` + `catalog_types.php` (23 produits en en/fr/ko). `php artisan db:seed --class=CatalogSeeder`.
- Visuels : maquettes SVG générées (`app/Support/Art/*`, `php artisan daebak:art`). Remplacez-les par vos mockups Printful/Printify en renseignant `image` / `images` / `variants.colors[].image` du produit (admin).
- Prix en USD ; EUR / KRW indicatifs (taux dans `config/site.php`). Livraison : zones et franco dans `config/site.php`.

## Paiement & IA

- Paiement en **mode démo** tant que `STRIPE_SECRET_KEY` est vide (`app/Services/PaymentService.php`). À brancher : Stripe / PayPal.
- Chat : base de connaissances locale (`lang/*/chat.json`) ; renseigner `ANTHROPIC_API_KEY` pour activer le modèle IA (`CHAT_AI_MODEL`).
- Synchronisation fournisseurs (`PRINTFUL_API_TOKEN`, `PRINTIFY_API_TOKEN`) : champs prêts (`provider`, `provider_sku`), intégration API à écrire.

## Avant la mise en ligne

Textes légaux = modèles à faire valider · profils créateurs et avis = données de démo (`is_sample`) · changer les mots de passe seedés · adresses/contacts dans `config/site.php`.

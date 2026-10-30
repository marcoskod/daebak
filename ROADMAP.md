# Daebak — feuille de route (templates, pages, admin)

Audit réalisé au navigateur le 2026-10-30 : dashboard admin (17 écrans), puis boutique cliente (22 URLs × 3 langues, parcours panier → commande → paiement démo, contact, newsletter, chat).
Règle : ne rien supprimer d'important, ne pas modifier le design ni les contenus existants, ajouts en continuité du code actuel.

## Livré
- **Éditeur de pages** (admin → Content → Shop pages) : textes en/fr/ko, images de bannière, couleurs, fonds, bordures, rayons — stockés en surcharges (`page_settings`), sans toucher aux vues d'origine.
- **10 templates d'articles** avec widget de choix avant la rédaction (`config/blog_templates.php`).
- **Miniatures de catégories** (upload admin + cards accueil / catalogue / collections).

## Constats de l'audit
| # | Constat | Gravité | Décision |
|---|---|---|---|
| 1 | « Reset this page » / enregistrement à vide ne vidait pas le cache des surcharges (suppression en masse sans événement modèle) | Haute | **Corrigé** |
| 2 | Checkout : bandeau rose vide sous le hero (`#checkout-errors[hidden]` écrasé par `.alert{display:flex}`) | Moyenne | **Corrigé** (règle `[hidden]` additive) |
| 3 | Fiche catégorie : la miniature existante n'est pas visible dans le champ d'upload | Moyenne | **Corrigé** (aperçu de l'image actuelle) |
| 4 | Liste admin des catégories : les catégories parentes affichent 0 produit (comptage direct uniquement) | Basse | **Corrigé** (parent + sous-catégories) |
| 5 | Libellé français « Mouvements de stock » dans un admin en anglais | Basse | Corrigé si trivial |
| 6 | Pas de test automatisé sur l'éditeur de pages | Moyenne | **Ajouté** (enregistrement, reset, CSS généré) |
| 7 | Données de test créées pendant l'audit (commande, chat, newsletter, contact) | Basse | **Nettoyées** |
| 8 | Serveur de dev lent (2–8 s par page : OneDrive + OPcache désactivé) | Info | Hors code — lancer avec `PHP_CLI_SERVER_WORKERS=4`, exclure le dossier de OneDrive |

## Prochaines étapes (hors périmètre de cette passe)
1. **Éditeur de pages** : rendre éditables les contenus pilotés par `config/site.php` (couvertures des collections, lookbook, zones de livraison) ; historique / restauration de version ; aperçu avant publication.
2. **Templates d'articles** : comparer octet à octet le rendu `classic` avec l'ancien ; tests visuels mobile + mode sombre des 9 autres templates ; réglages par template (largeur, lettrine on/off).
3. **Templates de description produit** : réserver l'aperçu `?tpl=` aux admins (aujourd'hui public, comme dans le README).
4. **Catégories** : permettre de retirer une image « legacy » depuis le champ ; recadrage 1:1 imposé ; ordre par glisser-déposer testé en navigateur.
5. **Médias** : nettoyage des fichiers `public/uploads` orphelins ; conversion WebP à l'upload.
6. **Qualité** : tests navigateur automatisés (parcours client), suppression des clés de traduction inutilisées, audit a11y (contrastes en mode sombre).
7. **Mise en ligne** : changer les mots de passe seedés, brancher Stripe/PayPal, valider les textes légaux.

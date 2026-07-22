# Guide de mise en production — Clinique Tamarix

Ce document décrit la procédure complète de déploiement du site institutionnel
et du tableau de bord de la Clinique Médico-Chirurgicale Tamarix.

---

## 1. Prérequis du serveur

| Élément | Version minimale | Remarque |
|---|---|---|
| PHP | 8.4 | avec les extensions ci-dessous |
| MySQL | 8.0 (ou MariaDB 10.6) | jeu de caractères `utf8mb4` |
| Composer | 2.x | |
| Node.js | 20.x | uniquement pour compiler les assets |
| Serveur web | Nginx ou Apache | avec module de réécriture d'URL |

**Extensions PHP requises :** `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`,
`gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.

```bash
php -m | grep -E 'bcmath|curl|gd|mbstring|pdo_mysql|zip'
```

> **Hébergement mutualisé** (conforme au CDC §6.1) : le site public et le
> back-office sont une seule application Laravel, sur un seul hébergement,
> avec un certificat SSL commun.

---

## 2. Base de données

Connectez-vous à MySQL et créez la base et un utilisateur dédié :

```sql
CREATE DATABASE clinique_tamarix
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'tamarix'@'localhost' IDENTIFIED BY 'UN_MOT_DE_PASSE_TRES_SOLIDE';
GRANT ALL PRIVILEGES ON clinique_tamarix.* TO 'tamarix'@'localhost';
FLUSH PRIVILEGES;
```

> N'utilisez **jamais** le compte `root` pour l'application en production.

---

## 3. Déploiement des fichiers

```bash
# 1. Récupérer le code
cd /var/www
git clone <url-du-depot> clinique-tamarix
cd clinique-tamarix

# 2. Dépendances PHP (sans les paquets de développement)
composer install --no-dev --optimize-autoloader

# 3. Compiler les assets, puis retirer node_modules
npm ci
npm run build
rm -rf node_modules

# 4. Configuration
cp .env.example .env
php artisan key:generate
```

---

## 4. Configurer le fichier `.env`

```dotenv
APP_NAME="Clinique Tamarix"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.clinique-tamarix.ci
APP_TIMEZONE=Africa/Abidjan
APP_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinique_tamarix
DB_USERNAME=tamarix
DB_PASSWORD=UN_MOT_DE_PASSE_TRES_SOLIDE

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

QUEUE_CONNECTION=database
CACHE_STORE=database

# Envoi des e-mails (repli en cas d'échec WhatsApp)
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-hebergeur.ci
MAIL_PORT=587
MAIL_USERNAME=contact@clinique-tamarix.ci
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="contact@clinique-tamarix.ci"
MAIL_FROM_NAME="Clinique Tamarix"
```

> ⚠️ `APP_DEBUG=false` est **impératif** : sinon les erreurs exposeraient
> la configuration du serveur aux visiteurs.

---

## 5. Initialiser l'application

```bash
php artisan migrate --force
php artisan db:seed --class=SettingSeeder      # paramètres par défaut
php artisan db:seed --class=SpecialtySeeder    # spécialités médicales
php artisan storage:link                       # accès aux photos

# Mise en cache (à relancer après chaque déploiement)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Créer le premier compte Direction

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Direction Tamarix',
    'email' => 'direction@clinique-tamarix.ci',
    'password' => 'UN_MOT_DE_PASSE_SOLIDE',
    'role' => App\Enums\UserRole::Direction,
    'is_active' => true,
]);
```

> **Ne jamais exécuter `php artisan db:seed` sans option en production** :
> les seeders de démonstration créeraient des comptes avec le mot de passe
> `password` et des rendez-vous fictifs.

---

## 6. Permissions des fichiers

```bash
sudo chown -R www-data:www-data /var/www/clinique-tamarix
sudo find /var/www/clinique-tamarix -type f -exec chmod 644 {} \;
sudo find /var/www/clinique-tamarix -type d -exec chmod 755 {} \;
sudo chmod -R 775 storage bootstrap/cache
```

---

## 7. Configuration du serveur web

### Nginx

```nginx
server {
    listen 80;
    server_name clinique-tamarix.ci www.clinique-tamarix.ci;
    return 301 https://www.clinique-tamarix.ci$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.clinique-tamarix.ci;

    # La racine pointe sur public/ — jamais sur la racine du projet
    root /var/www/clinique-tamarix/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/www.clinique-tamarix.ci/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.clinique-tamarix.ci/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    client_max_body_size 8M;   # photos des médecins et articles

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Cache des ressources statiques
    location ~* \.(css|js|jpg|jpeg|png|webp|svg|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known) { deny all; }
}
```

### Apache

Le fichier `public/.htaccess` fourni par Laravel suffit. Assurez-vous que
`mod_rewrite` est actif et que le `DocumentRoot` pointe sur `public/`.

---

## 8. Activer le HTTPS (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d clinique-tamarix.ci -d www.clinique-tamarix.ci
sudo certbot renew --dry-run   # vérifier le renouvellement automatique
```

Le renouvellement est ensuite automatique (tous les 90 jours).
L'application ajoute l'en-tête HSTS dès que le site est servi en HTTPS.

---

## 9. Tâches planifiées (cron)

Une **seule** ligne suffit — Laravel orchestre le reste :

```bash
sudo crontab -e -u www-data
```

```cron
* * * * * cd /var/www/clinique-tamarix && php artisan schedule:run >> /dev/null 2>&1
```

Tâches ainsi automatisées :

| Tâche | Fréquence | Rôle |
|---|---|---|
| `tamarix:rappels-j1` | tous les jours à 10h00 | Rappel WhatsApp J-1 |
| `tamarix:cloturer-rdv-passes` | tous les jours à 01h00 | Bascule des RDV échus en historique |
| `model:prune` | dimanche à 02h00 | Purge du journal d'audit (> 1 an) |

Vérification : `php artisan schedule:list`

---

## 10. File d'attente (envoi des notifications)

Les notifications partent en arrière-plan. Installez un service systemd :

```bash
sudo nano /etc/systemd/system/tamarix-queue.service
```

```ini
[Unit]
Description=File d'attente Clinique Tamarix
After=network.target

[Service]
User=www-data
Restart=always
RestartSec=5
ExecStart=/usr/bin/php /var/www/clinique-tamarix/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now tamarix-queue
sudo systemctl status tamarix-queue
```

> Sans ce service, les rendez-vous fonctionnent mais **aucune notification
> ne part**. C'est le point le plus souvent oublié en production.

---

## 11. Activer WhatsApp Business

1. Créer un compte sur [developers.facebook.com](https://developers.facebook.com) →
   application de type **Business** → produit **WhatsApp**.
2. Vérifier le numéro officiel de la clinique et le nom affiché « Clinique Tamarix ».
3. Créer les **5 modèles de messages** dans le gestionnaire WhatsApp et les faire
   approuver par Meta (délai : quelques heures à 48 h) :

   | Modèle | Variables, dans l'ordre |
   |---|---|
   | `tamarix_rdv_recu` | prénom, spécialité, médecin, date, heure, code de suivi |
   | `tamarix_rdv_confirme` | prénom, spécialité, médecin, date, heure, adresse |
   | `tamarix_rappel_j1` | prénom, date, heure, médecin |
   | `tamarix_rdv_annule` | prénom, date, heure |
   | `tamarix_rdv_reporte` | prénom, nouvelle date, nouvelle heure, médecin |

4. Renseigner le `.env` :

```dotenv
WHATSAPP_DRIVER=cloud
WHATSAPP_PHONE_NUMBER_ID=...
WHATSAPP_ACCESS_TOKEN=...
WHATSAPP_WEBHOOK_VERIFY_TOKEN=une-chaine-secrete-de-votre-choix
```

5. Configurer le webhook dans la console Meta :
   - URL : `https://www.clinique-tamarix.ci/webhooks/whatsapp`
   - Jeton de vérification : identique à `WHATSAPP_WEBHOOK_VERIFY_TOKEN`
   - S'abonner au champ **`messages`** (accusés délivré / lu).

6. `php artisan config:cache` puis tester une réservation réelle.

> Tant que `WHATSAPP_DRIVER=log`, le site fonctionne normalement : les messages
> sont journalisés et consultables dans **Notifications**, mais ne sont pas envoyés.
> Cela permet d'ouvrir le site sans attendre l'approbation de Meta.

---

## 12. Sauvegardes

```bash
sudo nano /usr/local/bin/tamarix-backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d-%H%M)
DEST=/var/backups/tamarix
mkdir -p "$DEST"

# Base de données
mysqldump -u tamarix -p'MOT_DE_PASSE' clinique_tamarix | gzip > "$DEST/bd-$DATE.sql.gz"

# Fichiers téléversés (photos des médecins, images d'articles)
tar czf "$DEST/fichiers-$DATE.tar.gz" -C /var/www/clinique-tamarix storage/app/public

# Conservation : 30 jours
find "$DEST" -type f -mtime +30 -delete
```

```bash
sudo chmod 700 /usr/local/bin/tamarix-backup.sh
sudo crontab -e
# Sauvegarde quotidienne à 03h00 (CDC §6.2)
0 3 * * * /usr/local/bin/tamarix-backup.sh
```

> Copiez régulièrement ces archives **hors du serveur** (stockage externe) :
> une sauvegarde sur la même machine ne protège pas d'une panne matérielle.

**Restauration :**
```bash
gunzip < /var/backups/tamarix/bd-AAAAMMJJ-HHMM.sql.gz | mysql -u tamarix -p clinique_tamarix
```

---

## 13. Vérifications après mise en ligne

- [ ] `https://` actif, redirection depuis `http://` fonctionnelle
- [ ] Page d'accueil correcte sur mobile, tablette et ordinateur
- [ ] Réservation complète : spécialité → médecin → date → créneau → validation
- [ ] Le rendez-vous apparaît **immédiatement** dans le tableau de bord en « en attente »
- [ ] La confirmation depuis le back-office déclenche la notification
- [ ] Suivi puis annulation avec téléphone + code de suivi ; le créneau est libéré
- [ ] `https://.../sitemap.xml` accessible
- [ ] `php artisan schedule:list` affiche les 3 tâches
- [ ] `systemctl status tamarix-queue` : service actif
- [ ] **Mots de passe de démonstration changés** (aucun compte avec `password`)
- [ ] `APP_DEBUG=false` confirmé
- [ ] Coordonnées réelles saisies dans **Paramètres** (téléphones, adresse, horaires, carte)
- [ ] Mentions légales complétées (RCCM, autorisation, hébergeur, directeur de publication)

---

## 14. Référencement

1. **Google Search Console** : ajouter la propriété, valider, soumettre
   `https://www.clinique-tamarix.ci/sitemap.xml`.
2. **Google Business Profile** : créer la fiche de la clinique (adresse, horaires,
   téléphone, photos) — essentiel pour la recherche locale à Abidjan.
3. Les données structurées Schema.org (`MedicalClinic`) sont déjà intégrées :
   vérifiez-les sur [search.google.com/test/rich-results](https://search.google.com/test/rich-results).

---

## 15. Mise à jour ultérieure

```bash
cd /var/www/clinique-tamarix
php artisan down --render="errors::503"

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build && rm -rf node_modules
php artisan migrate --force

php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl restart tamarix-queue

php artisan up
```

---

## 16. Dépannage

| Symptôme | Cause probable | Solution |
|---|---|---|
| Page blanche | Erreur PHP masquée | Consulter `storage/logs/laravel.log` |
| Erreur 500 après déploiement | Cache obsolète | `php artisan optimize:clear` puis remettre en cache |
| Photos non affichées | Lien symbolique absent | `php artisan storage:link` |
| Notifications non envoyées | File d'attente arrêtée | `systemctl status tamarix-queue` |
| Rappels J-1 absents | Cron non configuré | Vérifier `crontab -l -u www-data` |
| Créneaux indisponibles | Aucune disponibilité saisie | Back-office → Médecins → Disponibilités |
| « 419 Page Expired » | Session expirée / cookie non sécurisé | Vérifier `APP_URL` et `SESSION_SECURE_COOKIE` |

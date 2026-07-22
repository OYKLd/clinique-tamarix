# Clinique Médico-Chirurgicale Tamarix

Site web institutionnel et tableau de bord de gestion des rendez-vous.
« Nous plantons l'Espérance »

**Laravel 12 · PHP 8.4 · MySQL 8 · Bootstrap 5 · WhatsApp Business API**

---

## Fonctionnalités

**Site public**
- Pages institutionnelles : accueil, clinique, services, équipe médicale (filtrable), actualités, contact
- **Prise de rendez-vous en un écran** : spécialité → médecin (ou « premier disponible ») → date → créneau → coordonnées, en moins d'une minute, sans création de compte
- **Suivi et annulation en libre-service** avec le numéro de téléphone et un code de suivi (`TMX-JJMM-XXXX`)
- Bandeau urgences permanent, conseils santé de prévention, référencement optimisé

**Tableau de bord**
- Gestion des rendez-vous : vue liste filtrable et calendrier jour/semaine/mois
- Confirmation, report, annulation et clôture en un clic, avec notification automatique
- Fiches patients (historique, notes internes), gestion des médecins, disponibilités, congés, spécialités, articles, comptes et paramètres
- Statistiques (taux de remplissage, pics d'affluence) et exports Excel/PDF
- Journal d'audit de toutes les actions
- 4 rôles cloisonnés : Accueil, Médecin, Administration, Direction

**Notifications WhatsApp** — accusé de réception, confirmation, rappel J-1,
annulation et report, avec journal des envois et repli automatique par e-mail.

---

## Installation en développement

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Créez la base puis renseignez `DB_*` dans `.env` :

```sql
CREATE DATABASE clinique_tamarix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Le site est disponible sur http://localhost:8000 et le back-office sur `/admin`.

### Comptes de démonstration

| Rôle | Adresse | Mot de passe |
|---|---|---|
| Direction | direction@clinique-tamarix.ci | `password` |
| Administration | admin@clinique-tamarix.ci | `password` |
| Accueil | accueil@clinique-tamarix.ci | `password` |
| Médecin | medecin@clinique-tamarix.ci | `password` |

> Comptes de développement uniquement — voir [DEPLOIEMENT.md](DEPLOIEMENT.md) pour la production.

---

## Tests

```bash
php artisan test
```

39 tests couvrant la réservation, le suivi/annulation, les rôles du back-office,
les notifications et la sécurité des pages publiques.

---

## Tâches planifiées

```bash
php artisan tamarix:rappels-j1            # rappel J-1 des RDV confirmés
php artisan tamarix:cloturer-rdv-passes   # bascule des RDV échus en historique
```

En production, une seule ligne cron pilote l'ensemble (voir le guide de déploiement).

---

## Notifications WhatsApp

Par défaut, `WHATSAPP_DRIVER=log` : les messages sont journalisés et consultables
dans **Notifications** du back-office, sans envoi réel. Le passage en production
(`WHATSAPP_DRIVER=cloud`) est décrit dans [DEPLOIEMENT.md](DEPLOIEMENT.md) §11.

---

## Documentation

- **[DEPLOIEMENT.md](DEPLOIEMENT.md)** — mise en production complète : serveur,
  base de données, HTTPS, cron, file d'attente, WhatsApp, sauvegardes, dépannage.

---

## Structure

```
app/
├── Console/Commands/    Rappel J-1, clôture des RDV échus
├── Enums/               Statuts de RDV, rôles, canaux de notification
├── Exports/             Export Excel des rendez-vous
├── Http/
│   ├── Controllers/     Site public + Admin/
│   ├── Middleware/      Contrôle des rôles, en-têtes de sécurité
│   └── Requests/        Validation des formulaires
├── Jobs/                Envoi des notifications en file d'attente
├── Models/              12 modèles Eloquent
└── Services/
    ├── AvailabilityService     Calcul des créneaux disponibles
    ├── AppointmentNotifier     Rédaction des messages patients
    ├── StatisticsService       Indicateurs et taux de remplissage
    └── Whatsapp/               Client de l'API Meta Cloud
```

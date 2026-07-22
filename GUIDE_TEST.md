# Guide de test — Clinique Tamarix

Ce document vous permet de vérifier vous-même que tout fonctionne, puis de
préparer les éléments que **vous seul** pouvez fournir (compte WhatsApp,
contenus, coordonnées réelles).

---

# PARTIE 1 — Démarrer le projet

## 1.1 Ouvrir un terminal au bon endroit

Ouvrez **PowerShell** (touche Windows → tapez « PowerShell ») puis :

```powershell
cd C:\Users\DELL\Documents\clinique-tamarix
```

Toutes les commandes de ce guide se lancent depuis ce dossier.

## 1.2 Vérifier que MySQL tourne

```powershell
Get-Service MySQL80
```

Vous devez lire `Status : Running`. Si c'est `Stopped` :

```powershell
Start-Service MySQL80
```

## 1.3 Lancer le serveur

```powershell
php artisan serve
```

Vous verrez :

```
INFO  Server running on [http://127.0.0.1:8000].
```

**Laissez cette fenêtre ouverte** — c'est elle qui fait tourner le site.
Pour arrêter le serveur : `Ctrl + C`.

> Si le port 8000 est déjà pris : `php artisan serve --port=8080`,
> et remplacez `8000` par `8080` dans toutes les adresses ci-dessous.

## 1.4 Ouvrir le site

Dans votre navigateur : **http://localhost:8000**

---

# PARTIE 2 — Tester le site public

## Test 1 — La page d'accueil

Adresse : http://localhost:8000

**Ce que vous devez voir :**
- En haut, un bandeau bleu marine « Urgences 24h/24 – 7j/7 » avec un point rouge clignotant
- Le logo Tamarix, le menu, et un bouton bordeaux « Prendre rendez-vous »
- Une grande image de la clinique avec le texte « Votre santé mérite une prise en charge moderne… »
- Plus bas : les spécialités, les étapes de réservation, les médecins, un encadré « Le saviez-vous ? »
- En bas à droite, un bouton WhatsApp vert flottant

**Testez le responsive :** appuyez sur `F12`, puis sur l'icône téléphone/tablette
en haut à gauche du panneau. Le menu doit se transformer en bouton « hamburger ».

## Test 2 — Les autres pages

Cliquez sur chaque entrée du menu. Toutes doivent s'afficher sans erreur :

| Page | Adresse | À vérifier |
|---|---|---|
| La Clinique | /la-clinique | Mot de la direction, 4 valeurs, plateau technique |
| Nos Services | /nos-services | 10 spécialités avec conseils santé |
| Équipe médicale | /equipe-medicale | 8 médecins |
| Actualités | /actualites | 4 articles |
| Contact | /contact | Formulaire + carte |

**Sur « Équipe médicale »**, utilisez la liste déroulante « Filtrer par spécialité » :
choisissez *Cardiologie* → seul le Dr Serge YAO doit rester.

## Test 3 — LE TEST LE PLUS IMPORTANT : prendre un rendez-vous

Allez sur http://localhost:8000/prendre-rendez-vous

**Suivez exactement ces étapes :**

1. **Spécialité** → choisissez `Pédiatrie`
   → *La liste « Médecin » doit se remplir toute seule*

2. **Médecin** → choisissez `Dr Adjoua N'GUESSAN`
   → *La liste « Date » doit se remplir avec les jours disponibles*

3. **Date** → choisissez la première date proposée
   → *La liste « Créneau horaire » doit se remplir (08:00, 08:30…)*

4. **Créneau** → choisissez `09:00`
   → *Le bloc « Vos coordonnées » doit apparaître automatiquement*

5. Remplissez :
   - Prénom : `Test`
   - Nom : `Patient`
   - Téléphone : `07 11 22 33 44`
   - **Cochez la case** de consentement (obligatoire)

6. Cliquez sur **« Confirmer ma demande de rendez-vous »**

**Résultat attendu :** une page verte « Votre demande est enregistrée ! » avec
un **code de suivi** du type `TMX-2207-1234`.

👉 **NOTEZ CE CODE**, vous en avez besoin au test suivant.

### Vérifications importantes pendant ce test

- Les **dimanches n'apparaissent jamais** dans les dates
- Si vous choisissez « Peu importe — le premier disponible », le système
  attribue automatiquement un médecin
- Si vous ne cochez pas la case de consentement, le formulaire refuse l'envoi

## Test 4 — Suivre et annuler son rendez-vous

Allez sur http://localhost:8000/suivre-mon-rendez-vous

1. Téléphone : `07 11 22 33 44` *(le même qu'au test 3)*
2. Code de suivi : celui que vous avez noté *(les minuscules fonctionnent aussi)*
3. Cliquez sur **« Retrouver mon rendez-vous »**

**Résultat attendu :** votre rendez-vous s'affiche avec le badge orange
« En attente » et un message rassurant.

**Testez maintenant une erreur volontaire :** revenez en arrière et saisissez
le téléphone `07 99 99 99 99` avec le même code → message rouge
« Aucun rendez-vous ne correspond… ». C'est la protection contre la
consultation des rendez-vous d'autrui.

⚠️ **N'annulez pas encore** — on va d'abord le confirmer depuis le back-office.

---

# PARTIE 3 — Tester le tableau de bord

## Test 5 — Se connecter

Adresse : http://localhost:8000/admin

| Compte à utiliser | Adresse e-mail | Mot de passe |
|---|---|---|
| **Accueil** | accueil@clinique-tamarix.ci | password |
| **Administration** | admin@clinique-tamarix.ci | password |
| **Direction** | direction@clinique-tamarix.ci | password |
| **Médecin** | medecin@clinique-tamarix.ci | password |

Connectez-vous d'abord avec le compte **Accueil**.

**Résultat attendu :** un tableau de bord avec 4 cartes de chiffres et la liste
des rendez-vous du jour.

## Test 6 — LE TEST CLÉ : confirmer un rendez-vous

C'est le critère central du cahier des charges.

1. Menu de gauche → **Rendez-vous**
2. Filtre « Statut » → choisissez **En attente** → cliquez **Filtrer**
3. Trouvez la ligne de votre rendez-vous `TMX-…` (celui du test 3)
4. Cliquez sur le bouton **vert avec une coche** ✓

**Résultat attendu :** un bandeau vert
« Rendez-vous TMX-… confirmé. Le patient est notifié. »
et le statut passe à **Confirmé**.

### Vérifiez que la notification a bien été créée

1. Menu de gauche → **Notifications**
2. La première ligne doit être `rdv_confirme` avec votre code de suivi
3. Cliquez sur l'**œil** 👁 pour lire le message exact qui part au patient

> Le bandeau orange « Mode simulation » est **normal** : les messages sont
> préparés et journalisés, mais pas encore envoyés — voir la Partie 5.

## Test 7 — Le calendrier

Menu → **Calendrier**

- Les rendez-vous s'affichent par jour, avec une couleur par statut
- Boutons `<` et `>` pour naviguer, « Aujourd'hui » pour revenir
- Testez les vues jour / semaine / mois

## Test 8 — La fiche patient

Menu → **Patients** → cliquez sur un nom

Vous voyez ses coordonnées, son historique complet de rendez-vous, et une zone
« Notes internes ». Écrivez quelque chose et cliquez **Enregistrer les notes**.

## Test 9 — Vérifier le cloisonnement des rôles (sécurité)

1. Déconnectez-vous (menu en haut à droite → **Se déconnecter**)
2. Reconnectez-vous avec **medecin@clinique-tamarix.ci** / `password`

**Ce que vous devez constater :**
- Le menu de gauche est **plus court** : pas de section « Gestion », pas de « Patients »
- Menu **Rendez-vous** → seuls les rendez-vous du **Dr Aïcha KONÉ** apparaissent
- Tapez directement http://localhost:8000/admin/patients dans la barre d'adresse
  → **page « 403 Interdit »**. C'est le comportement voulu.

3. Reconnectez-vous en **admin@clinique-tamarix.ci** → tout le menu réapparaît.

## Test 10 — Le test métier qui prouve que tout est relié

**Objectif :** vérifier qu'un congé saisi par la clinique bloque immédiatement
la réservation côté patient.

1. Connecté en **Administration**, menu → **Médecins**
2. Cliquez sur le crayon ✏ du **Dr Serge YAO**
3. À droite, dans « Congés, gardes et absences » :
   - Du : une date de la semaine prochaine
   - Au : deux jours plus tard
   - Motif : `Congés`
   - Cliquez **+**
4. **Ouvrez un nouvel onglet** sur http://localhost:8000/prendre-rendez-vous
5. Choisissez `Cardiologie` → `Dr Serge YAO` → ouvrez la liste des dates

**Résultat attendu :** les 3 jours de congé **ont disparu** de la liste.

C'est la preuve que back-office et site public partagent bien la même base.
*(Supprimez ensuite ce congé de test avec le bouton corbeille.)*

## Test 11 — Statistiques et exports

Connecté en **Administration** ou **Direction**, menu → **Statistiques**

- Indicateurs, taux de remplissage par médecin, pics d'affluence, graphiques
- Cliquez **Export Excel** → un fichier `.xlsx` se télécharge → ouvrez-le
- Cliquez **Rapport PDF** → un PDF de synthèse se télécharge → ouvrez-le

> Avec le compte **Accueil**, ce menu n'existe pas et l'accès direct renvoie 403.

## Test 12 — Annuler le rendez-vous (retour côté patient)

Retournez sur http://localhost:8000/suivre-mon-rendez-vous,
retrouvez votre rendez-vous : il affiche maintenant **Confirmé** (badge vert).

Cliquez **« Annuler ce rendez-vous »** → confirmez.

**Résultat attendu :** « Votre rendez-vous a bien été annulé. Le créneau a été libéré. »

**Vérifiez la boucle complète :** retournez dans le back-office → **Rendez-vous** →
le statut est **Annulé**. Et si vous retournez sur la page de réservation, le
créneau de 09:00 est **de nouveau disponible**.

---

# PARTIE 4 — Tests automatiques (optionnel mais rassurant)

Ouvrez un **second** PowerShell (gardez le serveur tournant dans le premier) :

```powershell
cd C:\Users\DELL\Documents\clinique-tamarix
php artisan test
```

**Résultat attendu :** `Tests: 39 passed (109 assertions)` — tout en vert.

Ces tests vérifient automatiquement la réservation, l'impossibilité de réserver
deux fois le même créneau, le suivi/annulation, la protection du back-office,
le cloisonnement des rôles et les notifications.

## Tester les tâches planifiées à la main

```powershell
# Rappel J-1 : prépare les rappels des RDV confirmés de demain
php artisan tamarix:rappels-j1

# Traiter la file d'attente (envoie réellement les messages préparés)
php artisan queue:work --stop-when-empty

# Clôture des RDV échus
php artisan tamarix:cloturer-rdv-passes
```

Après ces commandes, allez dans **Notifications** du back-office : les rappels
`rappel_j1` apparaissent avec le statut « Envoyé ».

## Remettre les données de démonstration à zéro

Si vous avez fait beaucoup de tests et voulez repartir propre :

```powershell
php artisan migrate:fresh --seed
```

⚠️ Cette commande **efface tout** et recrée les données de démonstration.

---

# PARTIE 5 — CE QUE VOUS DEVEZ FOURNIR OU CONFIGURER

Voici ce qui ne dépend pas du code et que vous seul pouvez faire.

## 5.1 🟢 FACILE — Les coordonnées réelles de la clinique

**Aujourd'hui, le site affiche des numéros fictifs** (`+225 27 00 00 00 00`).

**Où corriger :** back-office → connectez-vous en **Administration** →
menu **Paramètres**.

Remplissez :

| Champ | Ce que vous devez mettre |
|---|---|
| Nom de la clinique | Clinique Médico-Chirurgicale Tamarix |
| Téléphone standard | Le vrai numéro d'accueil |
| **Téléphone des urgences** | Le vrai numéro d'urgence 24h/24 |
| **Numéro WhatsApp Business** | Le numéro WhatsApp officiel |
| Adresse e-mail | contact@… |
| Adresse postale | L'adresse exacte (commune, rue, repère) |
| Horaires d'ouverture | Ex. « Lundi – Samedi : 08h00 – 18h00 » |
| URL Google Maps | Voir ci-dessous |
| Facebook / Instagram / LinkedIn | Les liens des pages (ou laissez vide) |

**Pour l'URL Google Maps :**
1. Allez sur [google.com/maps](https://www.google.com/maps), cherchez la clinique
2. Cliquez **Partager** → onglet **Intégrer une carte**
3. Copiez uniquement l'adresse entre `src="` et `"` dans le code affiché
4. Collez-la dans le champ « URL d'intégration Google Maps »

Cliquez **Enregistrer** → le site public est mis à jour **immédiatement**.

## 5.2 🟢 FACILE — Les vrais médecins et spécialités

Actuellement : 8 médecins fictifs (Dr Aïcha KONÉ, etc.) créés pour la démonstration.

**Ce que vous devez faire**, back-office → **Médecins** :

1. **Supprimer ou désactiver** les médecins fictifs
   *(le bouton corbeille refuse la suppression s'ils ont des rendez-vous —
   dans ce cas, modifiez-les et décochez « Médecin actif »)*
2. **Ajouter les vrais médecins** avec, pour chacun :
   - Titre (Dr, Pr…), prénom, nom
   - Spécialité
   - **Photo** (JPG ou PNG, 2 Mo maximum, format carré de préférence)
   - Biographie (parcours, diplômes) — s'affiche sur le site
3. **Définir ses disponibilités** — ⚠️ **C'EST INDISPENSABLE** :
   sans disponibilité, **un médecin n'est pas réservable en ligne**.

   Exemple pour un médecin consultant le lundi matin et après-midi :
   - Jour `Lundi`, Début `08:00`, Fin `12:30`, Durée `30 min` → **+**
   - Jour `Lundi`, Début `14:00`, Fin `17:30`, Durée `30 min` → **+**
   - Répétez pour chaque jour de consultation

Faites de même dans **Spécialités** : ajustez la liste réelle, les descriptions
et les conseils santé (qui s'affichent sur le site pour inciter à consulter).

## 5.3 🟡 MOYEN — Les mentions légales

Les pages [Mentions légales](http://localhost:8000/mentions-legales) et
[Politique de confidentialité](http://localhost:8000/politique-de-confidentialite)
contiennent des passages en italique « *À compléter* ».

**Informations à réunir auprès de la direction :**
- Forme juridique et capital social
- Numéro **RCCM**
- Numéro de compte contribuable
- Numéro d'**autorisation d'ouverture** du Ministère de la Santé
- Nom du **directeur de la publication**
- Nom et coordonnées de l'**hébergeur** retenu

Ces textes sont dans les fichiers
`resources/views/pages/legal/mentions.blade.php` et `privacy.blade.php`.
Transmettez-moi les informations et je les intègre.

## 5.4 🔴 LE PLUS TECHNIQUE — Le compte WhatsApp Business

C'est l'élément le plus long à obtenir. **Comptez 3 à 7 jours** entre la
création du compte et l'approbation des messages par Meta.

> ℹ️ **Bonne nouvelle : le site fonctionne parfaitement sans.** Les messages
> sont préparés et consultables dans **Notifications**. Vous pouvez ouvrir le
> site et activer WhatsApp ensuite, sans rien changer au code.

### Étape A — Ce qu'il faut avant de commencer

1. **Un numéro de téléphone dédié** qui n'est **PAS** déjà utilisé sur
   WhatsApp normal ni WhatsApp Business.
   ⚠️ Si le numéro est déjà sur WhatsApp, il faut d'abord supprimer ce compte.
   Le plus simple : prendre une **nouvelle puce dédiée à la clinique**.
2. Une **page Facebook** au nom de la clinique (créez-la si elle n'existe pas).
3. Les **documents légaux** de la clinique (RCCM, autorisation) : Meta les
   demande pour la « vérification d'entreprise ».

### Étape B — Créer le compte Meta

1. Allez sur **[business.facebook.com](https://business.facebook.com)**
2. Créez un **compte Business Manager** au nom de la Clinique Tamarix
3. Allez sur **[developers.facebook.com](https://developers.facebook.com)**
4. **Mes applications** → **Créer une application** → type **Entreprise**
5. Dans le tableau de bord, ajoutez le produit **WhatsApp** → **Configurer**

### Étape C — Récupérer les 2 identifiants dont j'ai besoin

Dans **WhatsApp → Configuration de l'API** :

1. **`Identifiant du numéro de téléphone`** (Phone number ID)
   → une longue suite de chiffres, ex. `123456789012345`
2. **`Jeton d'accès`** (Access token)
   → une très longue chaîne commençant par `EAA…`

   ⚠️ Le jeton affiché par défaut est **temporaire (24 h)**. Pour la production,
   il faut un **jeton permanent** : Business Manager → **Paramètres d'entreprise**
   → **Utilisateurs système** → créer un utilisateur système → lui donner accès
   à l'application WhatsApp → **Générer un nouveau jeton** → cocher
   `whatsapp_business_messaging` et `whatsapp_business_management` →
   choisir **« N'expire jamais »**.

### Étape D — Faire approuver les 5 messages

Dans **WhatsApp → Gestionnaire de modèles** → **Créer un modèle**.

Créez **exactement ces 5 modèles**, catégorie **Utilitaire**, langue **Français** :

---

**Modèle 1 — Nom : `tamarix_rdv_recu`**

Corps du message à saisir :
```
Bonjour {{1}}, votre demande de rendez-vous à la Clinique Tamarix a bien été reçue.

Spécialité : {{2}}
Médecin : {{3}}
Date : {{4}} à {{5}}

Statut : en attente de confirmation par notre accueil.
Votre code de suivi : {{6}}
```
Exemples à fournir à Meta : `Awa`, `Pédiatrie`, `Dr Adjoua N'GUESSAN`,
`Lundi 27 juillet 2026`, `09:00`, `TMX-2707-1234`

---

**Modèle 2 — Nom : `tamarix_rdv_confirme`**

```
{{1}}, votre rendez-vous à la Clinique Tamarix est confirmé.

Spécialité : {{2}}
Médecin : {{3}}
Date : {{4}} à {{5}}
Lieu : {{6}}

Merci d'arriver 15 minutes en avance avec une pièce d'identité.
```

---

**Modèle 3 — Nom : `tamarix_rappel_j1`**

```
Rappel : {{1}}, vous avez rendez-vous demain {{2}} à {{3}} à la Clinique Tamarix avec {{4}}.

En cas d'empêchement, merci de nous prévenir.
```

---

**Modèle 4 — Nom : `tamarix_rdv_annule`**

```
{{1}}, votre rendez-vous du {{2}} à {{3}} à la Clinique Tamarix a bien été annulé.

Pour reprendre rendez-vous, rendez-vous sur notre site. À très bientôt.
```

---

**Modèle 5 — Nom : `tamarix_rdv_reporte`**

```
{{1}}, votre rendez-vous à la Clinique Tamarix a été reprogrammé.

Nouveau créneau : {{2}} à {{3}}
Médecin : {{4}}
```

---

⚠️ **Règles Meta à respecter absolument :**
- Les noms de modèles doivent être **exactement** ceux ci-dessus (minuscules, underscores)
- L'ordre des variables `{{1}}`, `{{2}}`… doit être respecté à la lettre
- Catégorie **Utilitaire** (pas « Marketing » : le refus serait probable)
- Pas de message commençant ou finissant par une variable

L'approbation prend de **quelques heures à 48 h**.

### Étape E — Me transmettre les informations

Une fois les modèles **approuvés** (statut vert « Actif »), donnez-moi :

```
Identifiant du numéro de téléphone : ...........................
Jeton d'accès permanent            : EAA......................
Statut des 5 modèles               : approuvés / en attente
```

Je les configure dans le fichier `.env` et on teste un envoi réel ensemble.

### Étape F — Le webhook (une fois le site en ligne)

Cette étape n'est possible **qu'après la mise en ligne** (il faut une adresse
publique en HTTPS, `localhost` ne fonctionne pas).

Dans **WhatsApp → Configuration → Webhooks** :
- URL de rappel : `https://www.votre-domaine.ci/webhooks/whatsapp`
- Jeton de vérification : une phrase secrète de votre choix, à me communiquer
- S'abonner au champ **`messages`**

C'est ce qui permet d'afficher « Délivré » et « Lu » dans le back-office.

## 5.5 🔴 Hébergement et mise en ligne

Pour que le site soit accessible au public, il faut :

1. **Un nom de domaine** — ex. `clinique-tamarix.ci`
   *(à acheter auprès d'un registrar ; le `.ci` se prend chez un prestataire agréé)*
2. **Un hébergement compatible** :
   - PHP **8.4** minimum
   - MySQL 8 ou MariaDB 10.6
   - **Accès SSH** (indispensable pour les tâches planifiées et la file d'attente)
   - Certificat SSL (Let's Encrypt gratuit)
3. Un **hébergement mutualisé suffit** — le site et le back-office sont une
   seule application, comme prévu au cahier des charges.

Toute la procédure d'installation serveur est détaillée dans
**[DEPLOIEMENT.md](DEPLOIEMENT.md)**. Communiquez-moi les accès de
l'hébergement le moment venu et je vous accompagne pas à pas.

## 5.6 🟢 Les contenus rédactionnels

À réunir auprès de la direction (annexe §11 du cahier des charges) :

- [ ] **Logo en haute définition** *(la version actuelle vient de votre `logo.jpeg` — un fichier SVG ou PNG transparent serait meilleur)*
- [ ] **Photos des médecins** (portraits, format carré)
- [ ] **Biographies** de chaque médecin
- [ ] **Photos des infrastructures** (accueil, bloc, imagerie, chambres…)
- [ ] **Mot de la direction** — le texte actuel est un exemple à valider ou remplacer
- [ ] **Liste définitive des spécialités** proposées à l'ouverture

---

# PARTIE 6 — Récapitulatif : votre feuille de route

## À faire par vous, dans l'ordre

| Priorité | Tâche | Difficulté | Délai |
|---|---|---|---|
| 1 | **Tester** le site avec ce guide (Parties 2 et 3) | Facile | 30 min |
| 2 | Saisir les **coordonnées réelles** (Paramètres) | Facile | 15 min |
| 3 | Réunir les **contenus** (photos, bios, textes) | Facile | selon la clinique |
| 4 | Saisir les **vrais médecins + disponibilités** | Facile | 1 h |
| 5 | Obtenir les **infos légales** (RCCM, autorisation) | Moyen | selon la direction |
| 6 | Créer le **compte WhatsApp Business** | Technique | 3 à 7 jours |
| 7 | Choisir **domaine + hébergement** | Technique | 1 à 3 jours |

## Ce que je fais dès que vous me transmettez

- Les informations légales → j'intègre les mentions légales définitives
- Les identifiants WhatsApp → je configure et on teste un envoi réel
- Les accès de l'hébergement → je vous accompagne pour la mise en ligne
- Le logo HD → je remplace la version actuelle

---

# PARTIE 7 — En cas de problème

| Symptôme | Cause | Solution |
|---|---|---|
| « Ce site est inaccessible » | Serveur arrêté | Relancer `php artisan serve` |
| Erreur de connexion à la base | MySQL arrêté | `Start-Service MySQL80` |
| Page sans mise en forme (texte brut) | Assets non compilés | `npm run build` |
| Photos de médecins invisibles | Lien manquant | `php artisan storage:link` |
| « 419 Page Expired » | Page restée ouverte trop longtemps | Rafraîchir avec `F5` |
| Aucun créneau proposé | Médecin sans disponibilités | Back-office → Médecins → Disponibilités |
| Modification invisible sur le site | Cache | `php artisan optimize:clear` |

**Commande de diagnostic générale** — si quelque chose cloche :

```powershell
php artisan optimize:clear
php artisan migrate --force
npm run build
php artisan serve
```

**Consulter les erreurs détaillées :**

```powershell
Get-Content storage\logs\laravel.log -Tail 30
```

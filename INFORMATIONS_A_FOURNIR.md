# Informations à fournir pour finaliser le projet

**Projet :** Site web institutionnel et tableau de bord — Clinique Médico-Chirurgicale Tamarix
**Destinataire :** Chef de projet
**Objet :** Éléments nécessaires à la mise en production

---

## Où en est le projet

Le développement est **terminé et fonctionnel** : site public, prise de rendez-vous
en ligne, suivi/annulation sans compte, tableau de bord avec gestion des rôles,
statistiques, exports et notifications WhatsApp.

Ce qui manque n'est **pas du développement**, mais des **contenus et des accès**
que seule la clinique peut fournir. Ce document liste précisément ces éléments.

Les informations sont classées en **3 niveaux** :

- 🔴 **BLOQUANT** — le site ne peut pas ouvrir sans
- 🟠 **IMPORTANT** — le site peut ouvrir, mais incomplet ou peu crédible
- 🟢 **CONFORT** — améliore le résultat, peut venir après l'ouverture

---

# 🔴 NIVEAU 1 — BLOQUANT POUR L'OUVERTURE

## 1.1 Coordonnées officielles de la clinique

Actuellement, le site affiche des numéros fictifs (`+225 27 00 00 00 00`).
**Ils apparaissent sur toutes les pages** — c'est le premier point à corriger.

| Information demandée | Précision attendue |
|---|---|
| Nom exact de la clinique | Tel qu'il doit apparaître (raison sociale ou nom commercial) |
| **Numéro d'urgence 24h/24** | Affiché en permanence en haut de chaque page |
| Numéro du standard / accueil | Pour la prise de rendez-vous par téléphone |
| Adresse e-mail de contact | Ex. contact@… — recevra les messages du formulaire |
| **Adresse physique complète** | Commune, quartier, rue, repère de localisation |
| Horaires d'ouverture | Ex. « Lundi – Samedi : 08h00 – 18h00 » |
| Jours de fermeture | La clinique ouvre-t-elle le samedi ? le dimanche ? |

> ❓ **Question précise :** l'accueil téléphonique et les urgences utilisent-ils
> le **même numéro** ou deux numéros différents ?

## 1.2 Liste des médecins et leurs horaires de consultation

⚠️ **C'est le point le plus critique du projet.** Le site propose des créneaux de
rendez-vous calculés à partir du planning de chaque médecin. **Sans planning,
aucun rendez-vous n'est réservable en ligne.**

Pour **chaque médecin**, il nous faut :

| Information | Exemple |
|---|---|
| Titre | Dr / Pr |
| Prénom et nom | Aïcha KONÉ |
| Spécialité | Pédiatrie |
| Jours et heures de consultation | Lundi 08h00–12h30 et 14h00–17h30 ; Mercredi 08h00–12h00 |
| **Durée d'une consultation** | 15, 20, 30, 45 ou 60 minutes |
| Photo | Portrait, format carré de préférence |
| Biographie courte | 2 à 4 lignes : parcours, diplômes, expérience |

> ❓ **Questions précises :**
> - Un médecin peut-il recevoir sur **plusieurs sites** ou uniquement à la clinique ?
> - Les médecins ont-ils des **jours de garde** récurrents pendant lesquels ils ne
>   consultent pas ?
> - Combien de temps à l'avance un patient peut-il réserver ?
>   *(actuellement fixé à 30 jours)*

**Format suggéré pour la réponse** — un simple tableau :

```
Dr Aïcha KONÉ | Pédiatrie | Lun 08h-12h30 + 14h-17h30 | Mar 08h-12h30 | Sam 08h-13h | 30 min
Dr Serge YAO  | Cardiologie | Mer 09h-13h | Ven 09h-13h | 45 min
```

## 1.3 Liste définitive des spécialités

Le site contient actuellement 10 spécialités **données à titre d'exemple** :
médecine générale, gynécologie-obstétrique, pédiatrie, chirurgie générale,
cardiologie, dermatologie, ophtalmologie, ORL, traumatologie-orthopédie,
imagerie médicale.

> ❓ **Questions précises :**
> - Cette liste correspond-elle aux spécialités réellement proposées **à l'ouverture** ?
> - Faut-il en **retirer** ? en **ajouter** ?
> - Certaines spécialités ne doivent-elles **pas** être réservables en ligne
>   (ex. imagerie, qui nécessite une prescription) ?

## 1.4 Nom de domaine et hébergement

| Information demandée | Précision |
|---|---|
| Nom de domaine souhaité | Ex. `clinique-tamarix.ci` — est-il déjà acheté ? |
| Hébergeur retenu | Nom du prestataire |
| Accès à l'hébergement | Identifiants SSH, FTP et base de données |

⚠️ **Contraintes techniques à vérifier avant de souscrire** — l'hébergement doit
impérativement proposer :

- **PHP 8.4** (ou 8.3 minimum)
- **MySQL 8** ou MariaDB 10.6
- **Accès SSH** — *indispensable* pour les rappels automatiques et l'envoi des notifications
- Certificat SSL (Let's Encrypt suffit, généralement gratuit)

> ❌ **Un hébergement mutualisé sans SSH ne conviendra pas** : les rappels J-1
> et les notifications WhatsApp ne pourraient pas fonctionner.

## 1.5 Compte WhatsApp Business

C'est l'élément avec le **délai le plus long** (3 à 7 jours). À lancer en priorité.

| Information demandée | Précision |
|---|---|
| **Numéro dédié WhatsApp Business** | ⚠️ Il ne doit **PAS** déjà être utilisé sur WhatsApp |
| Page Facebook de la clinique | Obligatoire pour créer le compte Meta |
| Documents légaux | RCCM + autorisation d'exercer (Meta vérifie l'entreprise) |
| Nom d'affichage souhaité | Ex. « Clinique Tamarix » — visible par les patients |

> ⚠️ **Piège fréquent :** si le numéro choisi est déjà utilisé sur WhatsApp ou
> WhatsApp Business, il faut d'abord **supprimer ce compte**, ce qui efface les
> conversations. **La solution la plus simple : dédier une nouvelle puce à ce service.**

> ℹ️ **Bonne nouvelle :** le site peut ouvrir **sans** WhatsApp. Les messages sont
> alors préparés et consultables dans le tableau de bord, mais non envoyés.
> L'activation se fait ensuite sans aucune modification du code.

---

# 🟠 NIVEAU 2 — IMPORTANT

## 2.1 Informations légales obligatoires

Les pages « Mentions légales » et « Politique de confidentialité » existent mais
comportent des zones à compléter. **Ces mentions sont une obligation légale.**

| Information demandée |
|---|
| Forme juridique (SARL, SA…) et capital social |
| Numéro **RCCM** |
| Numéro de compte contribuable |
| **Numéro d'autorisation d'ouverture** délivré par le Ministère de la Santé |
| Nom du **directeur de la publication** (généralement le directeur général) |
| Nom et coordonnées de l'**hébergeur** retenu |
| Numéro d'inscription à l'Ordre des Médecins (si applicable) |

> ❓ **Question :** la clinique a-t-elle effectué une **déclaration auprès de l'ARTCI**
> pour le traitement des données de santé ? La loi ivoirienne n° 2013-450 l'exige
> pour ce type de traitement.

## 2.2 Textes institutionnels à valider

Le site contient des textes **rédigés à titre de proposition**. Ils doivent être
validés ou remplacés par la direction.

| Texte | État actuel | Action attendue |
|---|---|---|
| Présentation « Qui sommes-nous » | Proposition rédigée | À valider ou réécrire |
| **Mot de la direction** | Texte d'exemple | À remplacer par le vrai message |
| Les 4 valeurs (Espérance, Excellence, Humanité, Intégrité) | Proposition | À valider ou ajuster |
| Engagements qualité | Proposition (4 engagements) | À valider |
| Description de chaque spécialité | Proposition | À valider par le corps médical |
| **Conseils santé par spécialité** | Proposition | ⚠️ À faire valider médicalement |

> ⚠️ **Point d'attention :** les conseils santé affichés sur le site
> (ex. « Faites contrôler votre tension au moins une fois par an ») ont été
> rédigés de manière prudente et générale, mais ils **doivent être relus et
> validés par un médecin de la clinique** avant publication. La clinique engage
> sa responsabilité sur ces contenus.

## 2.3 Comptes du personnel

Il faut créer les comptes réels du back-office et supprimer les comptes de
démonstration.

Pour **chaque personne**, il nous faut :

| Information | Précision |
|---|---|
| Nom et prénom | |
| Adresse e-mail professionnelle | Servira d'identifiant de connexion |
| **Rôle** | Accueil / Médecin / Administration / Direction |

**Rappel des droits par rôle :**

| Rôle | Ce qu'il peut faire |
|---|---|
| **Accueil** | Voir et gérer tous les rendez-vous, fiches patients, messages |
| **Médecin** | Voir **uniquement ses propres** rendez-vous |
| **Administration** | Tout l'Accueil + gestion médecins, spécialités, articles, comptes, statistiques |
| **Direction** | Accès complet |

> ❓ **Questions précises :**
> - Combien de personnes à l'accueil auront un accès ?
> - Chaque médecin doit-il avoir son propre compte pour consulter son agenda ?
> - Qui sera le **référent** chargé de gérer les contenus du site au quotidien ?

## 2.4 Logo en haute définition

La version actuelle a été extraite du fichier `logo.jpeg` transmis. Elle est
correcte mais perfectible.

**Formats souhaités :**
- Logo **vectoriel** (`.ai`, `.eps` ou `.svg`) — idéal, qualité parfaite à toute taille
- À défaut : **PNG à fond transparent**, largeur minimale 1000 px
- Une version **claire** (pour fonds sombres) si elle existe

## 2.5 Règles de fonctionnement des rendez-vous

Ces règles ont été fixées par défaut. **Elles doivent être confirmées par la clinique.**

| Règle | Valeur actuelle | À confirmer |
|---|---|---|
| Horizon de réservation | 30 jours à l'avance | ❓ |
| Délai minimum avant un RDV | Aucun (réservable jusqu'au dernier créneau libre) | ❓ Faut-il un délai minimum de 2 h ? |
| Statut à la réservation | « En attente » jusqu'à validation par l'accueil | ✅ Conforme au cahier des charges |
| Heure d'envoi du rappel J-1 | 10h00 | ❓ |
| Demandes non confirmées | Clôturées automatiquement après la date | ❓ |
| Annulation par le patient | Possible jusqu'au jour du RDV | ❓ Faut-il l'interdire moins de 24 h avant ? |

---

# 🟢 NIVEAU 3 — CONFORT

## 3.1 Éléments visuels

| Élément | Usage |
|---|---|
| Photos des infrastructures | Galerie : accueil, bloc opératoire, imagerie, laboratoire, chambres |
| Photo de façade en haute définition | Bandeau d'accueil *(une image est déjà utilisée, à remplacer si besoin)* |
| Photos d'équipe | Page « La Clinique » |
| Vidéo de présentation | Optionnelle |

> ⚠️ **Rappel important :** aucune photo ne doit permettre d'identifier un patient
> sans son autorisation écrite.

## 3.2 Réseaux sociaux et présence en ligne

| Information | Usage |
|---|---|
| Page Facebook | Lien dans le pied de page |
| Compte Instagram | Lien dans le pied de page |
| Page LinkedIn | Lien dans le pied de page |
| **Fiche Google Business Profile** | ⚠️ Essentielle pour être trouvé sur Google Maps à Abidjan |

> ❓ **Question :** la fiche Google Business Profile de la clinique existe-t-elle
> déjà ? Sinon, il faut la créer — c'est le premier levier de visibilité locale.

## 3.3 Contenu éditorial

Le blog contient 4 articles rédigés à titre d'exemple (hypertension, vaccination
des enfants, suivi de grossesse, ouverture de la clinique).

> ❓ **Questions :**
> - Ces articles peuvent-ils être conservés après validation médicale ?
> - Qui rédigera les futurs conseils santé ?
> - À quelle fréquence la clinique souhaite-t-elle publier ?

## 3.4 Questions ouvertes sur les évolutions

| Sujet | Question |
|---|---|
| Partenaires / assurances | Faut-il afficher les mutuelles et assurances acceptées ? |
| Tarifs | Faut-il publier une grille tarifaire indicative ? |
| Recrutement | Faut-il une page « Nous rejoindre » ? *(le CDC cite les candidats parmi les cibles)* |
| Langues | Le français seul suffit-il au lancement ? |
| Téléconsultation | Est-ce envisagé à moyen terme ? |

---

# 📋 RÉCAPITULATIF — FICHE DE DEMANDE

À transmettre au chef de projet. Les éléments 🔴 conditionnent l'ouverture.

## Bloquant

- [ ] 🔴 Numéro d'urgence 24h/24
- [ ] 🔴 Numéro du standard
- [ ] 🔴 Adresse e-mail de contact
- [ ] 🔴 Adresse physique complète
- [ ] 🔴 Horaires d'ouverture et jours de fermeture
- [ ] 🔴 **Liste des médecins avec leurs jours et heures de consultation**
- [ ] 🔴 Durée de consultation par médecin
- [ ] 🔴 Liste définitive des spécialités
- [ ] 🔴 Nom de domaine
- [ ] 🔴 Hébergement (PHP 8.4 + MySQL 8 + **SSH**) et ses accès
- [ ] 🔴 Numéro dédié WhatsApp Business (non utilisé sur WhatsApp)
- [ ] 🔴 Page Facebook de la clinique

## Important

- [ ] 🟠 RCCM, forme juridique, capital social
- [ ] 🟠 Numéro d'autorisation du Ministère de la Santé
- [ ] 🟠 Nom du directeur de la publication
- [ ] 🟠 Déclaration ARTCI (à vérifier)
- [ ] 🟠 Mot de la direction (texte définitif)
- [ ] 🟠 Validation médicale des conseils santé
- [ ] 🟠 Photos et biographies des médecins
- [ ] 🟠 Liste du personnel avec les rôles d'accès
- [ ] 🟠 Logo en haute définition (vectoriel ou PNG transparent)
- [ ] 🟠 Confirmation des règles de rendez-vous

## Confort

- [ ] 🟢 Photos des infrastructures
- [ ] 🟢 Liens réseaux sociaux
- [ ] 🟢 Fiche Google Business Profile
- [ ] 🟢 Validation des articles du blog
- [ ] 🟢 Réponses aux questions d'évolution

---

# 📅 Ordre de traitement conseillé

| Ordre | Action | Pourquoi en premier |
|---|---|---|
| **1** | Lancer la création du **compte WhatsApp Business** | Délai Meta de 3 à 7 jours — c'est le chemin critique |
| **2** | Souscrire l'**hébergement** et le domaine | Nécessaire pour finaliser WhatsApp (webhook en HTTPS) |
| **3** | Réunir les **coordonnées** et la **liste des médecins** | Saisie immédiate, effet visible tout de suite |
| **4** | Demander les **informations légales** | Souvent long à obtenir en interne |
| **5** | Collecter **photos et textes** | Peut se faire en parallèle |

> 💡 **Point clé à souligner au chef de projet :** le planning du cahier des charges
> prévoit 2 semaines de développement, 3 jours de tests et 2 jours de formation.
> Le développement est achevé. **Le délai restant dépend désormais entièrement de
> la disponibilité de ces informations**, en particulier de la création du compte
> WhatsApp Business, qui doit être lancée sans attendre.

---

# 🎓 Formation du personnel

Le cahier des charges prévoit une formation (lot 8) et un objectif de prise en
main en moins de 30 minutes.

> ❓ **Questions à poser :**
> - Combien de personnes à former ?
> - Formation sur site ou à distance ?
> - À quelle date, par rapport à l'ouverture de la clinique ?
> - Faut-il un support imprimé en complément du guide d'utilisation ?

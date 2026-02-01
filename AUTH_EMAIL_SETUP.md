# Configuration du système d'authentification et d'email

## Installation et Configuration

### 1. Configuration d'email avec PHPMailer

#### Option A : Gmail avec App Passwords (recommandé pour développement local)

1. Aller à https://myaccount.google.com/apppasswords
2. Générer un mot de passe d'application
3. Copier `.env.example` en `.env` et remplir :

```
MAIL_USE_SMTP=true
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-app-password
MAIL_FROM_EMAIL=noreply@bethelabs.local
MAIL_FROM_NAME=BETHEL LABS
```

#### Option B : PHPMailer via Composer

```bash
composer require phpmailer/phpmailer
```

Ensuite, configurez les variables d'environnement dans `.env`.

#### Option C : Fallback mail() PHP

Si PHPMailer n'est pas disponible ou que les variables d'environnement ne sont pas configurées, le système revient automatiquement à `mail()` natif PHP.

### 2. Flux d'authentification utilisateur

- **Inscription** (`inscription.php`) → Auto-login dans `process_inscription.php`
- **Connexion** (`login.php`) → Cherche l'email dans les inscriptions
- **Déconnexion** (`logout.php`) → Détruit la session

### 3. Flux de changement d'email

1. Utilisateur se connecte et va sur [profile.php](../profile.php)
2. Change son email et valide
3. `process_profile.php` :
   - Si email inchangé : mise à jour directe
   - Si email change : crée `EmailChangeRequest` avec token, envoie email
4. Utilisateur reçoit email avec lien `confirm_email.php?token=...`
5. Clique sur le lien → token validé et email mis à jour
6. Token expire dans 24h

### 4. Gestion administrative

Page : `admin/email_change_requests.php`
- Liste toutes les demandes en attente
- Permet de révoquer une demande (avant que l'utilisateur ne confirme)

**Note** : Cette page ne vérifie actuellement que la présence de `$_SESSION['admin_id']`. À adapter selon votre système de rôles.

## Architecture

```
/config
  - Database.php          (Connexion MySQL)
  - ErrorHandler.php      (Gestion d'erreurs)
  - MailerConfig.php      (PHPMailer wrapper - nouveau)

/models
  - Inscription.php       (incluant updateProfile())
  - EmailChangeRequest.php (nouveau)
  - Testimonial.php

/admin
  - email_change_requests.php (nouveau)

/
  - login.php             (nouveau)
  - process_login.php     (nouveau)
  - logout.php            (nouveau)
  - profile.php           (nouveau)
  - process_profile.php   (modifié pour MailerConfig)
  - confirm_email.php     (nouveau)
```

## Variables d'environnement (.env)

```
MAIL_USE_SMTP=true              # Activer SMTP (sinon utilise mail())
MAIL_HOST=smtp.gmail.com        # Serveur SMTP
MAIL_PORT=587                   # Port (587 pour TLS, 465 pour SSL)
MAIL_USERNAME=...@gmail.com     # Email d'authentification
MAIL_PASSWORD=...               # Mot de passe / App Password
MAIL_FROM_EMAIL=noreply@...     # Email expéditeur
MAIL_FROM_NAME=BETHEL LABS      # Nom expéditeur
```

## Sécurité

- Les tokens de changement d'email sont des 32 octets (256 bits) générés avec `random_bytes()`
- Les demandes expirent après 24h
- Les mots de passe d'email doivent être stockés en `.env` (jamais en source)
- La session utilisateur est requise pour toutes les pages sensibles

## Tests locaux

1. Inscrire un utilisateur via `inscription.php`
2. Aller sur `profile.php`
3. Changer l'email
4. Vérifier l'email reçu et cliquer sur le lien de confirmation
5. Admin peut voir/révoquer les demandes via `admin/email_change_requests.php`

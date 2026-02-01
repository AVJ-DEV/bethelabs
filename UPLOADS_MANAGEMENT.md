# Système de Gestion des Uploads et Témoignages

## Vue d'ensemble

Ce système permet de gérer les uploads de fichiers (images et vidéos) et les témoignages des utilisateurs avec un système d'approbation.

## Structure des dossiers

```
/uploads/
├── /images/     - Stockage des images (news, formations, concours)
└── /videos/     - Stockage des vidéos (news, formations, concours)
```

## Fonctionnalités

### 1. Gestion des Uploads de Fichiers

#### Classe: `MediaManager` (config/MediaManager.php)

**Méthodes principales:**

- `uploadImage($file)` - Upload une image avec validation
- `uploadVideo($file)` - Upload une vidéo avec validation
- `deleteFile($filepath)` - Supprime un fichier
- `isImage($filepath)` - Vérifie si c'est une image
- `isVideo($filepath)` - Vérifie si c'est une vidéo
- `getFullPath($filepath)` - Obtient l'URL complète du fichier

**Limites:**
- Taille maximale: 5 MB par fichier
- Formats images acceptés: JPG, PNG, GIF, WebP
- Formats vidéos acceptés: MP4, AVI, MOV, WMV, FLV, WebM
- Validation MIME type stricte

**Utilisation dans les pages admin:**

```php
require_once __DIR__ . '/../config/MediaManager.php';

$mediaManager = new MediaManager();

// Upload d'image
if (!empty($_FILES['image']['tmp_name'])) {
    $imagePath = $mediaManager->uploadImage($_FILES['image']);
    if ($imagePath) {
        $data['image'] = $imagePath;
    } else {
        $error = implode(', ', $mediaManager->getErrors());
    }
}
```

### 2. Pages Admin Modifiées

Les pages suivantes ont été mises à jour pour supporter les uploads:

- **admin/news.php** - Formulaire avec champs d'upload pour image et vidéo
- **admin/formations.php** - Formulaire avec champs d'upload pour image et vidéo
- **admin/concours.php** - Formulaire avec champs d'upload pour image et vidéo
- **admin/testimonials.php** - Gestion des témoignages avec approbation/rejet

**Tous les formulaires incluent:**
- Attribut `enctype="multipart/form-data"` pour les uploads
- Validation de fichier côté serveur
- Affichage du fichier actuel avec lien
- Messages d'erreur détaillés

### 3. Page Publique des Témoignages

**Fichier:** `testimonials.php`

#### Fonctionnalités:

1. **Formulaire de soumission** pour les utilisateurs:
   - Nom (requis)
   - Email (requis, validé)
   - Commentaire (requis)
   - Note (1-5 étoiles, requis)

2. **Affichage des témoignages approuvés:**
   - Affichage en grille responsive
   - Carte pour chaque témoignage
   - Visualisation des étoiles (⭐)
   - Avatar personnalisé
   - Date d'ajout
   - Design élégant avec animations

3. **Flux d'approbation:**
   - Les nouveaux témoignages sont créés avec statut "en attente"
   - Admin doit approuver (`admin/testimonials.php`)
   - Seuls les témoignages approuvés sont visibles sur la page publique

#### Statuts des témoignages:

- `pending` - En attente d'approbation
- `approved` - Approuvé, visible publiquement
- `rejected` - Rejeté, non visible

### 4. Gestion Admin des Témoignages

**Fichier:** `admin/testimonials.php`

#### Fonctionnalités:

1. **Tableau de gestion:**
   - Liste tous les témoignages (approuvés, en attente, rejetés)
   - Filtrage par statut
   - Affichage de la note en étoiles
   - Badges de couleur pour le statut

2. **Actions:**
   - Modifier le statut (approuvé/rejeté)
   - Modifier le commentaire
   - Supprimer un témoignage
   - Toutes les actions sont loggées

3. **Statuts visuels:**
   - Vert: Approuvé
   - Jaune/Orange: En attente
   - Rouge: Rejeté

## Flux de travail

### Pour les uploads de contenu:

1. Admin se connecte et accède à `admin/news.php`, `admin/formations.php`, ou `admin/concours.php`
2. Admin remplit le formulaire
3. Admin sélectionne une image et/ou vidéo
4. Soumission du formulaire
5. MediaManager valide le fichier
6. Le fichier est déplacé dans `/uploads/images/` ou `/uploads/videos/`
7. Le chemin est stocké en base de données
8. Message de succès affiché

### Pour les témoignages utilisateurs:

1. Utilisateur accède à `testimonials.php`
2. Remplit le formulaire de témoignage
3. Soumet le formulaire
4. Le témoignage est créé avec statut `pending`
5. Admin accède à `admin/testimonials.php`
6. Admin examine les témoignages en attente
7. Admin approuve ou rejette
8. Les témoignages approuvés apparaissent sur la page publique

## Structure des noms de fichiers

Pour éviter les collisions:

- Images: `img_TIMESTAMP_RANDOMHEX.extension`
  - Exemple: `img_1704067200_a1b2c3d4e5.jpg`
  
- Vidéos: `vid_TIMESTAMP_RANDOMHEX.extension`
  - Exemple: `vid_1704067200_f6g7h8i9j0.mp4`

## Configuration

### Limites modifiables (MediaManager.php):

```php
private static $maxFileSize = 5242880; // 5MB en bytes
private static $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
private static $allowedVideoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
```

## Sécurité

- ✅ Validation MIME type
- ✅ Vérification de l'extension
- ✅ Limite de taille de fichier
- ✅ Noms de fichiers sécurisés (hash + timestamp)
- ✅ Validation des permissions utilisateur
- ✅ Logging de toutes les actions

## Dépannage

### Erreurs courantes:

1. **"Le fichier dépasse la taille maximale"**
   - Réduire la taille du fichier
   - Compresser les images/vidéos avant upload

2. **"Format non autorisé"**
   - Vérifier que le format est dans la liste acceptée
   - Convertir le fichier vers un format accepté

3. **"Type MIME non autorisé"**
   - Le serveur ne reconnaît pas le type MIME
   - Vérifier que le fichier n'est pas corrompu

4. **Fichier uploadé mais pas sauvegardé en BD**
   - Vérifier les permissions du dossier `/uploads/`
   - Vérifier la connexion à la base de données

## Permissions des dossiers

Les dossiers `/uploads/images/` et `/uploads/videos/` doivent avoir les permissions:
- Windows: Lecture/Écriture pour l'utilisateur du serveur web
- Linux: `755` ou `775` pour le dossier

## Développement futur

- [ ] Redimensionnement automatique des images
- [ ] Génération de miniatures
- [ ] Suppression des fichiers orphelins (non référencés en BD)
- [ ] Intégration Amazon S3/CDN
- [ ] Watermark sur les images
- [ ] Compression vidéo automatique

<?php

class MediaManager {
    private static $uploadDir = __DIR__ . '/../uploads/';
    private static $maxFileSize = 5242880; // 5MB
    private static $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private static $allowedVideoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
    private static $allowedImageMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static $allowedVideoMimes = ['video/mp4', 'video/x-msvideo', 'video/quicktime', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'];

    private $errors = [];

    /**
     * Upload une image
     * @param array $file - $_FILES['image']
     * @return string|false - Chemin de l'image ou false
     */
    public function uploadImage($file) {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Erreur lors du téléchargement de l\'image.';
            return false;
        }

        // Validation
        if (!$this->validateFile($file, 'image')) {
            return false;
        }

        // Générer un nom unique
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'img_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
        $uploadPath = self::$uploadDir . 'images/' . $filename;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'uploads/images/' . $filename;
        } else {
            $this->errors[] = 'Impossible de déplacer le fichier image.';
            return false;
        }
    }

    /**
     * Upload une vidéo
     * @param array $file - $_FILES['video']
     * @return string|false - Chemin de la vidéo ou false
     */
    public function uploadVideo($file) {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Erreur lors du téléchargement de la vidéo.';
            return false;
        }

        // Validation
        if (!$this->validateFile($file, 'video')) {
            return false;
        }

        // Générer un nom unique
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'vid_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
        $uploadPath = self::$uploadDir . 'videos/' . $filename;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'uploads/videos/' . $filename;
        } else {
            $this->errors[] = 'Impossible de déplacer le fichier vidéo.';
            return false;
        }
    }

    /**
     * Valide un fichier
     * @param array $file - $_FILES['field']
     * @param string $type - 'image' ou 'video'
     * @return bool
     */
    private function validateFile($file, $type) {
        // Vérifier la taille
        if ($file['size'] > self::$maxFileSize) {
            $this->errors[] = 'Le fichier dépasse la taille maximale (5MB).';
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType = mime_content_type($file['tmp_name']);

        if ($type === 'image') {
            if (!in_array($extension, self::$allowedImageExtensions)) {
                $this->errors[] = 'Format d\'image non autorisé. Formats acceptés: ' . implode(', ', self::$allowedImageExtensions);
                return false;
            }
            if (!in_array($mimeType, self::$allowedImageMimes)) {
                $this->errors[] = 'Type MIME d\'image non autorisé.';
                return false;
            }
        } elseif ($type === 'video') {
            if (!in_array($extension, self::$allowedVideoExtensions)) {
                $this->errors[] = 'Format vidéo non autorisé. Formats acceptés: ' . implode(', ', self::$allowedVideoExtensions);
                return false;
            }
            if (!in_array($mimeType, self::$allowedVideoMimes)) {
                $this->errors[] = 'Type MIME vidéo non autorisé.';
                return false;
            }
        }

        return true;
    }

    /**
     * Supprime un fichier
     * @param string $filepath - Chemin du fichier (exemple: uploads/images/img_xxx.jpg)
     * @return bool
     */
    public function deleteFile($filepath) {
        $fullPath = __DIR__ . '/../' . $filepath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Retourne les erreurs
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Vérifier si un fichier est une image basée sur le chemin
     * @param string $filepath
     * @return bool
     */
    public static function isImage($filepath) {
        return strpos($filepath, 'uploads/images/') !== false;
    }

    /**
     * Vérifier si un fichier est une vidéo basée sur le chemin
     * @param string $filepath
     * @return bool
     */
    public static function isVideo($filepath) {
        return strpos($filepath, 'uploads/videos/') !== false;
    }

    /**
     * Obtenir l'URL complète d'un fichier
     * @param string $filepath
     * @return string
     */
    public static function getFullPath($filepath) {
        return 'http://localhost/bethelabs/' . $filepath;
    }
}

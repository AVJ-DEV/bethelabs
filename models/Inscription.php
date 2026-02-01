<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Inscription Model
 * Manages training course registrations
 */
class Inscription extends BaseModel {
    protected $table = 'inscriptions';
    protected $fillable = ['name', 'email', 'phone', 'formation'];

    /**
     * Validate inscription data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        // Name validation
        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        } elseif (strlen($data['name']) < 3) {
            $this->addError("Le nom doit contenir au moins 3 caractères.");
        }

        // Email validation
        if (empty($data['email'])) {
            $this->addError("L'email est requis.");
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("Format d'email invalide.");
        }

        // Phone validation
        if (empty($data['phone'])) {
            $this->addError("Le numéro de téléphone est requis.");
        } elseif (!preg_match('/^[0-9\-\+\(\)\s]{7,}$/', $data['phone'])) {
            $this->addError("Format de téléphone invalide.");
        }

        // Formation validation
        if (empty($data['formation'])) {
            $this->addError("Veuillez sélectionner une formation.");
        }

        return empty($this->errors);
    }

    /**
     * Get recent inscriptions
     */
    public function getRecent($limit = 5) {
        return $this->getAll('created_at DESC', $limit);
    }

    /**
     * Search inscriptions by formation
     */
    public function getByFormation($formation) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE formation LIKE :formation ORDER BY created_at DESC");
        $stmt->execute(['formation' => '%' . $formation . '%']);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get inscriptions by email
     */
    public function getByEmail($email) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email ORDER BY created_at DESC");
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update profile fields (name, email) for a given inscription id without running full inscription validation
     */
    public function updateProfile($id, $data) {
        try {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $data['name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            $this->addError('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            return false;
        }
    }
}
?>

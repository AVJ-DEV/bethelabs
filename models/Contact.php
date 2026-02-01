<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Contact Model
 * Manages contact form submissions
 */
class Contact extends BaseModel {
    protected $table = 'contacts';
    protected $fillable = ['name', 'email', 'message'];

    /**
     * Validate contact data
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

        // Message validation
        if (empty($data['message'])) {
            $this->addError("Le message est requis.");
        } elseif (strlen($data['message']) < 10) {
            $this->addError("Le message doit contenir au moins 10 caractères.");
        }

        return empty($this->errors);
    }

    /**
     * Get recent contacts
     */
    public function getRecent($limit = 5) {
        return $this->getAll('date DESC', $limit);
    }

    /**
     * Search contacts by name or email
     */
    public function searchContacts($keyword) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE name LIKE :keyword OR email LIKE :keyword 
                    ORDER BY date DESC";
            
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchTerm);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la recherche de contacts.");
        }
    }
}

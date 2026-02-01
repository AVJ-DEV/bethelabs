<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Formation Model
 * Manages training courses
 */
class Formation extends BaseModel {
    protected $table = 'formations';
    protected $fillable = [
        'title', 'description', 'content', 'category', 'level', 
        'duration', 'price', 'instructor', 'image', 'video',
        'max_participants', 'status', 'start_date', 'end_date'
    ];

    /**
     * Validate formation data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        // Title validation
        if (empty($data['title'])) {
            $this->addError("Le titre est requis.");
        }

        // Description validation
        if (empty($data['description'])) {
            $this->addError("La description est requise.");
        }

        // Level validation
        if (isset($data['level']) && !in_array($data['level'], ['beginner', 'intermediate', 'advanced'])) {
            $this->addError("Niveau invalide.");
        }

        // Price validation
        if (isset($data['price']) && !is_numeric($data['price'])) {
            $this->addError("Le prix doit être un nombre valide.");
        } elseif (isset($data['price']) && $data['price'] < 0) {
            $this->addError("Le prix ne peut pas être négatif.");
        }

        // Status validation
        if (isset($data['status']) && !in_array($data['status'], ['planned', 'active', 'completed', 'cancelled'])) {
            $this->addError("Statut invalide.");
        }

        return empty($this->errors);
    }

    /**
     * Get active formations
     */
    public function getActive() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY start_date ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des formations.");
        }
    }

    /**
     * Get formations by category
     */
    public function getByCategory($category) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE category = :category ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des formations.");
        }
    }

    /**
     * Check if formation has available slots
     */
    public function hasAvailableSlots($id) {
        try {
            $formation = $this->getById($id);
            
            if (!isset($formation['max_participants'])) {
                return true; // No limit
            }

            return $formation['current_participants'] < $formation['max_participants'];
        } catch (Exception $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Increment participants count
     */
    public function incrementParticipants($id) {
        try {
            if (!$this->hasAvailableSlots($id)) {
                throw new Exception("Nombre maximum de participants atteint.");
            }

            $sql = "UPDATE {$this->table} SET current_participants = current_participants + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de l'incrémentation des participants.");
        }
    }

    /**
     * Decrement participants count
     */
    public function decrementParticipants($id) {
        try {
            $sql = "UPDATE {$this->table} 
                    SET current_participants = GREATEST(current_participants - 1, 0) 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la décrémentation des participants.");
        }
    }
}

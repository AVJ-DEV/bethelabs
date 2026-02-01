<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * News Model
 * Manages news articles
 */
class News extends BaseModel {
    protected $table = 'news';
    protected $fillable = ['title', 'description', 'content', 'image', 'video', 'author', 'status'];

    /**
     * Validate news data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        // Title validation
        if (empty($data['title'])) {
            $this->addError("Le titre est requis.");
        } elseif (strlen($data['title']) < 5) {
            $this->addError("Le titre doit contenir au moins 5 caractères.");
        }

        // Description validation
        if (empty($data['description'])) {
            $this->addError("La description est requise.");
        } elseif (strlen($data['description']) < 20) {
            $this->addError("La description doit contenir au moins 20 caractères.");
        }

        // Status validation
        if (isset($data['status']) && !in_array($data['status'], ['draft', 'published', 'archived'])) {
            $this->addError("Statut invalide.");
        }

        return empty($this->errors);
    }

    /**
     * Get published news
     */
    public function getPublished($limit = null) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT {$limit}";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des actualités.");
        }
    }

    /**
     * Increment views count
     */
    public function incrementViews($id) {
        try {
            $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Get news by status
     */
    public function getByStatus($status) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des actualités.");
        }
    }

    /**
     * Get popular news (most viewed)
     */
    public function getPopular($limit = 5) {
        return $this->getAll('views DESC', $limit);
    }
}

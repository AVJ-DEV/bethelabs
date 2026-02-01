<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Testimonial Model
 * Manages testimonials
 */
class Testimonial extends BaseModel {
    protected $table = 'testimonials';
    protected $fillable = ['name', 'email', 'rating', 'comment', 'image', 'status'];

    /**
     * Validate testimonial data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("Un email valide est requis.");
        }

        if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            $this->addError("La note doit être entre 1 et 5.");
        }

        return empty($this->errors);
    }

    /**
     * Get approved testimonials
     */
    public function getApproved() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'approved' ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération.");
        }
    }

    /**
     * Get approved testimonials with pagination
     */
    public function getApprovedPaginated($limit, $offset) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'approved' ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération paginée.");
        }
    }

    /**
     * Count approved testimonials
     */
    public function countApproved() {
        try {
            $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE status = 'approved'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetch();
            return intval($res['cnt'] ?? 0);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return 0;
        }
    }
}

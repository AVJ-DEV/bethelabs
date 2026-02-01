<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/ErrorHandler.php';

class EmailChangeRequest {
    protected $db;
    protected $table = 'email_change_requests';

    public function __construct() {
        try {
            $database = Database::getInstance();
            $this->db = $database->getConnection();
            $this->ensureTable();
        } catch (Exception $e) {
            ErrorHandler::logError($e);
            throw $e;
        }
    }

    protected function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            inscription_id INT NOT NULL,
            new_email VARCHAR(255) NOT NULL,
            token VARCHAR(128) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (token),
            INDEX (inscription_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->db->exec($sql);
    }

    public function createRequest($inscription_id, $new_email, $token, $expires_at) {
        $sql = "INSERT INTO {$this->table} (inscription_id, new_email, token, expires_at) VALUES (:inscription_id, :new_email, :token, :expires_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':inscription_id' => $inscription_id,
            ':new_email' => $new_email,
            ':token' => $token,
            ':expires_at' => $expires_at
        ]);
    }

    public function getByToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE token = :token LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteById($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}

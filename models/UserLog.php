<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * UserLog Model
 * Manages user activity logs
 */
class UserLog extends BaseModel {
    protected $table = 'user_logs';
    protected $fillable = ['inscription_id', 'action', 'description', 'old_value', 'new_value'];

    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    protected function ensureTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                inscription_id INT NOT NULL,
                action VARCHAR(50) NOT NULL,
                description TEXT,
                old_value VARCHAR(255),
                new_value VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (inscription_id),
                INDEX (action),
                INDEX (created_at),
                FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $this->db->exec($sql);
        } catch (Exception $e) {
            // Table might already exist, continue
        }
    }

    /**
     * Log a user action
     */
    public function log($inscription_id, $action, $description = null, $old_value = null, $new_value = null) {
        try {
            $sql = "INSERT INTO {$this->table} (inscription_id, action, description, old_value, new_value) 
                    VALUES (:inscription_id, :action, :description, :old_value, :new_value)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':inscription_id' => $inscription_id,
                ':action' => $action,
                ':description' => $description,
                ':old_value' => $old_value,
                ':new_value' => $new_value
            ]);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Get logs for a specific user
     */
    public function getUserLogs($inscription_id, $limit = 50) {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE inscription_id = :inscription_id 
                    ORDER BY created_at DESC 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':inscription_id', $inscription_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return [];
        }
    }

    /**
     * Get all logs with optional filters
     */
    public function getAllLogs($limit = 100, $action = null) {
        try {
            $sql = "SELECT ul.*, i.name, i.email 
                    FROM {$this->table} ul
                    JOIN inscriptions i ON ul.inscription_id = i.id";
            
            if ($action) {
                $sql .= " WHERE ul.action = :action";
            }
            
            $sql .= " ORDER BY ul.created_at DESC LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            
            if ($action) {
                $stmt->bindParam(':action', $action, PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return [];
        }
    }
}
?>

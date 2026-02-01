<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Partner Model
 * Manages company partners
 */
class Partner extends BaseModel {
    protected $table = 'partners';
    protected $fillable = ['name', 'firstname', 'expertise', 'image', 'status'];

    public function __construct() {
        parent::__construct();
        $this->ensureTableExists();
        $this->ensureStatusColumn();
    }

    /**
     * Ensure partners table exists
     */
    private function ensureTableExists() {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            // Check if table exists
            $result = $pdo->query("SHOW TABLES LIKE 'partners'");
            if ($result->rowCount() === 0) {
                // Create table if it doesn't exist
                $pdo->exec("
                    CREATE TABLE partners (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        firstname VARCHAR(255),
                        expertise VARCHAR(255),
                        image VARCHAR(255),
                        status ENUM('draft','published') DEFAULT 'draft',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                
                // Insert default partners (published)
                $pdo->exec("
                    INSERT INTO partners (name, firstname, expertise, image, status) VALUES
                    ('Partner', 'Tech', 'Développement Web', 'partner1.jpg','published'),
                    ('Partner', 'Cloud', 'Infrastructure Cloud', 'partner2.jpg','published'),
                    ('Partner', 'Mobile', 'Développement Mobile', 'partner3.jpg','published'),
                    ('Partner', 'Design', 'Design Graphique', 'partner4.jpg','published')
                ");
            }
        } catch (Exception $e) {
            // Table already exists or other error
        }
    }

    private function ensureStatusColumn() {
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            $res = $pdo->query("SHOW COLUMNS FROM partners LIKE 'status'");
            if ($res->rowCount() === 0) {
                $pdo->exec("ALTER TABLE partners ADD COLUMN status ENUM('draft','published') DEFAULT 'draft'");
            }
        } catch (Exception $e) {
            // ignore
        }
    }

    /**
     * Validate partner data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        }

        if (empty($data['firstname'])) {
            $this->addError("Le prénom est requis.");
        }

        if (empty($data['expertise'])) {
            $this->addError("L'expertise est requise.");
        }

        return empty($this->errors);
    }

    /**
     * Get partners by expertise
     */
    public function getByExpertise($expertise) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$this->table} WHERE expertise LIKE :expertise ORDER BY created_at DESC");
        $stmt->execute(['expertise' => '%' . $expertise . '%']);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent partners
     */
    public function getRecent($limit = 4) {
        return $this->getByStatus('published', 'created_at DESC', $limit);
    }

    /**
     * Get partners by status
     */
    public function getByStatus($status = 'published', $orderBy = 'created_at DESC', $limit = null) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY {$orderBy}";
            if ($limit) {
                $sql .= " LIMIT :limit";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $status);
            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des partenaires.");
        }
    }
}
?>

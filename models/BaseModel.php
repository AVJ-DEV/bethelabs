<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ErrorHandler.php';

/**
 * Base Model Class
 * Provides common CRUD operations for all models
 */
abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $errors = [];

    public function __construct() {
        try {
            $database = Database::getInstance();
            $this->db = $database->getConnection();
        } catch (Exception $e) {
            ErrorHandler::logError($e);
            throw new Exception("Impossible de se connecter à la base de données.");
        }
    }

    /**
     * Get all records
     */
    public function getAll($orderBy = null, $limit = null) {
        try {
            $sql = "SELECT * FROM {$this->table}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }
            
            if ($limit) {
                $sql .= " LIMIT {$limit}";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération des données.");
        }
    }

    /**
     * Get record by ID
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            if (!$result) {
                throw new Exception("Enregistrement non trouvé.");
            }
            
            return $result;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération de l'enregistrement.");
        }
    }

    /**
     * Create new record
     */
    public function create($data) {
        try {
            // Filter only fillable fields
            $filteredData = $this->filterFillable($data);
            
            // Validate data
            if (!$this->validate($filteredData)) {
                return false;
            }

            $fields = array_keys($filteredData);
            $placeholders = array_map(function($field) {
                return ":{$field}";
            }, $fields);

            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = $this->db->prepare($sql);
            
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            throw new Exception("Erreur lors de la création de l'enregistrement.");
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            $this->errors[] = "Erreur lors de la création: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        try {
            // Filter only fillable fields
            $filteredData = $this->filterFillable($data);
            
            // Validate data
            if (!$this->validate($filteredData, $id)) {
                return false;
            }

            $fields = array_map(function($field) {
                return "{$field} = :{$field}";
            }, array_keys($filteredData));

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " 
                    WHERE {$this->primaryKey} = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            $this->errors[] = "Erreur lors de la mise à jour: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete record
     */
    public function delete($id) {
        try {
            // Check if record exists
            $this->getById($id);

            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la suppression de l'enregistrement.");
        }
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            
            if (!empty($conditions)) {
                $where = [];
                foreach ($conditions as $field => $value) {
                    $where[] = "{$field} = :{$field}";
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            $stmt = $this->db->prepare($sql);
            
            foreach ($conditions as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return 0;
        }
    }

    /**
     * Search records
     */
    public function search($field, $keyword, $limit = 10) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$field} LIKE :keyword LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la recherche.");
        }
    }

    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = 10, $orderBy = null) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT * FROM {$this->table}";
            
            if ($orderBy) {
                $sql .= " ORDER BY {$orderBy}";
            }
            
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $data = $stmt->fetchAll();
            $total = $this->count();
            
            return [
                'data' => $data,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la pagination.");
        }
    }

    /**
     * Filter only fillable fields
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter($data, function($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Validate data (to be overridden in child classes)
     */
    protected function validate($data, $id = null) {
        return true;
    }

    /**
     * Get validation errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Add error message
     */
    protected function addError($error) {
        $this->errors[] = $error;
    }

    /**
     * Clear errors
     */
    protected function clearErrors() {
        $this->errors = [];
    }
}

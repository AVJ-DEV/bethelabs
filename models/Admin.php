<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Admin Model
 * Manages admin users and authentication
 */
class Admin extends BaseModel {
    protected $table = 'admins';
    protected $fillable = ['username', 'email', 'password', 'first_name', 'last_name', 'role_id', 'status'];

    /**
     * Validate admin data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        // Username validation
        if (empty($data['username'])) {
            $this->addError("Le nom d'utilisateur est requis.");
        } elseif (strlen($data['username']) < 3) {
            $this->addError("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        } elseif ($this->usernameExists($data['username'], $id)) {
            $this->addError("Ce nom d'utilisateur est déjà utilisé.");
        }

        // Email validation
        if (empty($data['email'])) {
            $this->addError("L'email est requis.");
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->addError("Format d'email invalide.");
        } elseif ($this->emailExists($data['email'], $id)) {
            $this->addError("Cet email est déjà utilisé.");
        }

        // Password validation (only for new records or if password is being changed)
        if (($id === null || isset($data['password'])) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $this->addError("Le mot de passe doit contenir au moins 8 caractères.");
            }
        }

        // Role validation
        if (isset($data['role_id']) && !$this->roleExists($data['role_id'])) {
            $this->addError("Rôle invalide.");
        }

        return empty($this->errors);
    }

    /**
     * Create admin with hashed password
     */
    public function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::create($data);
    }

    /**
     * Update admin with optional password hash
     */
    public function update($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // Don't update password if empty
        }
        return parent::update($id, $data);
    }

    /**
     * Authenticate admin
     */
    public function authenticate($username, $password) {
        try {
            $sql = "SELECT a.*, r.role_name 
                    FROM {$this->table} a
                    LEFT JOIN roles r ON a.role_id = r.id
                    WHERE (a.username = :username OR a.email = :email) 
                    AND a.status = 'active'
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $username
            ]);
            
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Update last login
                $this->updateLastLogin($admin['id']);
                
                // Remove password from returned data
                unset($admin['password']);
                
                return $admin;
            }

            return false;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de l'authentification.");
        }
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($id) {
        try {
            $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
        }
    }

    /**
     * Check if username exists
     */
    private function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
            
            if ($excludeId) {
                $sql .= " AND id != :id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            
            if ($excludeId) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Check if email exists
     */
    private function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            
            if ($excludeId) {
                $sql .= " AND id != :id";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            
            if ($excludeId) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Check if role exists
     */
    private function roleExists($roleId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM roles WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Get admin with role information
     */
    public function getById($id) {
        try {
            $sql = "SELECT a.*, r.role_name, r.description as role_description
                    FROM {$this->table} a
                    LEFT JOIN roles r ON a.role_id = r.id
                    WHERE a.id = :id 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            if (!$result) {
                throw new Exception("Administrateur non trouvé.");
            }
            
            return $result;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            throw new Exception("Erreur lors de la récupération de l'administrateur.");
        }
    }

    /**
     * Get all admins with role information
     */
    public function getAll($orderBy = null, $limit = null) {
        try {
            $sql = "SELECT a.*, r.role_name 
                    FROM {$this->table} a
                    LEFT JOIN roles r ON a.role_id = r.id";
            
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
            throw new Exception("Erreur lors de la récupération des administrateurs.");
        }
    }

    /**
     * Get admin permissions
     */
    public function getPermissions($adminId) {
        try {
            $sql = "SELECT p.* 
                    FROM permissions p
                    INNER JOIN role_permissions rp ON p.id = rp.permission_id
                    INNER JOIN admins a ON a.role_id = rp.role_id
                    WHERE a.id = :admin_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return [];
        }
    }

    /**
     * Check if admin has permission
     */
    public function hasPermission($adminId, $permissionName) {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM permissions p
                    INNER JOIN role_permissions rp ON p.id = rp.permission_id
                    INNER JOIN admins a ON a.role_id = rp.role_id
                    WHERE a.id = :admin_id AND p.permission_name = :permission";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);
            $stmt->bindParam(':permission', $permissionName);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }
}

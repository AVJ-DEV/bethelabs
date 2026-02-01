<?php
require_once __DIR__ . '/../config/ErrorHandler.php';
require_once __DIR__ . '/../models/Admin.php';

/**
 * Auth Controller
 * Handles authentication and session management
 */
class AuthController {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
    }

    /**
     * Login admin
     */
    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Méthode non autorisée.");
            }

            // Validate CSRF token
            require_once __DIR__ . '/../config/Csrf.php';
            Csrf::init();
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Jeton CSRF invalide.');
            }

            // Sanitize input
            $username = ErrorHandler::sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validate input
            if (empty($username) || empty($password)) {
                throw new Exception("Nom d'utilisateur et mot de passe requis.");
            }

            // Authenticate
            $admin = $this->adminModel->authenticate($username, $password);

            if (!$admin) {
                throw new Exception("Identifiants incorrects.");
            }

            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role_name'];
            $_SESSION['admin_role_id'] = $admin['role_id'];
            $_SESSION['logged_in'] = true;

            // Log the login
            $this->logAction($admin['id'], 'login', 'auth', 'Connexion réussie');

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => 'dashboard.php'
            ];

        } catch (Exception $e) {
            ErrorHandler::logError($e);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Logout admin
     */
    public function logout() {
        try {
            if (isset($_SESSION['admin_id'])) {
                $this->logAction($_SESSION['admin_id'], 'logout', 'auth', 'Déconnexion');
            }

            session_unset();
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        } catch (Exception $e) {
            ErrorHandler::logError($e);
        }
        
        header('Location: login.php');
        exit();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Require authentication
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Check if admin has permission
     */
    public static function hasPermission($permissionName) {
        if (!self::isLoggedIn()) {
            return false;
        }

        try {
            $adminModel = new Admin();
            return $adminModel->hasPermission($_SESSION['admin_id'], $permissionName);
        } catch (Exception $e) {
            ErrorHandler::logError($e);
            return false;
        }
    }

    /**
     * Require specific permission
     */
    public static function requirePermission($permissionName) {
        if (!self::hasPermission($permissionName)) {
            http_response_code(403);
            die('Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }
    }

    /**
     * Get current admin info
     */
    public static function getCurrentAdmin() {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null,
            'role' => $_SESSION['admin_role'] ?? null,
            'role_id' => $_SESSION['admin_role_id'] ?? null,
        ];
    }

    /**
     * Log admin action
     */
    private function logAction($adminId, $action, $module, $description = '') {
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "INSERT INTO admin_logs (admin_id, action, module, description, ip_address, user_agent) 
                    VALUES (:admin_id, :action, :module, :description, :ip_address, :user_agent)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':admin_id' => $adminId,
                ':action' => $action,
                ':module' => $module,
                ':description' => $description,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
        }
    }

    /**
     * Log current admin action (static helper)
     */
    public static function log($action, $module, $description = '') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!self::isLoggedIn()) {
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "INSERT INTO admin_logs (admin_id, action, module, description, ip_address, user_agent) 
                    VALUES (:admin_id, :action, :module, :description, :ip_address, :user_agent)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':admin_id' => $_SESSION['admin_id'],
                ':action' => $action,
                ':module' => $module,
                ':description' => $description,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            ErrorHandler::logError($e);
        }
    }
}

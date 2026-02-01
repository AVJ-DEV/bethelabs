<?php
/**
 * Error Handler Class
 * Centralized error management system
 */
class ErrorHandler {
    private static $logFile = __DIR__ . '/../logs/errors.log';
    private static $displayErrors = true; // Set to false in production

    /**
     * Initialize error handling
     */
    public static function init() {
        // Ensure logs directory exists
        $logsDir = dirname(self::$logFile);
        if (!file_exists($logsDir)) {
            mkdir($logsDir, 0755, true);
        }

        // Set custom error and exception handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        // Configure error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', self::$displayErrors ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', self::$logFile);
    }

    /**
     * Handle PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorType = self::getErrorType($errno);
        $message = "[$errorType] $errstr in $errfile on line $errline";
        
        self::logError(new ErrorException($message, 0, $errno, $errfile, $errline));
        
        if (self::$displayErrors) {
            self::displayError($errorType, $errstr, $errfile, $errline);
        }

        return true;
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handleException($exception) {
        self::logError($exception);
        
        if (self::$displayErrors) {
            self::displayException($exception);
        } else {
            self::displayGenericError();
        }
    }

    /**
     * Handle fatal errors
     */
    public static function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Log error to file
     */
    public static function logError($exception) {
        $timestamp = date('Y-m-d H:i:s');
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();

        $logMessage = "[$timestamp] " . get_class($exception) . ": $message\n";
        $logMessage .= "File: $file\n";
        $logMessage .= "Line: $line\n";
        $logMessage .= "Stack trace:\n$trace\n";
        $logMessage .= str_repeat('-', 80) . "\n\n";

        error_log($logMessage, 3, self::$logFile);
    }

    /**
     * Display error in development mode
     */
    private static function displayError($type, $message, $file, $line) {
        echo "<div class='alert alert-danger' role='alert'>";
        echo "<strong>$type:</strong> $message<br>";
        echo "<small>Fichier: $file (ligne $line)</small>";
        echo "</div>";
    }

    /**
     * Display exception in development mode
     */
    private static function displayException($exception) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erreur - BetheLabs</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4><i class="bi bi-exclamation-triangle"></i> Une erreur est survenue</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-danger"><strong><?php echo get_class($exception); ?></strong></p>
                        <p><?php echo htmlspecialchars($exception->getMessage()); ?></p>
                        <hr>
                        <p class="text-muted mb-1"><strong>Fichier:</strong> <?php echo $exception->getFile(); ?></p>
                        <p class="text-muted mb-3"><strong>Ligne:</strong> <?php echo $exception->getLine(); ?></p>
                        <details>
                            <summary class="btn btn-sm btn-outline-secondary">Voir la trace complète</summary>
                            <pre class="mt-3 p-3 bg-dark text-white rounded"><?php echo $exception->getTraceAsString(); ?></pre>
                        </details>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Display generic error in production mode
     */
    private static function displayGenericError() {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erreur - BetheLabs</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="alert alert-danger text-center" role="alert">
                    <h4 class="alert-heading">Oups ! Une erreur est survenue</h4>
                    <p>Nous sommes désolés, mais quelque chose s'est mal passé. Notre équipe a été notifiée.</p>
                    <hr>
                    <p class="mb-0">Veuillez réessayer plus tard ou <a href="/" class="alert-link">retourner à l'accueil</a>.</p>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    /**
     * Get error type name
     */
    private static function getErrorType($errno) {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
        ];

        return $errorTypes[$errno] ?? 'Unknown Error';
    }

    /**
     * Create formatted error response for AJAX
     */
    public static function jsonError($message, $code = 500, $data = []) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    /**
     * Create formatted success response for AJAX
     */
    public static function jsonSuccess($message, $data = []) {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }

    /**
     * Validate form data
     */
    public static function validateRequired($data, $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Le champ $field est requis.";
            }
        }
        return $errors;
    }

    /**
     * Sanitize input data
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}

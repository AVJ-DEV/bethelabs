<?php
/**
 * CSRF helper
 * Simple CSRF token generation and validation using session
 */
class Csrf {
    // Token lifetime in seconds
    private const LIFETIME = 3600; // 1 hour
    private const TOKEN_KEY = 'csrf_token';
    private const TOKEN_TIME_KEY = 'csrf_token_time';

    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::TOKEN_KEY]) || empty($_SESSION[self::TOKEN_TIME_KEY]) || (time() - $_SESSION[self::TOKEN_TIME_KEY]) > self::LIFETIME) {
            self::regenerateToken();
        }
    }

    public static function regenerateToken() {
        $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_TIME_KEY] = time();
    }

    public static function getToken() {
        self::init();
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function validateToken($token) {
        self::init();
        if (empty($token) || empty($_SESSION[self::TOKEN_KEY])) return false;
        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function inputField() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::getToken()) . '">';
    }
}

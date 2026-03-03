<?php
// config/database.php
// Edit these values to match your local MySQL setup

define('DB_HOST', 'localhost');
define('DB_NAME', 'sgs_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection.
 * Uses prepared statements by default (SQL injection protection).
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,   // real prepared statements
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;color:red;">
                  <h2>Database Connection Failed</h2>
                  <p>' . htmlspecialchars($e->getMessage()) . '</p>
                  <p>Please check your <code>config/database.php</code> settings.</p>
                 </div>');
        }
    }
    return $pdo;
}
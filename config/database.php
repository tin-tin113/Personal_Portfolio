<?php
/**
 * Database Connection Manager (PDO)
 * Personal Portfolio & Private Admin CMS
 */

if (!defined('APP_INIT')) {
    require_once __DIR__ . '/app.php';
}

class Database {
    private static ?PDO $instance = null;

    // Default Laragon MySQL Configuration
    private static string $host = '127.0.0.1';
    private static string $port = '3306';
    private static string $dbname = 'personal_portfolio';
    private static string $username = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $driver = getenv('DB_DRIVER') ?: '';
            $sqlitePath = dirname(__DIR__) . '/database/portfolio.sqlite';

            // Auto-detect SQLite if specified or if portfolio.sqlite exists and no MySQL env host provided
            if ($driver === 'sqlite' || (!getenv('DB_HOST') && file_exists($sqlitePath))) {
                try {
                    self::$instance = new PDO("sqlite:" . $sqlitePath, null, null, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]);
                    // Enable foreign keys in SQLite
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                    return self::$instance;
                } catch (PDOException $e) {
                    error_log("SQLite connection error: " . $e->getMessage());
                    if (APP_ENV === 'development') {
                        die("SQLite Connection Error: " . htmlspecialchars($e->getMessage()));
                    }
                    die("Database connection failure.");
                }
            }

            // MySQL Connection (Local Laragon or Cloud MySQL via Env)
            $host = getenv('DB_HOST') ?: self::$host;
            $port = getenv('DB_PORT') ?: self::$port;
            $dbname = getenv('DB_NAME') ?: self::$dbname;
            $username = getenv('DB_USER') ?: self::$username;
            $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : self::$password;

            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                    $host,
                    $port,
                    $dbname,
                    self::$charset
                );

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];

                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // If database doesn't exist yet (Error code 1049), auto-create it
                if ($e->getCode() === 1049 || str_contains($e->getMessage(), 'Unknown database')) {
                    try {
                        $rawDsn = sprintf("mysql:host=%s;port=%s;charset=%s", $host, $port, self::$charset);
                        $tempPdo = new PDO($rawDsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . $dbname . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        // Retry connection with dbname
                        self::$instance = new PDO($dsn, $username, $password, $options);
                        return self::$instance;
                    } catch (Exception $ex) {
                        // Fall through to error handler below
                    }
                }

                if (APP_ENV === 'development') {
                    die('<div style="font-family:sans-serif; padding:20px; background:#fee2e2; color:#991b1b; border:1px solid #f87171; border-radius:8px; margin:20px;">' .
                        '<h2>Database Connection Error</h2>' .
                        '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                        '<p>Please verify that MySQL or SQLite database is properly configured.</p>' .
                        '</div>');
                } else {
                    error_log("Database connection error: " . $e->getMessage());
                    die("Database connection failure. Please contact administrator.");
                }
            }
        }
        return self::$instance;
    }
}

/**
 * Global helper function to get PDO database instance
 */
function db(): PDO {
    return Database::getConnection();
}

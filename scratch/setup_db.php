<?php
require_once __DIR__ . '/../config/app.php';
require_once CONFIG_PATH . '/database.php';

try {
    $pdo = db();
    echo "1. Connection: OK\n";

    $schemaSql = file_get_contents(ROOT_PATH . '/database/schema.sql');
    $pdo->exec($schemaSql);
    echo "2. Schema Execution: OK\n";

    $seedsSql = file_get_contents(ROOT_PATH . '/database/seeds.sql');
    $pdo->exec($seedsSql);
    echo "3. Seeds Execution: OK\n";

    $pCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $uCount = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    echo "4. Database Status: {$pCount} projects, {$uCount} admin user(s).\n";

} catch (Exception $e) {
    echo "Database Setup Error: " . $e->getMessage() . "\n";
}

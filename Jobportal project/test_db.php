<?php
require_once 'config/db.php';
try {
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbName = $stmt->fetchColumn();
    echo "Connected successfully to: " . $dbName;
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

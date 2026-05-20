<?php
require_once 'config/db.php';
try {
    // Check if sector column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM jobs LIKE 'sector'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE jobs ADD COLUMN sector VARCHAR(100) AFTER description");
        echo "Successfully added 'sector' column to 'jobs' table.\n";
    } else {
        echo "'sector' column already exists.\n";
    }

    // Also check if status ENUM matches what we use in dashboard (accepted, etc)
    // database.sql says: status ENUM('pending', 'reviewed', 'accepted', 'rejected')
    // and dashboard uses 'accepted' for shortlisted. This matches.

    // Let's also check if we need to update sample data with sectors
    $pdo->exec("UPDATE jobs SET sector = 'Tech' WHERE sector IS NULL OR sector = ''");
    echo "Updated existing jobs with default sector 'Tech'.\n";

} catch (Exception $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>

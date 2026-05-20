<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$sql_file = 'c:/xampp/htdocs/Jobportal project/database.sql';

try {
    $pdo = new PDO("mysql:host=$host;port=3307", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents($sql_file);
    
    // Split SQL by semicolon, but be careful with multi-line statements
    // For simplicity, we can just execute the whole thing if it's one block
    // or use a better parser. But most SQL files work with exec() if not too large.
    
    $pdo->exec($sql);
    echo "Database updated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

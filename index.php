<?php
$host = 'localhost';
$dbname = 'myapp';
$user = 'myapp_user';
$pass = 'MyApp123!';

echo "<h1>🚀 App déployée via Ansible + Git</h1>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    echo "<p style='color:green'>✅ Connexion MariaDB OK !</p>";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS visites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("INSERT INTO visites () VALUES ()");
    
    $count = $pdo->query("SELECT COUNT(*) FROM visites")->fetchColumn();
    echo "<p>👀 Nombre de visites : <strong>$count</strong></p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>❌ Erreur DB : " . $e->getMessage() . "</p>";
}
?>
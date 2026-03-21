<?php
$db_host = getenv('POSTGRES_HOST');
$db_port = getenv('POSTGRES_PORT');
$db_user = getenv('POSTGRES_USER');
$db_pass = getenv('POSTGRES_PASSWORD');
$db_name = getenv('POSTGRES_DB');

if (!$db_host) {
    die("Database not configured. Make sure the PostgreSQL addon is linked.\n");
}

try {
    $pdo = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        image VARCHAR(255)
    )");

    // Hash a password using: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
    // Replace the hash below with your own generated one
    $hashed = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // hash for "password"
    $stmt = $pdo->prepare("INSERT INTO users (username, password, image) VALUES (:username, :password, :image) ON CONFLICT (username) DO NOTHING");
    $stmt->execute([
        'username' => 'alice',
        'password' => $hashed,
        'image' => 'https://via.placeholder.com/150?text=Alice'
    ]);
    echo "Table created and test user 'alice' added (password: 'password').\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
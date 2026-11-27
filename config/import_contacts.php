<?php
// Import contacts table into database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aci-newdb', 'root', 'aranetacity');
    $sql = file_get_contents(__DIR__ . '/schema/contacts.sql');
    $pdo->exec($sql);
    echo "✓ Contacts table imported successfully!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

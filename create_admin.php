<?php // RUN ONCE in the browser, then DELETE this file.
require 'db.php';
$pdo->prepare('INSERT IGNORE INTO users (full_name,email,password,role) VALUES (?,?,?,"admin")')
    ->execute(['Administrator','admin@portal.com',password_hash('Admin@123',PASSWORD_DEFAULT)]);
echo 'Admin created. Delete this file now.';

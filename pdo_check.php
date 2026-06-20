<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=chronorex;port=3306', 'root', '');
$stmt = $pdo->query('SELECT COUNT(*) as cnt FROM expense_categories');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Categories Count: " . $row['cnt'] . "\n";

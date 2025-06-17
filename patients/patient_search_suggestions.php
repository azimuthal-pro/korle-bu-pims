<?php
require_once '../includes/dbconfig.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if ($term) {
    $stmt = $pdo->prepare("SELECT full_name FROM patients WHERE full_name LIKE :term OR medical_record_no LIKE :term LIMIT 10");
    $stmt->execute([':term' => "$term%"]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($results);
}
?>


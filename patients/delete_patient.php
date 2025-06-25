<?php
require_once '../includes/dbconfig.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid request. No patient ID provided.";
    exit;
}

$id = $_GET['id'];

// Confirm patient exists before deleting (optional but safe)
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    echo "Patient not found.";
    exit;
}

// Perform deletion
$stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
$stmt->execute([$id]);

// Redirect back to the list
header("Location: ../modules/view_patient.php");
exit;
?>

<?php
require_once '../includes/dbconfig.php';

// Get the history ID from the URL
$historyId = $_GET['id'] ?? null;

if (!$historyId) {
    echo "Invalid request. No history ID provided.";
    exit;
}

// Optional: Fetch history first to ensure it exists (and maybe for logging)
$stmtCheck = $pdo->prepare("SELECT * FROM medical_history WHERE id = ?");
$stmtCheck->execute([$historyId]);
$history = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$history) {
    echo "Record not found.";
    exit;
}

// Proceed to delete
$stmtDelete = $pdo->prepare("DELETE FROM medical_history WHERE id = ?");
$stmtDelete->execute([$historyId]);

// Redirect back to the history list
header("Location: view_history.php");
exit;
?>

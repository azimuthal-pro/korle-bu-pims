<?php
session_start();
require_once '../includes/dbconfig.php';

$patientId = $_SESSION['patient_id'] ?? null;

if (!$patientId) {
    echo "No patient selected.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $diagnosis_date = $_POST['diagnosis_date'];
    $medication = $_POST['medication'];
    $dose = $_POST['dose'];

    $stmt = $pdo->prepare("INSERT INTO medical_history 
        (patient_id, diagnosis, diagnosis_date, medication, dose, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())");

    $stmt->execute([$patientId, $diagnosis, $diagnosis_date, $medication, $dose]);

    // Optionally clear the patient ID from session after first use
    //unset($_SESSION['patient_id']);

    // Redirect or show success message
    header("Location: ../modules/view_history.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Medical History</title>
    <link rel="stylesheet" href="/korle-bu-pims/assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Add Medical History</h2>
        <form method="post">
            <label>Diagnosis:</label><br>
            <textarea name="diagnosis" required></textarea><br><br>

            <label>Diagnosis Date:</label><br>
            <input type="date" name="diagnosis_date" required><br><br>

            <label>Medication:</label><br>
            <input type="text" name="medication" required><br><br>

            <label>Dose:</label><br>
            <input type="text" name="dose" required><br><br>

            <button type="submit">Add History</button>
        </form>
    </div>
</body>
</html>

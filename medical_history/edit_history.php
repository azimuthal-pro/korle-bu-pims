<?php
require_once '../includes/dbconfig.php';

// Check if history ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid history ID.";
    exit;
}

$historyId = $_GET['id'];

// Fetch the medical history record
$stmt = $pdo->prepare("SELECT * FROM medical_history WHERE id = ?");
$stmt->execute([$historyId]);
$history = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$history) {
    echo "Medical history not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $diagnosis_date = $_POST['diagnosis_date'];
    $medication = $_POST['medication'];
    $dose = $_POST['dose'];

    $update = $pdo->prepare("UPDATE medical_history SET 
        diagnosis = :diagnosis,
        diagnosis_date = :diagnosis_date,
        medication = :medication,
        dose = :dose
        WHERE id = :id");

    $update->execute([
        ':diagnosis' => $diagnosis,
        ':diagnosis_date' => $diagnosis_date,
        ':medication' => $medication,
        ':dose' => $dose,
        ':id' => $historyId
    ]);

    header("Location: view_history.php?patient_id=" . $history['patient_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Medical History</title>
    <link rel="stylesheet" href="/korle-bu-pims/assets/css/style.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Medical History</h2>
        <form method="post">
            <label>Diagnosis:</label><br>
            <textarea name="diagnosis" required><?= htmlspecialchars($history['diagnosis']) ?></textarea><br><br>

            <label>Diagnosis Date:</label><br>
            <input type="date" name="diagnosis_date" value="<?= htmlspecialchars($history['diagnosis_date']) ?>" required><br><br>

            <label>Medication:</label><br>
            <input type="text" name="medication" value="<?= htmlspecialchars($history['medication']) ?>" required><br><br>

            <label>Dose:</label><br>
            <input type="text" name="dose" value="<?= htmlspecialchars($history['dose']) ?>" required><br><br>

            <button type="submit">Update History</button>
        </form>
    </div>
</body>

</html>
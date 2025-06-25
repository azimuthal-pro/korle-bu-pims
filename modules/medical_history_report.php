<?php
require_once '../includes/dbconfig.php';

$query = "SELECT mh.*, p.medical_record_no 
          FROM medical_history mh
          JOIN patients p ON mh.patient_id = p.id
          ORDER BY mh.created_at DESC";

$stmt = $pdo->query($query);
$histories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/view.css">
</head>
<body>
    <h2>🩺 Medical History Reports</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Medical No</th>
                <th>Diagnosis</th>
                <th>Date</th>
                <th>Medication</th>
                <th>Dose</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($histories as $history): ?>
                <tr>
                    <td><?= htmlspecialchars($history['medical_record_no']) ?></td>
                    <td><?= htmlspecialchars($history['diagnosis']) ?></td>
                    <td><?= htmlspecialchars($history['diagnosis_date']) ?></td>
                    <td><?= htmlspecialchars($history['medication']) ?></td>
                    <td><?= htmlspecialchars($history['dose']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>


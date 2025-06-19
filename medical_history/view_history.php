<?php
session_start();
require_once '../includes/dbconfig.php';

$patientId = $_SESSION['patient_id'] ?? null;

if (!$patientId) {
    echo "No patient selected.";
    exit;
}

$term = $_GET['term'] ?? '';

if ($term) {
    $stmt = $pdo->prepare("
        SELECT mh.*, p.medical_record_no, p.full_name 
        FROM medical_history mh
        JOIN patients p ON mh.patient_id = p.id
        WHERE p.medical_record_no LIKE :term OR p.full_name LIKE :term
        ORDER BY mh.created_at ASC
    ");
    $stmt->execute([':term' => "%$term%"]);
} else {
    $stmt = $pdo->prepare("
        SELECT mh.*, p.medical_record_no, p.full_name 
        FROM medical_history mh
        JOIN patients p ON mh.patient_id = p.id
        ORDER BY mh.created_at ASC
    ");
    $stmt->execute();
}

$histories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Medical History</title>
    <link rel="stylesheet" href="/korle-bu-pims/assets/css/view.css">
</head>

<body>

    <div class="view-wrapper">
        <h2>Medical History of All Available Patients</h2>
        <?php if (count($histories) > 0): ?>
            <div class="table-container">
                <form method="get" class="search-form">
                    <input type="text" name="term" placeholder="Search by Name or Medical No" value="<?= htmlspecialchars($_GET['term'] ?? '') ?>">
                    <button type="submit">Search</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Medical Record Number</th>
                            <th>Patient</th>
                            <th>Diagnosis</th>
                            <th>Diagnosis Date</th>
                            <th>Medication</th>
                            <th>Dose</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($histories as $entry): ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['medical_record_no']) ?></td>
                                <td><?= htmlspecialchars($entry['full_name']) ?></td>
                                <td><?= htmlspecialchars($entry['diagnosis']) ?></td>
                                <td><?= htmlspecialchars($entry['diagnosis_date']) ?></td>
                                <td><?= htmlspecialchars($entry['medication']) ?></td>
                                <td><?= htmlspecialchars($entry['dose']) ?></td>
                                <td>
                                    <a class="btn edit" href="edit_history.php?id=<?= $entry['id'] ?>">Edit</a>
                                    <a class="btn delete" href="delete_history.php?id=<?= $entry['id'] ?>" onclick="return confirm('Are you sure you want to delete this history record?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-msg">No history records found for this patient.</p>
        <?php endif; ?>
    </div>
</body>

</html>
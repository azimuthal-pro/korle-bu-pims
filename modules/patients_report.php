<?php
require_once '../includes/dbconfig.php';

$stmt = $pdo->query("SELECT * FROM patients ORDER BY full_name ASC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h2>📋 Patient Reports</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Medical No</th>
                <th>Full Name</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= htmlspecialchars($patient['medical_record_no']) ?></td>
                    <td><?= htmlspecialchars($patient['full_name']) ?></td>
                    <td><?= htmlspecialchars($patient['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($patient['gender']) ?></td>
                    <td><?= htmlspecialchars($patient['phone']) ?></td>
                    <td><?= htmlspecialchars($patient['address']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>



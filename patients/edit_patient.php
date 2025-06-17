<?php
require_once '../includes/dbconfig.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid patient ID.";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    echo "Patient not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE patients SET 
        medical_record_no = :medical_record_no,
        full_name = :full_name,
        date_of_birth = :date_of_birth,
        gender = :gender,
        phone = :phone,
        address = :address
        WHERE id = :id");
    $stmt->execute([
        ':medical_record_no' => $_POST['medical_record_no'],
        ':full_name' => $_POST['full_name'],
        ':date_of_birth' => $_POST['date_of_birth'],
        ':gender' => $_POST['gender'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address'],
        ':id' => $id
    ]);
    header("Location: view_patient.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>
    <link rel="stylesheet" href="/korle-bu-pims/assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Patient</h2>
        <form method="post">
            <label for="medical_record_no">Medical Record No</label>
            <input id="medical_record_no" name="medical_record_no" value="<?= htmlspecialchars($patient['medical_record_no']) ?>" required>

            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name" value="<?= htmlspecialchars($patient['full_name']) ?>" required>

            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($patient['date_of_birth']) ?>" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= $patient['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>

            <label for="phone">Phone</label>
            <input id="phone" name="phone" value="<?= htmlspecialchars($patient['phone']) ?>">

            <label for="address">Address</label>
            <textarea id="address" name="address"><?= htmlspecialchars($patient['address']) ?></textarea>

            <button type="submit">Update Patient</button>
        </form>
    </div>
</body>
</html>

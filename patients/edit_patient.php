<?php
require_once '../includes/dbconfig.php';
require_once '../includes/header.php';


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

<div class="container">
    <h2>Edit Patient</h2>
    <form method="post">
        <input name="medical_record_no" value="<?= $patient['medical_record_no'] ?>" required><br><br>
        <input name="full_name" value="<?= $patient['full_name'] ?>" required><br><br>
        <input type="date" name="date_of_birth" value="<?= $patient['date_of_birth'] ?>" required><br><br>
        <select name="gender">
            <option <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
            <option <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            <option <?= $patient['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
        </select><br><br>
        <input name="phone" value="<?= $patient['phone'] ?>"><br><br>
        <textarea name="address"><?= $patient['address'] ?></textarea><br><br>
        <button type="submit">Update</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>

<?php
session_start();
require_once '../includes/dbconfig.php';
//require_once '../includes/header.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO patients 
        (medical_record_no, full_name, date_of_birth, gender, phone, address)
        VALUES (:medical_record_no, :full_name, :date_of_birth, :gender, :phone, :address)");
    $stmt->execute([
        ':medical_record_no' => $_POST['medical_record_no'],
        ':full_name' => $_POST['full_name'],
        ':date_of_birth' => $_POST['date_of_birth'],
        ':gender' => $_POST['gender'],
        ':phone' => $_POST['phone'],
        ':address' => $_POST['address']
    ]);

    // Get inserted patient ID
    $patientId = $pdo->lastInsertId();

    // Save ID to session and redirect without putting it in the URL
    $_SESSION['patient_id'] = $patientId;
    header("Location: ../medical_history/add_history.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
</head>

<body>
    <div class="form-container">
        <h2>Add Patient</h2>
        <?php if (!empty($success)): ?>
            <p class="success-msg"><?= $success ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Medical Record No</label>
            <input name="medical_record_no" required>

            <label>Full Name</label>
            <input name="full_name" required>

            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" required>

            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>

            <label>Phone</label>
            <input name="phone">

            <label>Address</label>
            <textarea name="address"></textarea>

            <button type="submit">Save</button>
        </form>
    </div>
</body>

</html>



 <?php //require_once '../includes/footer.php';?> 
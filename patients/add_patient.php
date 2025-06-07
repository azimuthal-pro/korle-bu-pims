<?php
require_once '../includes/dbconfig.php';
require_once '../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medical_record_no = $_POST['medical_record_no'];
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Insert into DB
    try {
        $stmt = $pdo->prepare("INSERT INTO patients (medical_record_no, full_name, date_of_birth, gender, phone, address) 
                               VALUES (:medical_record_no, :full_name, :date_of_birth, :gender, :phone, :address)");
        $stmt->execute([
            ':medical_record_no' => $medical_record_no,
            ':full_name' => $full_name,
            ':date_of_birth' => $date_of_birth,
            ':gender' => $gender,
            ':phone' => $phone,
            ':address' => $address
        ]);
        $success = "Patient added successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="container">
    <h2>Add New Patient</h2>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php elseif (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Medical Record Number:</label><br>
        <input type="text" name="medical_record_no" required><br><br>

        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br><br>

        <label>Date of Birth:</label><br>
        <input type="date" name="date_of_birth" required><br><br>

        <label>Gender:</label><br>
        <select name="gender" required>
            <option value="">-- Select --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone"><br><br>

        <label>Address:</label><br>
        <textarea name="address" rows="3"></textarea><br><br>

        <button type="submit">Add Patient</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>

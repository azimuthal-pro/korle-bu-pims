<?php
require_once '../includes/dbconfig.php';

$searchTerm = $_GET['search'] ?? '';

// Search query
if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT * FROM patients 
        WHERE full_name LIKE :term OR medical_record_no LIKE :term
        ORDER BY id ASC");
    $stmt->execute([':term' => "%$searchTerm%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM patients ORDER BY id ASC");
}
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Patients - Korle-Bu PIMS</title>
    <link rel="stylesheet" href="/korle-bu-pims/assets/css/view.css">
</head>

<body>
    <div class="view-wrapper">
        <h2>Patient Records</h2>

      <form method="get" class="search-form" onsubmit="return false;">
    <div class="search-input-wrapper">
        <input type="text" id="searchBox" name="search" placeholder="Search by name or medical number" autocomplete="off">
        <button type="submit" onclick="submitSearch()">Search</button>
    </div>
    <div id="suggestions" class="suggestions-box"></div>
</form>



        <?php if (count($patients) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Medical No</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <!-- <td><?//= htmlspecialchars($patient['id'])?></td> -->
                                <td><?= htmlspecialchars($patient['medical_record_no']) ?></td>
                                <td><?= htmlspecialchars($patient['full_name']) ?></td>
                                <td><?= htmlspecialchars($patient['date_of_birth']) ?></td>
                                <td><?= htmlspecialchars($patient['gender']) ?></td>
                                <td><?= htmlspecialchars($patient['phone']) ?></td>
                                <td><?= htmlspecialchars($patient['address']) ?></td>
                                <td>
                                    <a class="btn edit" href="edit_patient.php?id=<?= $patient['id'] ?>">Edit</a>
                                    <a class="btn delete" href="delete_patient.php?id=<?= $patient['id'] ?>" onclick="return confirm('Delete this patient?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-msg">No patients found.</p>
        <?php endif; ?>
    </div>


    <script>
        const searchBox = document.getElementById("searchBox");
        const suggestionsBox = document.getElementById("suggestions");

        searchBox.addEventListener("input", () => {
            const query = searchBox.value;
            if (query.length < 2) {
                suggestionsBox.innerHTML = "";
                return;
            }

            fetch(`patient_search_suggestions.php?term=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = "";
                    data.forEach(name => {
                        const div = document.createElement("div");
                        div.classList.add("suggestion");
                        div.textContent = name;
                        div.onclick = () => {
                            searchBox.value = name;
                            suggestionsBox.innerHTML = "";
                        };
                        suggestionsBox.appendChild(div);
                    });
                });
        });

        function submitSearch() {
            const value = searchBox.value;
            if (value) {
                window.location.href = `view_patient.php?search=${encodeURIComponent(value)}`;
            }
        }

        console.log(searchBox)
    </script>

</body>

</html>
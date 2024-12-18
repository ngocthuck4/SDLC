<?php
include 'db.php'; // Include database connection

// Initialize variables
$editMode = false;
$student = [
    'StudentID' => '',
    'FullName' => '',
    'DateOfBirth' => '',
    'Email' => '',
    'Phone' => '',
    'Major' => ''
];

// Handle search functionality
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Handle logic for adding, editing, and deleting students
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $id = $_POST['StudentID'];
        $name = $_POST['FullName'];
        $dob = $_POST['DateOfBirth'];
        $email = $_POST['Email'];
        $phone = $_POST['Phone'];
        $major = $_POST['Major'];

        if ($id) {
            // Edit student
            $sql = "UPDATE Students SET FullName = ?, DateOfBirth = ?, Email = ?, Phone = ?, Major = ? WHERE StudentID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $dob, $email, $phone, $major, $id);
        } else {
            // Add new student
            $sql = "INSERT INTO Students (FullName, DateOfBirth, Email, Phone, Major) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $dob, $email, $phone, $major);
        }

        if ($stmt->execute()) {
            header("Location: Student.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $sql = "DELETE FROM Students WHERE StudentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: Student.php");
        exit();
    }
}

// Load data for editing a student
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $sql = "SELECT * FROM Students WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    }
}

// Search query to filter students based on full name
if ($searchTerm) {
    $sql = "SELECT * FROM Students WHERE FullName LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no search term, fetch all students
    $sql = "SELECT * FROM Students";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            border-radius: 20px;
        }
        .btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center text-primary mb-4">Student Management</h2>

    <!-- Search Form -->
    <form method="GET" action="Student.php" class="mb-4">
        <input type="text" class="form-control" name="search" placeholder="Search by student name" value="<?= htmlspecialchars($searchTerm) ?>" />
        <button type="submit" class="btn btn-primary mt-2">Search</button>
    </form>

    <!-- Form for adding/editing students -->
    <div class="card mb-5">
        <div class="card-header bg-info text-white">
            <h5><?= $editMode ? 'Edit Student' : 'Add New Student' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="StudentID" value="<?= $student['StudentID'] ?>">
                <div class="mb-3">
                    <label for="FullName" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="FullName" name="FullName" value="<?= $student['FullName'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="DateOfBirth" class="form-label">Date of Birth:</label>
                    <input type="date" class="form-control" id="DateOfBirth" name="DateOfBirth" value="<?= $student['DateOfBirth'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="Email" name="Email" value="<?= $student['Email'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="Phone" class="form-label">Phone Number:</label>
                    <input type="text" class="form-control" id="Phone" name="Phone" value="<?= $student['Phone'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="Major" class="form-label">Major:</label>
                    <input type="text" class="form-control" id="Major" name="Major" value="<?= $student['Major'] ?>" required>
                </div>
                <button type="submit" name="save" class="btn btn-success"><?= $editMode ? 'Update' : 'Add New' ?></button>
                <?php if ($editMode): ?>
                    <a href="Student.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Student List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Student List</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Date of Birth</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Major</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['StudentID'] ?></td>
                        <td><?= $row['FullName'] ?></td>
                        <td><?= $row['DateOfBirth'] ?></td>
                        <td><?= $row['Email'] ?></td>
                        <td><?= $row['Phone'] ?></td>
                        <td><?= $row['Major'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['StudentID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline-block;">
                                <button type="submit" name="delete" value="<?= $row['StudentID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>


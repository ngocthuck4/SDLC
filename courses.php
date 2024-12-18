<?php
include 'db.php'; // Database connection

// Initialize variables
$editMode = false;
$course = [
    'CourseID' => '',
    'CourseName' => '',
    'Description' => '',
    'Credits' => ''
];

// Handle logic for adding, editing, and deleting courses
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save'])) {
        $id = $_POST['CourseID'];
        $name = $_POST['CourseName'];
        $description = $_POST['Description'];
        $credits = $_POST['Credits'];

        if ($id) {
            // Edit course
            $sql = "UPDATE Courses SET CourseName = ?, Description = ?, Credits = ? WHERE CourseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $name, $description, $credits, $id);
        } else {
            // Add new course
            $sql = "INSERT INTO Courses (CourseName, Description, Credits) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $description, $credits);
        }

        if ($stmt->execute()) {
            header("Location: courses.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $sql = "DELETE FROM Courses WHERE CourseID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: courses.php");
        exit();
    }
}

// Load data for editing a course
if (isset($_GET['edit'])) {
    $editMode = true;
    $id = $_GET['edit'];
    $sql = "SELECT * FROM Courses WHERE CourseID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
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
    <h2 class="text-center text-primary mb-4">Course Management</h2>

    <!-- Form for adding/editing courses -->
    <div class="card mb-5">
        <div class="card-header bg-info text-white">
            <h5><?= $editMode ? 'Edit Course' : 'Add New Course' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="CourseID" value="<?= $course['CourseID'] ?>">
                <div class="mb-3">
                    <label for="CourseName" class="form-label">Course Name:</label>
                    <input type="text" class="form-control" id="CourseName" name="CourseName" value="<?= $course['CourseName'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="Description" class="form-label">Description:</label>
                    <textarea class="form-control" id="Description" name="Description" rows="3" required><?= $course['Description'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="Credits" class="form-label">Credits:</label>
                    <input type="number" class="form-control" id="Credits" name="Credits" value="<?= $course['Credits'] ?>" required>
                </div>
                <button type="submit" name="save" class="btn btn-success"><?= $editMode ? 'Update' : 'Add New' ?></button>
                <?php if ($editMode): ?>
                    <a href="courses.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Course List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5>Course List</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Credits</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM Courses");
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['CourseID'] ?></td>
                        <td><?= $row['CourseName'] ?></td>
                        <td><?= $row['Description'] ?></td>
                        <td><?= $row['Credits'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['CourseID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" style="display: inline-block;">
                                <button type="submit" name="delete" value="<?= $row['CourseID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
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


<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Home</title>
    <style>
        /* CSS from the previous code */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        .container h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #4CAF50;
        }

        .container ul {
            list-style-type: none;
            padding: 0;
        }

        .container ul li {
            margin: 15px 0;
        }

        .container ul li a {
            text-decoration: none;
            font-size: 18px;
            color: #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #f2f2f2;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .container ul li a:hover {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Teacher!</h2>
        <ul>
            <li><a href="grades.php">Manage Grades</a></li>
            <li><a href="courses.php">Manage Courses</a></li>
        </ul>
    </div>
</body>
</html>


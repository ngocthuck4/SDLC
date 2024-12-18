
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_management";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Truy vấn để kiểm tra xem người dùng có tồn tại không
    $sql = "SELECT u.id, u.username, u.password, r.name AS role FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Chuyển hướng người dùng dựa trên vai trò
            if ($user['role'] == 'admin') {
                header("Location: admin_home.php");
            } else if ($user['role'] == 'teacher') {
                header("Location: teacher_home.php");
            }
        } else {
            echo "Mật khẩu không đúng.";
        }
    } else {
        echo "Không tìm thấy người dùng với tên đăng nhập này.";
    }
}
?>

<!-- HTML Form for Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* CSS from previous answer */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        form h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        form label {
            text-align: left;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
            display: block;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        form button {
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #45a049;
        }

        form .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        form a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        form a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>

        <p>Don't have an account? <a href="regit.php">Register</a></p>  <!-- Link to Register page -->
    </form>
</body>
</html>

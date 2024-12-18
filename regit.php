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

// Xử lý khi người dùng gửi form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $role_id = $_POST["role_id"];  // Lấy vai trò từ form

    // Mã hóa mật khẩu trước khi lưu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Thực hiện truy vấn để thêm người dùng vào bảng `users`
    $sql = "INSERT INTO users (username, password, email, role_id) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $hashed_password, $email, $role_id);

    if ($stmt->execute()) {
        // Đăng ký thành công, chuyển hướng về trang login
        echo "Registration successful!";
        header("Location: login.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* CSS từ phần trước */
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
        form input[type="password"],
        form input[type="email"],
        form select {
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
    <form method="POST" action="regit.php">
        <h2>Register</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="role">Role:</label>
        <select name="role_id" required>
            <option value="1">Admin</option>
            <option value="2">Teacher</option>
        </select><br><br>

        <button type="submit">Register</button>

        <!-- Liên kết đến trang đăng nhập nếu người dùng đã có tài khoản -->
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>

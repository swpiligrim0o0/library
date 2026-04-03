<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT users.id, users.username, profiles.full_name
            FROM users
            JOIN profiles ON users.id = profiles.user_id
            WHERE users.username='$username' AND users.password='$password'";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['fullname'] = $row['full_name'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 300px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 20px;
        }

        .input-box {
            margin-bottom: 15px;
        }

        .input-box input {
            width: 100%;
            padding: 10px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
        }

        .input-box input:focus {
            border-color: #4facfe;
        }

        .btn {
            width: 100%;
            padding: 10px;
            border: none;
            background: #4facfe;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background: #00c6ff;
        }

        .link {
            margin-top: 15px;
            font-size: 14px;
        }

        .link a {
            text-decoration: none;
            color: #4facfe;
        }
    </style>
</head>
<body>

<div class="login-box">

    <h2>Login</h2>

    <form method="POST">
        <div class="input-box">
            <input type="text" name="fullname" placeholder="Full Name" required>
        </div>
        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <?php if (!empty($error)) { ?>
    <p style="color:red; margin-bottom:15px;">
        <?php echo $error; ?>
    </p>
        <?php } ?>

        <button class="btn" type="submit">Login</button>
    </form>

    <div class="link">
        Don't have account? <a href="register.php">Register</a>
    </div>
</div>

</body>
</html>
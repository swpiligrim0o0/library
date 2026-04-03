<?php
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $fullname && $password) {

        $sql = "INSERT INTO users (username, password)
                VALUES ('$username', '$password')";
        mysqli_query($conn, $sql);

        $user_id = mysqli_insert_id($conn);
        $sql2 = "INSERT INTO profiles (user_id, full_name)
                 VALUES ('$user_id', '$fullname')";
        mysqli_query($conn, $sql2);

        $message = "Register successful!";
    } else {
        $message = "Please fill all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 320px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 2px solid #ccc;
            outline: none;
        }

        input:focus {
            border-color: #43e97b;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: #43e97b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background: #2ed573;
        }

        .msg {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .link {
            margin-top: 15px;
        }

        .link a {
            color: #43e97b;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Register</h2>

    <?php if (!empty($message)) { ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button class="btn" type="submit">Register</button>
    </form>

    <div class="link">
        Already have account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>
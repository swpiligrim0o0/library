<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Dashboard</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        .box {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background: #4facfe;
            color: white;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .logout {
            float: right;
            text-decoration: none;
            background: red;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h1>Welcome, <?php echo $_SESSION['username']; ?></h1>
<a class="logout" href="logout.php">Logout</a>

<div class="box">
    <h2>User Profile</h2>

    <?php
    $sql = "SELECT users.username, profiles.full_name
            FROM users
            JOIN profiles ON users.id = profiles.user_id
            WHERE users.id = " . $_SESSION['user_id'];

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    ?>

    <p><b>Username:</b> <?php echo $row['username']; ?></p>
    <p><b>Full Name:</b> <?php echo $row['full_name']; ?></p>
</div>

<div class="box">
    <h2>Authors & Books</h2>

    <table>
        <tr>
            <th>Author</th>
            <th>Book</th>
        </tr>

        <?php
        $sql = "SELECT authors.name AS author, books.title AS book
                FROM authors
                JOIN books ON authors.id = books.author_id";

        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['author']}</td>
                    <td>{$row['book']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>
<div class="box">
    <h2>Books & Categories</h2>

    <table>
        <tr>
            <th>Book</th>
            <th>Category</th>
        </tr>

        <?php
        $sql = "SELECT books.title AS book, categories.name AS category
                FROM books
                JOIN book_category ON books.id = book_category.book_id
                JOIN categories ON book_category.category_id = categories.id";

        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['book']}</td>
                    <td>{$row['category']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
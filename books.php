<?php
include "config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Books List</title>
</head>
<body>

<h2>Books and Authors</h2>

<?php
$sql = "SELECT books.title, authors.name 
        FROM books
        JOIN authors ON books.author_id = authors.id";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "Book: " . $row['title'] . " | Author: " . $row['name'] . "<br>";
}
?>

</body>
</html>
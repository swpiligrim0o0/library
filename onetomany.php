<?php
include("config.php");

mysqli_query($conn, "INSERT INTO Authors (name) VALUES ('George Orwell')");
mysqli_query($conn, "INSERT INTO Books (title, author_id) VALUES ('1984', 1)");
mysqli_query($conn, "INSERT INTO Books (title, author_id) VALUES ('Animal Farm', 1)");

$sql = "
SELECT Authors.name, Books.title
FROM Authors
JOIN Books ON Authors.author_id = Books.author_id
";

$result = mysqli_query($conn, $sql);

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($data as $row) {
    echo $row['name'] . " -> " . $row['title'] . "<br>";
}
?>
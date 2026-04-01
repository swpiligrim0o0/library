<?php
include("config.php");

mysqli_query($conn, "INSERT INTO BookLoans (reader_id, book_id) VALUES (1,1)");
mysqli_query($conn, "INSERT INTO BookLoans (reader_id, book_id) VALUES (1,2)");

$sql = "
SELECT Readers.name, Books.title
FROM BookLoans
JOIN Readers ON BookLoans.reader_id = Readers.reader_id
JOIN Books ON BookLoans.book_id = Books.book_id
WHERE Readers.reader_id = 1
";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach ($data as $row) {
    echo $row['name'] . " -> " . $row['title'] . "<br>";
}
?>
<?php
include("config.php");
mysqli_query($conn, "INSERT INTO Cards (reader_id, issue_date) VALUES (1, '2026-04-01')");
$sql = "
SELECT Readers.name, Cards.issue_date
FROM Readers
JOIN Cards ON Readers.reader_id = Cards.reader_id
";

$result = mysqli_query($conn, $sql);

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($data as $row) {
    echo $row['name'] . " -> karta sanasi: " . $row['issue_date'] . "<br>";
}
?>
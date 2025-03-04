<?php
include './conn.php';
include './config.php';
include './connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $statement = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $statement->bind_param("s", $id);
    $statement->execute();
    $result = $statement->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $old_image = $row["filename"];
    } else {
        echo "No results found.";
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {

        if (file_exists($old_image)) {
            unlink($old_image);
        }
        echo "deleted successfully";
        header("Location:updateproduct.php");

    } else {
        echo "error";
    }
}
?>
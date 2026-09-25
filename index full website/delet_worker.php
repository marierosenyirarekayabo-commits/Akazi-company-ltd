<?php

require_once "database.php";

$id = $_GET["id"];

$sql = "DELETE FROM workers WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: admin.php");

    exit;

} else {

    echo "Error deleting worker.";

}

?>
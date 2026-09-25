<?php

require_once "database.php";

$id = $_GET["id"];

$sql = "SELECT * FROM workers WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$worker = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST["full_name"];
    $age = $_POST["age"];
    $job = $_POST["job"];
    $location = $_POST["location"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    $sql = "UPDATE workers SET
            full_name=?,
            age=?,
            job=?,
            references_info=?,
            location=?,
            phone=?,
            email=?
            WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sisssssi",
        $full_name,
        $age,
        $job,
        $references_info,
        $location,
        $phone,
        $email,
        $id
    );

    $stmt->execute();

    header("Location: admin.php");

    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Worker</title>

<style>

body {
    font-family: Arial;
    background: #f5f7fb;
}

.box {
    width: 500px;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
}

input,
textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
}

</style>

</head>

<body>

<div class="box">

<h2>Edit Worker</h2>

<form method="POST">

<label>Full Name</label>

<input type="text"
       name="full_name"
       value="<?= htmlspecialchars($worker["full_name"]) ?>"
       required>

<label>Age</label>

<input type="number"
       name="age"
       value="<?= $worker["age"] ?>"
       required>

<label>Job</label>

<input type="text"
       name="job"
       value="<?= htmlspecialchars($worker["job"]) ?>"
       required>

<label>References</label>

<textarea name="references_info"><?= htmlspecialchars($worker["references_info"]) ?></textarea>

<label>Location</label>

<input type="text"
       name="location"
       value="<?= htmlspecialchars($worker["location"]) ?>"
       required>

<label>Phone</label>

<input type="text"
       name="phone"
       value="<?= htmlspecialchars($worker["phone"]) ?>"
       required>

<label>Email</label>

<input type="email"
       name="email"
       value="<?= htmlspecialchars($worker["email"]) ?>"
       required>

<button type="submit">
Update Worker
</button>

</form>

</div>

</body>

</html>
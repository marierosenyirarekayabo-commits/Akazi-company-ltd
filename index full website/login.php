<?php

session_start();

require_once "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == "admin") {

            header("Location: admin.php");

            exit;

        }

    } else {

        $error = "Email cyangwa password ntabwo ari byo.";

    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Admin Login</title>

<style>

body {
    font-family: Arial;
    background: #f5f7fb;
}

.box {
    width: 400px;
    margin: 100px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
}

.error {
    color: red;
}

</style>

</head>

<body>

<div class="box">

<h2>Administrator Login</h2>

<?php if ($error) { ?>

<p class="error">
<?= $error ?>
</p>

<?php } ?>

<form method="POST">

<input type="email"
       name="email"
       placeholder="Email"
       required>

<input type="password"
       name="password"
       placeholder="Password"
       required>

<button type="submit">
Login
</button>

</form>

</div>

</body>

</html>
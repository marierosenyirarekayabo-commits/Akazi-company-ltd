<?php
session_start();

/* =====================================================
   DATABASE CONNECTION
   ===================================================== */

$host = "localhost";
$user = "root";
$password = "";
$database = "employee";
\xampp\htdocs\akazi\

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


/* =====================================================
   LOGOUT
   ===================================================== */

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}


/* =====================================================
   LOGIN
   ===================================================== */

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password_input = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    if (
        $user_data &&
        password_verify($password_input, $user_data["password"]) &&
        $user_data["role"] === "admin"
    ) {

        $_SESSION["user_id"] = $user_data["id"];
        $_SESSION["role"] = $user_data["role"];

        header("Location: index.php?page=admin");
        exit;

    } else {

        $error = "Email cyangwa password ntabwo ari byo.";

    }

    $stmt->close();
}


/* =====================================================
   CHECK ADMIN LOGIN
   ===================================================== */

$is_admin = (
    isset($_SESSION["user_id"]) &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
);


/* =====================================================
   SAVE WORKER
   ===================================================== */

$success = "";
$save_error = "";

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["save_worker"])
) {

    if (!$is_admin) {
        die("Access denied.");
    }

    $full_name = trim($_POST["full_name"]);
    $age = (int) $_POST["age"];
    $job = trim($_POST["job"]);
    $references_info = trim($_POST["references_info"]);
    $location = trim($_POST["location"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);

    $certificate = "";

    /* Certificate upload */

    if (
        isset($_FILES["certificate"]) &&
        $_FILES["certificate"]["error"] === 0
    ) {

        $folder = "certificates/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $original_name = basename($_FILES["certificate"]["name"]);

        $extension = strtolower(
            pathinfo($original_name, PATHINFO_EXTENSION)
        );

        $allowed = ["pdf", "jpg", "jpeg", "png"];

        if (!in_array($extension, $allowed)) {

            $save_error = "Certificate igomba kuba PDF, JPG, JPEG cyangwa PNG.";

        } else {

            $new_name =
                time() . "_" .
                uniqid() . "." .
                $extension;

            $certificate = $folder . $new_name;

            if (!move_uploaded_file(
                $_FILES["certificate"]["tmp_name"],
                $certificate
            )) {

                $save_error = "Certificate ntabwo yabitswe.";

            }
        }
    }


    /* Save worker */

    if ($save_error === "") {

        $sql = "INSERT INTO workers
                (
                    full_name,
                    age,
                    job,
                    certificate,
                    references_info,
                    location,
                    phone,
                    email
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sissssss",
            $full_name,
            $age,
            $job,
            $certificate,
            $references_info,
            $location,
            $phone,
            $email
        );

        if ($stmt->execute()) {

            $success = "Worker registered successfully!";

        } else {

            $save_error = "Error: " . $conn->error;

        }

        $stmt->close();
    }
}


/* =====================================================
   DELETE WORKER
   ===================================================== */

if (
    $is_admin &&
    isset($_GET["delete"])
) {

    $id = (int) $_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM workers WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php?page=admin");
    exit;
}


/* =====================================================
   UPDATE WORKER
   ===================================================== */

if (
    $is_admin &&
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["update_worker"])
) {

    $id = (int) $_POST["id"];

    $full_name = trim($_POST["full_name"]);
    $age = (int) $_POST["age"];
    $job = trim($_POST["job"]);
    $references_info = trim($_POST["references_info"]);
    $location = trim($_POST["location"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);

    $sql = "UPDATE workers SET
            full_name = ?,
            age = ?,
            job = ?,
            references_info = ?,
            location = ?,
            phone = ?,
            email = ?
            WHERE id = ?";

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
    $stmt->close();

    header("Location: index.php?page=admin");
    exit;
}


/* =====================================================
   GET PAGE
   ===================================================== */

$page = $_GET["page"] ?? "login";


/* =====================================================
   IF NOT LOGGED IN
   ===================================================== */

if (!$is_admin) {

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>AKAZI COMPANY LTD - Login</title>

        <style>

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: Arial, sans-serif;

                min-height: 100vh;

                display: flex;
                justify-content: center;
                align-items: center;

                padding: 20px;

                background:
                    linear-gradient(
                        rgba(220,235,255,.90),
                        rgba(190,215,255,.94)
                    ),
                    url("logo.png");

                background-size: 750px;
                background-position: center;
                background-repeat: no-repeat;
            }

            .login-box {

                width: 100%;
                max-width: 430px;

                background: rgba(255,255,255,.97);

                padding: 40px;

                border-radius: 20px;

                box-shadow:
                    0 15px 45px rgba(0,50,120,.25);
            }

            .logo {
                width: 200px;
                display: block;
                margin: 0 auto 20px;
            }

            h1 {
                text-align: center;
                color: #0758d9;
                margin-bottom: 8px;
            }

            .subtitle {
                text-align: center;
                color: #555;
                margin-bottom: 25px;
            }

            label {
                display: block;
                margin-top: 15px;
                margin-bottom: 7px;
                font-weight: bold;
                color: #14213d;
            }

            input {
                width: 100%;
                padding: 13px;

                border: 2px solid #d8e2f2;
                border-radius: 8px;

                outline: none;
            }

            input:focus {
                border-color: #0758d9;
            }

            button {
                width: 100%;
                padding: 14px;

                margin-top: 25px;

                border: none;
                border-radius: 8px;

                background: #0758d9;

                color: white;

                font-size: 16px;
                font-weight: bold;

                cursor: pointer;
            }

            button:hover {
                background: #0648b3;
            }

            .error {
                color: red;
                background: #ffecec;
                padding: 10px;
                border-radius: 7px;
                margin-bottom: 15px;
            }

        </style>

    </head>

    <body>

        <div class="login-box">

            <img src="logo.png"
                 class="logo"
                 alt="AKAZI COMPANY LTD">

            <h1>Administrator Login</h1>

            <p class="subtitle">
                AKAZI COMPANY LTD
            </p>

            <?php if ($error): ?>

                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter email"
                    required
                >

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

                <button type="submit" name="login">
                    Login
                </button>

            </form>

        </div>

    </body>

    </html>

    <?php

    exit;
}


/* =====================================================
   EDIT PAGE
   ===================================================== */

if ($page === "edit") {

    $id = (int) ($_GET["id"] ?? 0);

    $stmt = $conn->prepare(
        "SELECT * FROM workers WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $worker = $result->fetch_assoc();

    $stmt->close();

    if (!$worker) {
        die("Worker not found.");
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <title>Edit Worker</title>

        <style>

            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial;

                min-height: 100vh;

                padding: 30px;

                background:
                    linear-gradient(
                        rgba(220,235,255,.90),
                        rgba(190,215,255,.94)
                    ),
                    url("logo.png");

                background-size: 700px;
                background-position: center;
                background-repeat: no-repeat;
            }

            .box {
                width: 100%;
                max-width: 600px;

                margin: auto;

                background: white;

                padding: 35px;

                border-radius: 18px;

                box-shadow: 0 15px 40px rgba(0,0,0,.20);
            }

            h1 {
                text-align: center;
                color: #0758d9;
                margin-bottom: 25px;
            }

            label {
                display: block;
                font-weight: bold;
                margin-top: 15px;
                margin-bottom: 7px;
            }

            input,
            textarea {
                width: 100%;

                padding: 12px;

                border: 2px solid #ddd;

                border-radius: 8px;
            }

            textarea {
                height: 100px;
                resize: vertical;
            }

            button {
                width: 100%;

                padding: 14px;

                margin-top: 25px;

                background: #0758d9;

                color: white;

                border: none;

                border-radius: 8px;

                font-weight: bold;

                cursor: pointer;
            }

            .back {
                display: block;
                text-align: center;
                margin-top: 15px;
                color: #0758d9;
                text-decoration: none;
            }

        </style>

    </head>

    <body>

        <div class="box">

            <h1>Edit Worker</h1>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?= $worker["id"] ?>"
                >

                <label>Full Name</label>

                <input
                    type="text"
                    name="full_name"
                    value="<?= htmlspecialchars($worker["full_name"]) ?>"
                    required
                >

                <label>Age</label>

                <input
                    type="number"
                    name="age"
                    value="<?= $worker["age"] ?>"
                    required
                >

                <label>Type of Work</label>

                <input
                    type="text"
                    name="job"
                    value="<?= htmlspecialchars($worker["job"]) ?>"
                    required
                >

                <label>References</label>

                <textarea
                    name="references_info"
                ><?= htmlspecialchars($worker["references_info"]) ?></textarea>

                <label>Location</label>

                <input
                    type="text"
                    name="location"
                    value="<?= htmlspecialchars($worker["location"]) ?>"
                    required
                >

                <label>Phone</label>

                <input
                    type="text"
                    name="phone"
                    value="<?= htmlspecialchars($worker["phone"]) ?>"
                    required
                >

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($worker["email"]) ?>"
                    required
                >

                <button
                    type="submit"
                    name="update_worker"
                >
                    Update Worker
                </button>

            </form>

            <a class="back" href="index.php?page=admin">
                ← Back to Dashboard
            </a>

        </div>

    </body>

    </html>

    <?php

    exit;
}


/* =====================================================
   ADMIN DASHBOARD
   ===================================================== */

$sql = "SELECT * FROM workers ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>AKAZI COMPANY LTD - Administrator</title>

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial, sans-serif;

    min-height: 100vh;

    background:
        linear-gradient(
            rgba(225,238,255,.93),
            rgba(200,220,250,.95)
        ),
        url("logo.png");

    background-size: 800px;

    background-position: center;

    background-repeat: no-repeat;

    background-attachment: fixed;
}


/* HEADER */

header {

    background: linear-gradient(
        90deg,
        #064bb8,
        #0758d9
    );

    color: white;

    padding: 18px 30px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-shadow:
        0 4px 15px rgba(0,0,0,.15);
}

.header-left {

    display: flex;

    align-items: center;

    gap: 15px;
}

.header-logo {

    width: 75px;

    height: 75px;

    object-fit: contain;

    background: white;

    border-radius: 10px;
}

header h1 {

    margin: 0;

    font-size: 25px;
}

header p {

    margin: 5px 0 0;

    opacity: .9;
}

.logout {

    color: white;

    text-decoration: none;

    background: #dc2626;

    padding: 10px 16px;

    border-radius: 7px;

    font-weight: bold;
}

.logout:hover {
    background: #b91c1c;
}


/* MAIN */

.container {

    padding: 30px;

    overflow-x: auto;
}

.dashboard-card {

    background: rgba(255,255,255,.96);

    padding: 25px;

    border-radius: 18px;

    box-shadow:
        0 12px 35px rgba(0,0,0,.15);
}

.dashboard-title {

    color: #0758d9;

    margin-top: 0;
}


/* REGISTER BUTTON */

.register {

    display: inline-block;

    background: #0758d9;

    color: white;

    text-decoration: none;

    padding: 12px 18px;

    border-radius: 8px;

    font-weight: bold;

    margin-bottom: 20px;
}

.register:hover {
    background: #0648b3;
}


/* TABLE */

.table-wrapper {
    overflow-x: auto;
}

table {

    width: 100%;

    min-width: 1100px;

    border-collapse: collapse;

    background: white;
}

th {

    background: #0758d9;

    color: white;

    padding: 13px;

    text-align: left;
}

td {

    padding: 12px;

    border-bottom: 1px solid #ddd;

    vertical-align: top;
}

tr:hover {
    background: #f1f6ff;
}


/* LINKS */

.view {

    color: #0758d9;

    text-decoration: none;

    font-weight: bold;
}

.edit {

    color: #059669;

    text-decoration: none;

    font-weight: bold;
}

.delete {

    color: #dc2626;

    text-decoration: none;

    font-weight: bold;
}


/* WORKER FORM */

.form-section {

    margin-top: 30px;

    max-width: 700px;

    background: white;

    padding: 30px;

    border-radius: 18px;

    box-shadow:
        0 12px 35px rgba(0,0,0,.15);
}

.form-section h2 {

    color: #0758d9;

    margin-top: 0;
}

.form-section label {

    display: block;

    margin-top: 15px;

    margin-bottom: 7px;

    font-weight: bold;

    color: #14213d;
}

.form-section input,
.form-section textarea {

    width: 100%;

    padding: 13px;

    border: 2px solid #d8e2f2;

    border-radius: 8px;

    outline: none;
}

.form-section input:focus,
.form-section textarea:focus {

    border-color: #0758d9;
}

.form-section textarea {

    height: 90px;

    resize: vertical;
}

.save-btn {

    width: 100%;

    padding: 14px;

    margin-top: 25px;

    background: #0758d9;

    color: white;

    border: none;

    border-radius: 8px;

    font-weight: bold;

    font-size: 16px;

    cursor: pointer;
}

.save-btn:hover {
    background: #0648b3;
}

.success {

    background: #dcfce7;

    color: #166534;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 15px;
}

.error-message {

    background: #fee2e2;

    color: #991b1b;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 15px;
}


/* MOBILE */

@media (max-width: 700px) {

    header {

        flex-direction: column;

        gap: 15px;

        text-align: center;
    }

    .header-left {

        flex-direction: column;
    }

    .container {

        padding: 15px;
    }

    .dashboard-card {

        padding: 15px;
    }

}

</style>

</head>


<body>


<!-- HEADER -->

<header>

    <div class="header-left">

        <img
            src="logo.png"
            class="header-logo"
            alt="AKAZI COMPANY LTD"
        >

        <div>

            <h1>
                AKAZI COMPANY LTD
            </h1>

            <p>
                Administrator Dashboard
            </p>

        </div>

    </div>


    <a
        href="index.php?logout=1"
        class="logout"
    >
        Logout
    </a>

</header>



<div class="container">


    <!-- DASHBOARD -->

    <div class="dashboard-card">

        <h2 class="dashboard-title">
            All Workers
        </h2>


        <!-- REGISTER NEW WORKER -->

        <a
            href="index.php?page=register"
            class="register"
        >
            + Register New Worker
        </a>


        <?php if ($success): ?>

            <div class="success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <?php if ($save_error): ?>

            <div class="error-message">
                <?= htmlspecialchars($save_error) ?>
            </div>

        <?php endif; ?>


        <!-- WORKERS TABLE -->

        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>

                <th>Full Name</th>

                <th>Age</th>

                <th>Job</th>

                <th>Certificate</th>

                <th>References</th>

                <th>Location</th>

                <th>Phone</th>

                <th>Email</th>

                <th>Actions</th>

            </tr>


            <?php while ($worker = $result->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= $worker["id"] ?>
                </td>

                <td>
                    <?= htmlspecialchars($worker["full_name"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($worker["age"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($worker["job"]) ?>
                </td>

                <td>

                    <?php if (!empty($worker["certificate"])): ?>

                        <a
                            href="<?= htmlspecialchars($worker["certificate"]) ?>"
                            target="_blank"
                            class="view"
                        >
                            View Certificate
                        </a>

                    <?php else: ?>

                        No certificate

                    <?php endif; ?>

                </td>

                <td>

                    <?= nl2br(
                        htmlspecialchars(
                            $worker["references_info"]
                        )
                    ) ?>

                </td>

                <td>
                    <?= htmlspecialchars($worker["location"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($worker["phone"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($worker["email"]) ?>
                </td>

                <td>

                    <a
                        href="index.php?page=edit&id=<?= $worker["id"] ?>"
                        class="edit"
                    >
                        Edit
                    </a>

                    <br><br>

                    <a
                        href="index.php?delete=<?= $worker["id"] ?>"
                        class="delete"
                        onclick="return confirm('Delete this worker?')"
                    >
                        Delete
                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

        </div>

    </div>



    <!-- REGISTER WORKER -->

    <?php if ($page === "register"): ?>

    <div class="form-section">

        <h2>
            Worker Registration
        </h2>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <label>
                Full Name
            </label>

            <input
                type="text"
                name="full_name"
                placeholder="Enter full name"
                required
            >


            <label>
                Age
            </label>

            <input
                type="number"
                name="age"
                min="18"
                placeholder="Enter age"
                required
            >


            <label>
                Type of Work
            </label>

            <input
                type="text"
                name="job"
                placeholder="Enter job"
                required
            >


            <label>
                Certificate
            </label>

            <input
                type="file"
                name="certificate"
                accept=".pdf,.jpg,.jpeg,.png"
            >


            <label>
                References
            </label>

            <textarea
                name="references_info"
                placeholder="Enter references"
            ></textarea>


            <label>
                Location
            </label>

            <input
                type="text"
                name="location"
                placeholder="Enter location"
                required
            >


            <label>
                Phone Number
            </label>

            <input
                type="tel"
                name="phone"
                placeholder="Enter phone number"
                required
            >


            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Enter email"
                required
            >


            <button
                type="submit"
                name="save_worker"
                class="save-btn"
            >
                Save Worker
            </button>


        </form>

    </div>

    <?php endif; ?>


</div>


</body>

</html>
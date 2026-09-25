<?php

require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST["full_name"];
    $age = $_POST["age"];
    $job = $_POST["job"];
    $references_info = $_POST["references_info"];
    $location = $_POST["location"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    $certificate = "";

    if (isset($_FILES["certificate"]) &&
        $_FILES["certificate"]["error"] == 0) {

        $folder = "certificates/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $file_name = time() . "_" .
                     basename($_FILES["certificate"]["name"]);

        $certificate = $folder . $file_name;

        move_uploaded_file(
            $_FILES["certificate"]["tmp_name"],
            $certificate
        );
    }

    $sql = "INSERT INTO workers
            (full_name, age, job, certificate,
             references_info, location, phone, email)
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

        echo "<h2>Worker registered successfully!</h2>";
        echo "<a href='worker_form.php'>Register another worker</a><br>";
        echo "<a href='admin.php'>Go to Administrator Dashboard</a>";

    } else {

        echo "Error: " . $conn->error;

    }

    $stmt->close();
    $conn->close();
}

?>
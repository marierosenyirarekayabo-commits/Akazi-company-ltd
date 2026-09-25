<form action="save_worker.php" method="POST" enctype="multipart/form-data">
<?php

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "abel";

$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get form data
$full_name = $_POST['full_name'];
$age = $_POST['age'];
$job = $_POST['job'];
$references_info = $_POST['references'];
$location = $_POST['location'];
$phone = $_POST['phone'];
$email = $_POST['email'];

// Certificate upload
$certificate = $_FILES['certificate']['name'];
$tmp_name = $_FILES['certificate']['tmp_name'];

// Folder for certificates
$upload_folder = "certificates/";

// Create folder if it doesn't exist
if (!is_dir($upload_folder)) {
    mkdir($upload_folder, 0777, true);
}

// Move certificate
$certificate_path = $upload_folder . basename($certificate);

if (!move_uploaded_file($tmp_name, $certificate_path)) {
    die("Certificate upload failed.");
}

// Insert data into database
$sql = "INSERT INTO workers
        (full_name, age, job, certificate, references_info, location, phone, email)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sissssss",
    $full_name,
    $age,
    $job,
    $certificate_path,
    $references_info,
    $location,
    $phone,
    $email
);

// Save
if (mysqli_stmt_execute($stmt)) {
    echo "Worker registered successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
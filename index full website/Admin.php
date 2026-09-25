<?php

session_start();

require_once "database.php";

$sql = "SELECT * FROM workers ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Administrator Dashboard</title>

<style>

body {
    font-family: Arial;
    background: #f5f7fb;
    margin: 0;
}

header {
    background: #111827;
    color: white;
    padding: 20px;
    text-align: center;
}

.container {
    padding: 30px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th,
td {
    padding: 12px;
    border: 1px solid #ddd;
}

th {
    background: #2563eb;
    color: white;
}

a {
    text-decoration: none;
    color: #2563eb;
}

.delete {
    color: red;
}

</style>

</head>

<body>

<header>

<h1>AKAZI COMPANY LTD</h1>

<h2>Administrator Dashboard</h2>

</header>

<div class="container">

<h2>All Workers</h2>

<a href="worker_form.php">
    + Register New Worker
</a>

<br><br>

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

<?php

while ($worker = $result->fetch_assoc()) {

?>

<tr>

<td>
<?= $worker["id"] ?>
</td>

<td>
<?= htmlspecialchars($worker["full_name"]) ?>
</td>

<td>
<?= $worker["age"] ?>
</td>

<td>
<?= htmlspecialchars($worker["job"]) ?>
</td>

<td>

<?php if (!empty($worker["certificate"])) { ?>

<a href="<?= htmlspecialchars($worker["certificate"]) ?>"
   target="_blank">

View Certificate

</a>

<?php } else { ?>

No certificate

<?php } ?>

</td>

<td>
<?= nl2br(htmlspecialchars($worker["references_info"])) ?>
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

<a href="edit_worker.php?id=<?= $worker["id"] ?>">
Edit
</a>

<br>

<a class="delete"
   href="delete_worker.php?id=<?= $worker["id"] ?>"
   onclick="return confirm('Delete this worker?')">

Delete

</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</body>
</html>
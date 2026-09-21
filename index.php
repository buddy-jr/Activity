<?php
include "config.php";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save"])) {
    $fullname = htmlspecialchars($_POST["fullname"]);
    $email = htmlspecialchars($_POST["email"]);

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM students WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color:red;'>Email already exists!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (fullname, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $fullname, $email);
        $stmt->execute();
    }
}

if (isset($_POST["update"])) {
    $id = $_POST["id"];
    $fullname = htmlspecialchars($_POST["fullname"]);
    $email = htmlspecialchars($_POST["email"]);

    $stmt = $conn->prepare("UPDATE students SET fullname=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $fullname, $email, $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

if (isset($_GET["search"])) {
    $search = "%" . $_GET["search"] . "%";
    $stmt = $conn->prepare("SELECT * FROM students WHERE fullname LIKE ?");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
}

$countResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$data = $countResult->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>PHP MySQL Example (Insert + Update + Search + Delete + Counter + Duplicate Check)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h2>Student Registration</h2>

<form method="POST">
<input type="text" name="fullname" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="submit" name="save" value="Save">
</form>

<h2>Edit Student Record</h2>
<form method="POST">
<input type="number" name="id" placeholder="Student ID" required>
<input type="text" name="fullname" placeholder="New Full Name" required>
<input type="email" name="email" placeholder="New Email" required>
<input type="submit" name="update" value="Update">
</form>


<h2>Search Students</h2>
<form method="GET">
<input type="text" name="search" placeholder="Search by name">
<input type="submit" value="Search">
</form>

<p>Total Students: <strong><?php echo $data['total']; ?></strong></p>

<h3>Student List</h3>
<table>
<tr>
<th>ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Date Created</th>
<th>Action</th>
</tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
<td><?php echo $row["id"]; ?></td>
<td><?php echo $row["fullname"]; ?></td>
<td><?php echo $row["email"]; ?></td>
<td><?php echo $row["created_at"]; ?></td>
<td>
<a href="index.php?delete=<?php echo $row['id']; ?>"
   onclick="return confirm('This action cannot be undone. Delete student?');"
   class="btn-delete">Delete</a>
</td>
</tr>
<?php } ?>
</table>
</div>
</body>
</html>

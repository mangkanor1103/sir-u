<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id']) || !isset($_GET['id'])) {
    header("Location: positions.php");
    exit();
}

$position_id = $_GET['id'];
$election_id = $_SESSION['election_id'];

// Fetch position details
$sql = "SELECT * FROM positions WHERE position_id = ? AND election_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $position_id, $election_id);
$stmt->execute();
$result = $stmt->get_result();
$position = $result->fetch_assoc();

if (!$position) {
    header("Location: positions.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $max_vote = $_POST['max_vote'];
    $priority = $_POST['priority'];

    $sql = "UPDATE positions SET description = ?, max_vote = ?, priority = ? WHERE position_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $description, $max_vote, $priority, $position_id);

    if ($stmt->execute()) {
        header("Location: positions.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Position</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Position</h2>
        <form method="POST" action="">
            <div class="form-group mb-3">
                <label>Description</label>
                <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($position['description']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label>Max Vote</label>
                <input type="number" class="form-control" name="max_vote" value="<?php echo $position['max_vote']; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label>Priority</label>
                <input type="number" class="form-control" name="priority" value="<?php echo $position['priority']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Position</button>
            <a href="positions.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

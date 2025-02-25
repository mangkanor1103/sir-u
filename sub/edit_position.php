<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Position</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&family=Electrolize&display=swap');
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Orbitron', sans-serif;
        }
        .container {
            max-width: 500px;
            margin-top: 50px;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }
        .form-control {
            background: #2c2c2c;
            border: 1px solid cyan;
            color: white;
        }
        .btn-custom {
            background: cyan;
            color: black;
            font-weight: bold;
            border: none;
        }
        .btn-custom:hover {
            background: #00cccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Edit Position</h2>
        <?php
        require 'conn.php';

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM positions WHERE position_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $position = $result->fetch_assoc();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['id'];
            $description = $_POST['description'];
            $max_vote = $_POST['max_vote'];
            $priority = $_POST['priority'];

            $sql = "UPDATE positions SET description = ?, max_vote = ?, priority = ? WHERE position_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siii", $description, $max_vote, $priority, $id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Position updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating position.</div>";
            }
        }
        ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $position['position_id']; ?>">
            <div class="form-group mb-3">
                <label>Description</label>
                <input type="text" class="form-control" name="description" value="<?php echo $position['description']; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label>Max Vote</label>
                <input type="number" class="form-control" name="max_vote" value="<?php echo $position['max_vote']; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label>Priority</label>
                <input type="number" class="form-control" name="priority" value="<?php echo $position['priority']; ?>" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Update Position</button>
            <a href="positions.php" class="btn btn-secondary">Cancel</a>

        </form>
    </div>
</body>
</html>
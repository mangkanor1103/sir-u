<?php
require 'conn.php';
session_start();

$election_id = $_SESSION['election_id'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM partylists WHERE partylist_id = ? AND election_id = ?");
    $stmt->bind_param("ii", $id, $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $partylist = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $stmt = $conn->prepare("UPDATE partylists SET name = ? WHERE partylist_id = ? AND election_id = ?");
    $stmt->bind_param("sii", $name, $id, $election_id);
    $stmt->execute();
    header("Location: partylist.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Partylist</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8fff0; /* Light greenish-white background */
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card w-50">
            <h2 class="text-center text-success">
                <i class="fas fa-edit"></i> Edit Partylist
            </h2>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $partylist['partylist_id']; ?>">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-users"></i> Partylist Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo $partylist['name']; ?>" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update</button>
                    <a href="partylist.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

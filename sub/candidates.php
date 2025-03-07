<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];

// Function to delete a candidate
function deleteCandidate($id) {
    global $conn;
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handle form submissions for candidates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['back'])) {
        unset($_SESSION['election_id']);
        header("Location: index.php");
        exit();
    }

    $action = $_POST['action'];

    if ($action == "create_candidate") {
        $position_id = $_POST['position_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $platform = $_POST['platform'];
        $partylist_id = $_POST['partylist_id'];

        // New candidate info fields
        $info_enabled = isset($_POST['info_enabled']) ? 1 : 0;
        $course = $info_enabled ? $_POST['course'] : '';
        $year_section = $info_enabled ? $_POST['year_section'] : '';
        $age = $info_enabled ? $_POST['age'] : null;
        $sex = $info_enabled ? $_POST['sex'] : '';
        $address = $info_enabled ? $_POST['address'] : '';

        // Handle photo upload
        $photo = uploadPhoto($_FILES['photo']);
        if ($photo) {
            if (createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id,
                              $course, $year_section, $age, $sex, $address, $info_enabled)) {
                $_SESSION['message'] = "Candidate created successfully!";
                header("Location: candidates.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error creating candidate!</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading photo!</div>";
        }
    } elseif ($action == "delete_candidate") {
        $id = $_POST['id'];
        if (deleteCandidate($id)) {
            echo "<div class='alert alert-success'>Candidate deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting candidate!</div>";
        }
    }
}

// Function to fetch positions
function getPositions($election_id) {
    global $conn;
    $sql = "SELECT * FROM positions WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch partylists
function getPartylists($election_id) {
    global $conn;
    $sql = "SELECT * FROM partylists WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch candidates with all details
function getCandidates($election_id) {
    global $conn;
    $sql = "
        SELECT c.*, p.description AS position_description, pl.name AS partylist_name
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
        WHERE c.election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to create a new candidate
function createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id,
                        $course, $year_section, $age, $sex, $address, $info_enabled) {
    global $conn;
    $sql = "INSERT INTO candidates (election_id, position_id, firstname, lastname, photo, platform, partylist_id,
                                  course, year_section, age, sex, address, info_enabled)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssississi", $election_id, $position_id, $firstname, $lastname, $photo, $platform,
                      $partylist_id, $course, $year_section, $age, $sex, $address, $info_enabled);
    return $stmt->execute();
}

// Function to handle file upload
function uploadPhoto($file) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is valid
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false;
    }

    // Check file size (20MB limit)
    if ($file["size"] > 20000000) {
        return false;
    }

    // Allow certain file formats
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedFormats)) {
        return false;
    }

    // Generate unique filename
    $newFilename = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFilename;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    }
    return false;
}

// Fetch data for display
$candidates = getCandidates($election_id);
$positions = getPositions($election_id);
$partylists = getPartylists($election_id);

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidates Management</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
        }
        .navbar-nav .nav-link {
            font-family: 'Orbitron', sans-serif;
            color: #e0e0e0;
            transition: color 0.3s ease, transform 0.3s ease;
            padding: 10px 15px;
        }
        .navbar-nav .nav-link:hover {
            color: #00ffcc;
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }
        .form-control:disabled {
            background-color: #e9ecef;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
      <!-- Navigation bar -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Election Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'home.php' ? 'active' : ''; ?>" href="home.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'partylist.php' ? 'active' : ''; ?>" href="partylist.php">
                            <i class="fas fa-users"></i> Partylist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">
                            <i class="fas fa-users"></i> Positions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">
                            <i class="fas fa-user-tie"></i> Candidates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">
                            <i class="fas fa-id-card"></i> Voters
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="">
                            <button type="submit" name="back" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt"></i> Back to Login
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="header text-center mb-4">
            <h1>Candidate Management</h1>
        </div>

        <!-- Create Candidate Form -->
        <div class="card p-4 mb-4 bg-light border-success">
            <h2 class="text-success">Add New Candidate</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_candidate">

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="position" class="text-success">Position</label>
                            <select class="form-control border-success" name="position_id" required>
                                <option value="">Select Position</option>
                                <?php while ($position = $positions->fetch_assoc()): ?>
                                    <option value="<?php echo $position['position_id']; ?>">
                                        <?php echo $position['description']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="firstname" class="text-success">First Name</label>
                            <input type="text" class="form-control border-success" name="firstname" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="lastname" class="text-success">Last Name</label>
                            <input type="text" class="form-control border-success" name="lastname" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="partylist" class="text-success">Partylist</label>
                            <select class="form-control border-success" name="partylist_id" required>
                                <option value="">Select Partylist</option>
                                <?php while ($partylist = $partylists->fetch_assoc()): ?>
                                    <option value="<?php echo $partylist['partylist_id']; ?>">
                                        <?php echo $partylist['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Information -->
<div class="col-md-6">
    <div class="form-group mb-3">
        <label for="course" class="text-success">Course</label>
        <select class="form-control border-success candidate-info" name="course">
            <option value="">Select Course</option>
            <?php
            // Fetch courses from the courses table
            $sql_courses = "SELECT * FROM courses";
            $result_courses = $conn->query($sql_courses);

            while($course = $result_courses->fetch_assoc()) {
                echo "<option value='" . $course['course'] . "'>" . $course['course'] . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group mb-3">
        <label for="year_section" class="text-success">Year and Section</label>
        <select class="form-control border-success candidate-info" name="year_section">
            <option value="">Select Year and Section</option>
            <?php
            // Fetch year_section from the courses table
            $sql_sections = "SELECT DISTINCT year_section FROM courses ORDER BY year_section";
            $result_sections = $conn->query($sql_sections);

            while($section = $result_sections->fetch_assoc()) {
                echo "<option value='" . $section['year_section'] . "'>" . $section['year_section'] . "</option>";
            }
            ?>
        </select>
    </div>
</div>


                        <div class="form-group mb-3">
                            <label for="age" class="text-success">Age</label>
                            <input type="number" class="form-control border-success candidate-info" name="age" min="16" max="30">
                        </div>

                        <div class="form-group mb-3">
                            <label for="sex" class="text-success">Sex</label>
                            <select class="form-control border-success candidate-info" name="sex">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="text-success">Address</label>
                            <textarea class="form-control border-success candidate-info" name="address" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Photo and Platform -->
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="photo" class="text-success">Photo</label>
                            <input type="file" class="form-control border-success" name="photo" accept="image/*" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="platform" class="text-success">Platform</label>
                            <textarea class="form-control border-success" name="platform" rows="3" required></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="enableInfo" name="info_enabled" checked>
                            <label class="form-check-label text-success" for="enableInfo">
                                Enable Additional Candidate Information
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Create Candidate</button>
                </div>
            </form>
        </div>

        <!-- Candidates List -->
        <div class="card p-4 bg-light border-success">
            <h2 class="text-success">Existing Candidates</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Position</th>
                            <th>Name</th>
                            <th>Partylist</th>
                            <th>Course</th>
                            <th>Year-Section</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($candidate = $candidates->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $candidate['position_description']; ?></td>
                                <td><?php echo $candidate['firstname'] . ' ' . $candidate['lastname']; ?></td>
                                <td><?php echo $candidate['partylist_name'] ?? 'None'; ?></td>
                                <td><?php echo $candidate['course'] ?? 'N/A'; ?></td>
                                <td><?php echo $candidate['year_section'] ?? 'N/A'; ?></td>
                                <td><?php echo $candidate['age'] ?? 'N/A'; ?></td>
                                <td><?php echo $candidate['sex'] ?? 'N/A'; ?></td>
                                <td>
                                    <img src="<?php echo $candidate['photo']; ?>" alt="Candidate Photo"
                                         class="rounded-circle border border-success"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit_candidate.php?id=<?php echo $candidate['id']; ?>"
                                           class="btn btn-warning btn-sm">Edit</a>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $candidate['id']; ?>">
                                            <input type="hidden" name="action" value="delete_candidate">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this candidate?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('enableInfo').addEventListener('change', function() {
            const infoFields = document.querySelectorAll('.candidate-info');
            infoFields.forEach(field => {
                field.disabled = !this.checked;
                if (this.checked) {
                    field.required = true;
                } else {
                    field.required = false;
                    field.value = '';
                }
            });
        });
    </script>
</body>
</html>

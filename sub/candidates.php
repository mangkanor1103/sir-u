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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">

    <!-- Navigation Bar -->
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="home.php" class="text-2xl font-bold">Election Dashboard</a>
            <ul class="flex space-x-6">
                <li><a href="home.php" class="hover:text-green-300 <?php echo $current_page == 'home.php' ? 'font-bold underline' : ''; ?>">Home</a></li>
                <li><a href="partylist.php" class="hover:text-green-300 <?php echo $current_page == 'partylist.php' ? 'font-bold underline' : ''; ?>">Partylist</a></li>
                <li><a href="positions.php" class="hover:text-green-300 <?php echo $current_page == 'positions.php' ? 'font-bold underline' : ''; ?>">Positions</a></li>
                <li><a href="candidates.php" class="hover:text-green-300 <?php echo $current_page == 'candidates.php' ? 'font-bold underline' : ''; ?>">Candidates</a></li>
                <li><a href="voters.php" class="hover:text-green-300 <?php echo $current_page == 'voters.php' ? 'font-bold underline' : ''; ?>">Voters</a></li>
                <li><a href="start.php" class="hover:text-green-300 <?php echo $current_page == 'start.php' ? 'font-bold underline' : ''; ?>">Start</a></li>
                <li>
                    <a href="#" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" 
                       onclick="openLogoutModal(event);">
                       Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Confirm Logout</h2>
            <p class="text-gray-700 mb-6">Are you sure you want to logout?</p>
            <div class="flex justify-end space-x-4">
                <button onclick="closeLogoutModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <a href="../index.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center mb-6">Manage Candidates</h2>
        <p class="text-center text-lg mb-8">Add, edit, or delete candidates for the current election.</p>

        <!-- Create Candidate Form -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <h3 class="text-2xl font-bold mb-4">Add New Candidate</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_candidate">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Position -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700">Position</label>
                        <select id="position_id" name="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" required>
                            <option value="">Select Position</option>
                            <?php while ($position = $positions->fetch_assoc()): ?>
                                <option value="<?php echo $position['position_id']; ?>"><?php echo $position['description']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Partylist -->
                    <div>
                        <label for="partylist_id" class="block text-sm font-medium text-gray-700">Partylist</label>
                        <select id="partylist_id" name="partylist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" required>
                            <option value="">Select Partylist</option>
                            <?php while ($partylist = $partylists->fetch_assoc()): ?>
                                <option value="<?php echo $partylist['partylist_id']; ?>"><?php echo $partylist['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- First Name -->
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="firstname" name="firstname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" required>
                    </div>
                    <!-- Last Name -->
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="lastname" name="lastname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" required>
                    </div>
                </div>
                <!-- Additional Information -->
                <div class="mt-6">
                    <label for="info_enabled" class="flex items-center space-x-2">
                        <input type="checkbox" id="info_enabled" name="info_enabled" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">Include Additional Candidate Information</span>
                    </label>
                </div>
                <div id="additional-info" class="mt-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                            <input type="text" id="course" name="course" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50">
                        </div>
                        <div>
                            <label for="year_section" class="block text-sm font-medium text-gray-700">Year & Section</label>
                            <input type="text" id="year_section" name="year_section" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50">
                        </div>
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                            <input type="number" id="age" name="age" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50">
                        </div>
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                            <select id="sex" name="sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50"></textarea>
                        </div>
                    </div>
                </div>
                <!-- Photo -->
                <div class="mt-6">
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" id="photo" name="photo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" accept="image/*" required>
                </div>
                <!-- Platform -->
                <div class="mt-6">
                    <label for="platform" class="block text-sm font-medium text-gray-700">Platform</label>
                    <textarea id="platform" name="platform" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" required></textarea>
                </div>
                <div class="mt-6 text-center">
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">Create Candidate</button>
                </div>
            </form>
        </div>

        <!-- Candidates List -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-2xl font-bold mb-4">Candidates List</h3>
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-green-700 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Position</th>
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Partylist</th>
                        <th class="border border-gray-300 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($candidate = $candidates->fetch_assoc()): ?>
                    <tr class="hover:bg-green-100">
                        <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['position_description']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['firstname'] . ' ' . $candidate['lastname']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['partylist_name'] ?? 'None'; ?></td>
                        <td class="border border-gray-300 px-4 py-2">
                            <a href="edit_candidate.php?id=<?php echo $candidate['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Edit</a>
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="id" value="<?php echo $candidate['id']; ?>">
                                <input type="hidden" name="action" value="delete_candidate">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Are you sure you want to delete this candidate?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Show or hide additional candidate information based on the checkbox
        document.getElementById('info_enabled').addEventListener('change', function () {
            const additionalInfo = document.getElementById('additional-info');
            if (this.checked) {
                additionalInfo.classList.remove('hidden');
            } else {
                additionalInfo.classList.add('hidden');
            }
        });

        // Function to open the logout confirmation modal
        function openLogoutModal(event) {
            event.preventDefault(); // Prevent the default link behavior
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Function to close the logout confirmation modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>

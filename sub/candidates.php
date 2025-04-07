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
    $action = $_POST['action'];

    if ($action == "create_candidate") {
        $position_id = $_POST['position_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $platform = $_POST['platform'];
        $partylist_id = $_POST['partylist_id'];

        // Handle photo upload
        $photo = uploadPhoto($_FILES['photo']);
        if ($photo) {
            $createResult = createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id);
            if ($createResult === true) {
                $_SESSION['message'] = "Candidate created successfully!";
                header("Location: candidates.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>$createResult</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading photo!</div>";
        }
    } elseif ($action == "delete_candidate") {
        $id = $_POST['id'];
        if (deleteCandidate($id)) {
            $_SESSION['message'] = "Candidate deleted successfully!";
            header("Location: candidates.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error deleting candidate!</div>";
        }
    } elseif ($action == "update_candidate") {
        $id = $_POST['id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $position_id = $_POST['position_id'];
        $partylist_id = $_POST['partylist_id'];
        $platform = $_POST['platform'];
    
        // Handle photo upload
        $photo = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $photo = uploadPhoto($_FILES['photo']);
        }
    
        if (updateCandidate($id, $firstname, $lastname, $position_id, $partylist_id, $platform, $photo)) {
            $_SESSION['message'] = "Candidate updated successfully!";
            header("Location: candidates.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error updating candidate!</div>";
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
function createCandidate($election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id) {
    global $conn;

    // Check if the number of candidates in the partylist for the position exceeds the max_vote
    $maxVoteQuery = "
        SELECT p.max_vote, COUNT(c.id) AS current_candidates
        FROM positions p
        LEFT JOIN candidates c ON c.position_id = p.position_id AND c.partylist_id = ?
        WHERE p.position_id = ?
        GROUP BY p.max_vote";
    $stmt = $conn->prepare($maxVoteQuery);
    $stmt->bind_param("ii", $partylist_id, $position_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['current_candidates'] >= $result['max_vote']) {
        // Return false if the max_vote limit is reached
        return "Max vote limit reached for this partylist and position.";
    }

    // Proceed to insert the candidate if the limit is not reached
    $sql = "INSERT INTO candidates (election_id, position_id, firstname, lastname, photo, platform, partylist_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssi", $election_id, $position_id, $firstname, $lastname, $photo, $platform, $partylist_id);
    return $stmt->execute() ? true : "Error adding candidate.";
}

// Function to update a candidate
function updateCandidate($id, $firstname, $lastname, $position_id, $partylist_id, $platform, $photo = null) {
    global $conn;

    if ($photo) {
        $sql = "UPDATE candidates 
                SET firstname = ?, lastname = ?, position_id = ?, partylist_id = ?, platform = ?, photo = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssi", $firstname, $lastname, $position_id, $partylist_id, $platform, $photo, $id);
    } else {
        $sql = "UPDATE candidates 
                SET firstname = ?, lastname = ?, position_id = ?, partylist_id = ?, platform = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissi", $firstname, $lastname, $position_id, $partylist_id, $platform, $id);
    }

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


    <!-- Navigation bar -->
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo and Title -->
            <div class="flex items-center space-x-3">
                <img src="../pics/logo.png" alt="Logo" class="h-10 w-10">
                <a href="home.php" class="text-2xl font-bold">Election Dashboard</a>
            </div>

            <!-- Hamburger Menu for Mobile -->
            <button id="menu-toggle" class="block md:hidden focus:outline-none">
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Navigation Links -->
            <ul id="menu" class="hidden md:flex space-x-6">
                <li><a href="home.php" class="hover:text-green-300 <?php echo $current_page == 'home.php' ? 'font-bold underline' : ''; ?>">Home</a></li>
                <li><a href="partylist.php" class="hover:text-green-300 <?php echo $current_page == 'partylist.php' ? 'font-bold underline' : ''; ?>">Partylist</a></li>
                <li><a href="positions.php" class="hover:text-green-300 <?php echo $current_page == 'positions.php' ? 'font-bold underline' : ''; ?>">Positions</a></li>
                <li><a href="candidates.php" class="hover:text-green-300 <?php echo $current_page == 'candidates.php' ? 'font-bold underline' : ''; ?>">Candidates</a></li>
                <li><a href="voters.php" class="hover:text-green-300 <?php echo $current_page == 'voters.php' ? 'font-bold underline' : ''; ?>">Voters</a></li>
                <li><a href="start.php" class="hover:text-green-300 <?php echo $current_page == 'start.php' ? 'font-bold underline' : ''; ?>">Start</a></li>
                <li>
                    <a href="#" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" 
                       onclick="confirmLogout(event);">
                       Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Script to toggle mobile menu -->
<script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
        const menu = document.getElementById('menu');
        menu.classList.toggle('hidden');
    });
</script>
    <!-- Main Content -->
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-center mb-6">Manage Candidates</h2>
        <p class="text-center text-lg mb-8">Add, edit, or delete candidates for the current election.</p>

                <!-- Navigation Buttons -->
<div class="flex justify-between items-center mb-6">
    <a href="positions.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">
        &larr; Back to Positions
    </a>
    <button onclick="openAddModal()" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg mx-auto">
        + Add Candidates
    </button>
    <a href="voters.php" 
       class="px-6 py-3 rounded-lg text-white <?php echo ($candidates->num_rows > 0) ? 'bg-green-700 hover:bg-green-800' : 'bg-red-500 cursor-not-allowed'; ?>" 
       <?php echo ($candidates->num_rows > 0) ? '' : 'onclick="return false;"'; ?>>
        Next Step &rarr;
    </a>
</div>

<!-- Red Message -->
<?php if ($candidates->num_rows == 0): ?>
    <p class="text-red-500 mt-4 text-center">You must add at least one candidate to proceed to the next step.</p>
<?php endif; ?>
<!-- Candidates List -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-2xl font-bold mb-4">Candidates List</h3>

    <!-- Scrollable Table Container -->
    <div class="overflow-x-auto">
        <table class="table-auto w-full min-w-[900px] border-collapse border border-gray-300">
            <thead class="bg-green-700 text-white">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Position</th>
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Partylist</th>
                    <th class="border border-gray-300 px-4 py-2">Platform</th>
                    <th class="border border-gray-300 px-4 py-2">Photo</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($candidate = $candidates->fetch_assoc()): ?>
                <tr class="hover:bg-green-100">
                    <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['position_description']; ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['firstname'] . ' ' . $candidate['lastname']; ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo $candidate['partylist_name'] ?? 'None'; ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($candidate['platform']); ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <?php if (!empty($candidate['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" alt="Candidate Photo" class="h-16 w-16 object-cover rounded">
                        <?php else: ?>
                            <span>No Photo</span>
                        <?php endif; ?>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="bg-green-500 hover:bg-blue-600 text-white px-4 py-2 rounded" 
                            onclick="openEditModal(
                                <?php echo $candidate['id']; ?>, 
                                '<?php echo htmlspecialchars($candidate['firstname'], ENT_QUOTES); ?>', 
                                '<?php echo htmlspecialchars($candidate['lastname'], ENT_QUOTES); ?>', 
                                <?php echo $candidate['position_id']; ?>, 
                                <?php echo $candidate['partylist_id'] ?? 'null'; ?>, 
                                '<?php echo htmlspecialchars($candidate['platform'], ENT_QUOTES); ?>'
                            )">
                            Edit
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" 
                            onclick="openDeleteModal(<?php echo $candidate['id']; ?>)">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


    <!-- Add Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Add Candidate</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_candidate">
                <div class="mb-4">
                    <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="firstname" name="firstname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="lastname" name="lastname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                </div>
                <div class="mb-4">
                    <label for="position_id" class="block text-sm font-medium text-gray-700">Position</label>
                    <select id="position_id" name="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                        <option value="">Select Position</option>
                        <?php while ($position = $positions->fetch_assoc()): ?>
                            <option value="<?php echo $position['position_id']; ?>"><?php echo $position['description']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="partylist_id" class="block text-sm font-medium text-gray-700">Partylist</label>
                    <select id="partylist_id" name="partylist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Select Partylist</option>
                        <?php while ($partylist = $partylists->fetch_assoc()): ?>
                            <option value="<?php echo $partylist['partylist_id']; ?>"><?php echo $partylist['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="platform" class="block text-sm font-medium text-gray-700">Platform</label>
                    <textarea id="platform" name="platform" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                    <input type="file" id="photo" name="photo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" accept="image/*" required>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Add</button>
                </div>
            </form>
        </div>
    </div>
<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h2 class="text-2xl font-bold text-green-700 mb-4">Edit Candidate</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_candidate">
            <input type="hidden" id="editCandidateId" name="id">
            <div class="mb-4">
                <label for="editFirstname" class="block text-sm font-medium text-gray-700">First Name</label>
                <input type="text" id="editFirstname" name="firstname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            </div>
            <div class="mb-4">
                <label for="editLastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input type="text" id="editLastname" name="lastname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            </div>
            <div class="mb-4">
                <label for="editPositionId" class="block text-sm font-medium text-gray-700">Position</label>
                <select id="editPositionId" name="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                    <option value="">Select Position</option>
                    <?php foreach ($positions as $position): ?>
                        <option value="<?php echo $position['position_id']; ?>"><?php echo $position['description']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="editPartylistId" class="block text-sm font-medium text-gray-700">Partylist</label>
                <select id="editPartylistId" name="partylist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Select Partylist</option>
                    <?php foreach ($partylists as $partylist): ?>
                        <option value="<?php echo $partylist['partylist_id']; ?>"><?php echo $partylist['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="editPlatform" class="block text-sm font-medium text-gray-700">Platform</label>
                <textarea id="editPlatform" name="platform" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="editPhoto" class="block text-sm font-medium text-gray-700">Photo</label>
                <input type="file" id="editPhoto" name="photo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" accept="image/*">
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-red-700 mb-4">Confirm Deletion</h2>
            <p class="text-gray-700 mb-6">Are you sure you want to delete this candidate?</p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete_candidate">
                <input type="hidden" id="deleteCandidateId" name="id">
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Function to open the logout confirmation modal
        function openLogoutModal(event) {
            event.preventDefault(); // Prevent the default link behavior
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Function to close the logout confirmation modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        // Function to open the add candidate modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        // Function to close the add candidate modal
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(id, firstname, lastname, positionId, partylistId, platform) {
    document.getElementById('editCandidateId').value = id;
    document.getElementById('editFirstname').value = firstname;
    document.getElementById('editLastname').value = lastname;
    document.getElementById('editPositionId').value = positionId;
    document.getElementById('editPartylistId').value = partylistId;
    document.getElementById('editPlatform').value = platform;
    document.getElementById('editModal').classList.remove('hidden');
}

        // Function to close the edit candidate modal
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Function to open the delete confirmation modal
        function openDeleteModal(id) {
            document.getElementById('deleteCandidateId').value = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Function to close the delete confirmation modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</body>
</html>

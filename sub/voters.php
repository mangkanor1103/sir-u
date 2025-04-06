<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['back'])) {
    unset($_SESSION['election_id']);
    header("Location: index.php");
    exit();
}
$election_id = $_SESSION['election_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Function to fetch voters with pagination
function getVoters($election_id, $limit = null, $offset = null) {
    global $conn;

    if ($limit !== null && $offset !== null) {
        $sql = "SELECT * FROM voters WHERE election_id = ? ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $election_id, $limit, $offset);
    } else {
        $sql = "SELECT * FROM voters WHERE election_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $election_id);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Function to count total voters
function countVoters($election_id) {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM voters WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to generate voter codes with prefix
function generateVoterCodes($election_id, $count, $prefix = '', $length = 6) {
    global $conn;
    $codes = array();

    // Get the election name for default prefix if none provided
    if (empty($prefix)) {
        $sql = "SELECT name FROM elections WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Use first 3 characters of election name as default prefix
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $row['name']), 0, 3));
        }
    }

    for ($i = 0; $i < $count; $i++) {
        // Generate a unique code with prefix
        $unique = false;
        while (!$unique) {
            $random_part = generateRandomString($length);
            $code = $prefix . $random_part;

            // Check if code already exists
            $sql = "SELECT COUNT(*) as count FROM voters WHERE voters_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] == 0) {
                $unique = true;
            }
        }

        // Insert the unique code
        $sql = "INSERT INTO voters (election_id, voters_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $election_id, $code);
        $stmt->execute();
        $codes[] = $code;
    }
    return $codes;
}

// Function to generate a random string
function generateRandomString($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "clear_voter_codes") {
    // Now, delete votes
    $sql = "DELETE FROM votes WHERE voters_id IN (SELECT id FROM voters WHERE election_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();

    // Finally, delete voters
    $sql = "DELETE FROM voters WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submission for generating voter codes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "generate_voter_codes") {
    $count = $_POST['count'];
    $prefix = isset($_POST['prefix']) ? strtoupper($_POST['prefix']) : '';
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 6;

    // Validate length (minimum 4, maximum 10)
    $length = max(4, min(10, $length));

    generateVoterCodes($election_id, $count, $prefix, $length);

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?generated=true");
    exit();
}

// Pagination logic
$limit = 10; // Number of voters per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit;

// Get total number of voters for pagination
$totalVoters = countVoters($election_id);
$totalPages = ceil($totalVoters / $limit);

// Get voters for current page
$voters = getVoters($election_id, $limit, $offset);

// Get election name
$election_name = "";
$sql = "SELECT name FROM elections WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $election_name = $row['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">

    <!-- Navigation Bar -->
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
        <h2 class="text-3xl font-bold text-center mb-6">Manage Voters</h2>
        <p class="text-center text-lg mb-8">Generate, view, or delete voter codes for the current election.</p>
                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center mb-6">
            <a href="candidates.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">
                &larr; Back to Candidates
            </a>
            <button onclick="openGenerateModal()" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg flex items-center space-x-2">
                <span>Generate Codes</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
            <a href="start.php" class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg">
                Next Step &rarr;
            </a>
        </div>

        <!-- Voter Codes List -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-2xl font-bold mb-4">Generated Voter Codes</h3>
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-green-700 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Voter Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($voters->num_rows > 0): ?>
                        <?php while ($row = $voters->fetch_assoc()): ?>
                        <tr class="hover:bg-green-100">
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['voters_id']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="1" class="text-center text-gray-500">No voter codes generated yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="flex justify-center mt-4">
                <?php if ($totalPages > 1): ?>
                    <nav class="inline-flex space-x-2">
                        <!-- Previous Page Link -->
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Previous</a>
                        <?php else: ?>
                            <span class="bg-gray-200 text-gray-500 px-4 py-2 rounded cursor-not-allowed">Previous</span>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="bg-green-700 text-white px-4 py-2 rounded"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Next Page Link -->
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Next</a>
                        <?php else: ?>
                            <span class="bg-gray-200 text-gray-500 px-4 py-2 rounded cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Generate Voter Codes Modal -->
    <div id="generateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Generate Voter Codes</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate_voter_codes">
                <div class="mb-4">
                    <label for="prefix" class="block text-sm font-medium text-gray-700">Prefix (Optional)</label>
                    <input type="text" id="prefix" name="prefix" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" maxlength="5" placeholder="e.g., VOTE">
                    <small class="text-gray-500">Leave blank to use election name abbreviation</small>
                </div>
                <div class="mb-4">
                    <label for="length" class="block text-sm font-medium text-gray-700">Random Code Length</label>
                    <select id="length" name="length" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50">
                        <option value="4">4 characters</option>
                        <option value="6" selected>6 characters</option>
                        <option value="8">8 characters</option>
                        <option value="10">10 characters</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="count" class="block text-sm font-medium text-gray-700">Number of Codes to Generate</label>
                    <input type="number" id="count" name="count" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-green-50" min="1" max="1000" value="10" required>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeGenerateModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle the mobile menu
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Open Generate Modal
        function openGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
        }

        // Close Generate Modal
        function closeGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }

        // Open Logout Modal
        function openLogoutModal(event) {
            event.preventDefault();
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        // Close Logout Modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }
    </script>
</body>
</html>

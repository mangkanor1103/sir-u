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
    // First, delete dependent feedback records
    $sql = "DELETE FROM feedback WHERE voter_id IN (SELECT id FROM voters WHERE election_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();

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
    <title>Generate Voter Codes</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
        }
        .form-control {
            background: #f0f0f0;
            border: 1px solid #28a745;
            color: #333;
        }
        .btn-custom {
            background: #28a745;
            color: white;
            font-weight: bold;
            border: none;
            width: 100%;
        }
        .btn-custom:hover {
            background: #218838;
        }
        .table thead {
            background: #28a745;
            color: white;
        }
        .table tbody tr:hover {
            background: #f1f1f1;
        }
        .navbar-nav .nav-link {
            font-family: 'Orbitron', sans-serif;
            color: #e0e0e0;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
            position: relative;
            padding: 10px 15px;
        }

        /* Hover Effect */
        .navbar-nav .nav-link:hover {
            color: #00ffcc;
            transform: translateY(-2px); /* Slight lift effect */
        }

        /* Active Page Indicator */
        .navbar-nav .nav-link.active {
            color: #00ffcc;
            font-weight: bold;
            text-shadow: 0px 0px 8px rgba(0, 255, 204, 0.8);
        }

        /* Underline Animation */
        .navbar-nav .nav-link::after {
            content: "";
            display: block;
            width: 0;
            height: 2px;
            background: #00ffcc;
            transition: width 0.3s ease;
            margin-top: 3px;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        /* Icons Styling */
        .navbar-nav .nav-link i {
            margin-right: 8px;
        }

        /* Pagination styling */
        .pagination .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
        }
        .pagination .page-link {
            color: #28a745;
        }
        .pagination .page-link:hover {
            color: #218838;
        }

        /* Code format styling */
        .code-format {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Alert styling */
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        /* Form card styling */
        .form-card {
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }

        .form-card h4 {
            color: #28a745;
            margin-bottom: 15px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }

        /* Preview styling */
        .code-preview {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin-top: 10px;
            text-align: center;
            font-size: 18px;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Election Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Home -->
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
                    <!-- Positions -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'positions.php' ? 'active' : ''; ?>" href="positions.php">
                            <i class="fas fa-user-tie"></i> Positions
                        </a>
                    </li>
                    <!-- Candidates -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'candidates.php' ? 'active' : ''; ?>" href="candidates.php">
                            <i class="fas fa-users"></i> Candidates
                        </a>
                    </li>
                    <!-- Voters -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'voters.php' ? 'active' : ''; ?>" href="voters.php">
                            <i class="fas fa-id-card"></i> Voters
                        </a>
                    </li>
                    <!-- Back to Login -->
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

    <div class="container">
        <div class="header text-center mb-4">
            <h1>Generate Voter Codes</h1>
            <h5 class="text-muted">Election: <?php echo htmlspecialchars($election_name); ?></h5>
            <a href="home.php" class="btn btn-success"><i class="fas fa-home"></i> Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['generated']) && $_GET['generated'] == 'true'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-check-circle"></i> Success!</strong> Voter codes have been generated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="form-card">
            <h4><i class="fas fa-key"></i> Generate Voter Codes</h4>
            <form method="POST" action="" id="generateForm">
                <input type="hidden" name="action" value="generate_voter_codes">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prefix" class="form-label">Prefix (Optional)</label>
                        <input type="text" class="form-control" id="prefix" name="prefix" placeholder="e.g., VOTE" maxlength="5" oninput="updatePreview()">
                        <small class="text-muted">Leave blank to use election name abbreviation</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="length" class="form-label">Random Code Length</label>
                        <select class="form-control" id="length" name="length" onchange="updatePreview()">
                            <option value="4">4 characters</option>
                            <option value="6" selected>6 characters</option>
                            <option value="8">8 characters</option>
                            <option value="10">10 characters</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="count" class="form-label">Number of Codes to Generate</label>
                    <input type="number" class="form-control" id="count" name="count" min="1" max="1000" value="10" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Code Format Preview:</label>
                    <div class="code-preview" id="codePreview">PREFIX123456</div>
                </div>

                <button type="submit" class="btn btn-custom"><i class="fas fa-cog"></i> Generate Codes</button>
            </form>
        </div>

        <div class="d-flex gap-2 mt-3 mb-4">
            <form method="POST" action="" onsubmit="return confirmDelete()">
                <input type="hidden" name="action" value="clear_voter_codes">
                <!-- Delete Button -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Clear All Codes
                </button>
            </form>

            <button onclick="printTable()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Codes
            </button>

            <button onclick="exportToCSV()" class="btn btn-success">
                <i class="fas fa-file-csv"></i> Export to CSV
            </button>
        </div>

        <h2><i class="fas fa-list"></i> Generated Voter Codes</h2>
        <div class="table-responsive">
            <table class="table table-bordered" id="voterCodesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Voter Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($voters->num_rows > 0): ?>
                        <?php while ($row = $voters->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td class="code-format"><?php echo $row['voters_id']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No voter codes generated yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <?php if ($totalPages > 0): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=1" aria-label="First">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                // Show limited page numbers with current page in the middle
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                // Always show first page
                if ($startPage > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($startPage > 2) {
                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                    }
                }

                // Display page numbers
                for ($i = $startPage; $i <= $endPage; $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                            <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                          </li>';
                }

                // Always show last page
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) {
                        echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $totalPages; ?>" aria-label="Last">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <div class="text-center mt-3 mb-4">
            <p>Showing <?php echo min(($page-1)*$limit+1, $totalVoters); ?> to <?php echo min($page*$limit, $totalVoters); ?> of <?php echo $totalVoters; ?> entries</p>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete all voter codes?</p>
                    <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                    <p>All votes and feedback associated with these codes will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="clear_voter_codes">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete All</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to update the code preview
        function updatePreview() {
            let prefix = document.getElementById('prefix').value.toUpperCase();
            let length = document.getElementById('length').value;
            let randomPart = generateRandomPreview(length);

            document.getElementById('codePreview').innerText = prefix + randomPart;
        }

        // Function to generate a random preview string
        function generateRandomPreview(length) {
            const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result;
        }

        // Function to print the table
        function printTable() {
            let printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Print Voter Codes</title>');
            printWindow.document.write('<style>');
            printWindow.document.write(`
                body { font-family: Arial, sans-serif; }
                h2 { color: #28a745; text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th { background-color: #28a745; color: white; padding: 10px; text-align: left; }
                td { padding: 8px; border: 1px solid #ddd; }
                .code-format { font-family: 'Courier New', monospace; font-weight: bold; letter-spacing: 1px; }
                .election-info { text-align: center; margin-bottom: 10px; color: #666; }
            `);
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<h2>Generated Voter Codes</h2>');
            printWindow.document.write('<div class="election-info">Election: <?php echo htmlspecialchars($election_name); ?></div>');

            // Create a new table for printing
            printWindow.document.write('<table>');
            printWindow.document.write('<thead><tr><th>ID</th><th>Voter Code</th></tr></thead><tbody>');

            // Get all rows from the current table
            const rows = document.querySelectorAll('#voterCodesTable tbody tr');
            rows.forEach(row => {
                printWindow.document.write('<tr>');
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    if (cell.classList.contains('code-format')) {
                        printWindow.document.write(`<td class="code-format">${cell.innerText}</td>`);
                    } else {
                        printWindow.document.write(`<td>${cell.innerText}</td>`);
                    }
                });
                printWindow.document.write('</tr>');
            });

            printWindow.document.write('</tbody></table>');
            printWindow.document.write('<div style="text-align: center; margin-top: 20px; font-size: 12px;">Generated on: ' + new Date().toLocaleString() + '</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Function to export table to CSV
        function exportToCSV() {
            const table = document.getElementById('voterCodesTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');

                for (let j = 0; j < cols.length; j++) {
                    // Escape double quotes with double quotes
                    let data = cols[j].innerText.replace(/"/g, '""');
                    // Add quotes around the field
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }

            // Download CSV file
            downloadCSV(csv.join('\n'), 'voter_codes_<?php echo date("Y-m-d"); ?>.csv');
        }

        function downloadCSV(csv, filename) {
            const csvFile = new Blob([csv], {type: 'text/csv'});
            const downloadLink = document.createElement('a');

            // Create a download link
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';

            // Add the link to the DOM
            document.body.appendChild(downloadLink);

            // Click the link
            downloadLink.click();

            // Remove the link
            document.body.removeChild(downloadLink);
        }

        // Initialize the preview when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>
</body>
</html>

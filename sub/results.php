<?php
session_start();
require 'conn.php';

// Redirect if no election is selected
if (!isset($_SESSION['election_id'])) {
    header("Location: index.php");
    exit();
}

$election_id = $_SESSION['election_id'];

// Fetch election details
$election_query = "SELECT name, status, end_time FROM elections WHERE id = ?";
$stmt = $conn->prepare($election_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$election = $stmt->get_result()->fetch_assoc();
$election_name = $election['name'] ?? 'Election not found';
$election_status = $election['status'] ?? 0;

// Fetch total voters count
$total_voters_query = "SELECT COUNT(*) AS total_voters FROM voters WHERE election_id = ?";
$stmt = $conn->prepare($total_voters_query);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$total_voters_result = $stmt->get_result()->fetch_assoc();
$total_voters = $total_voters_result['total_voters'] ?? 0;

// Fetch votes by position
function getVotesByPosition($election_id) {
    global $conn;
    $sql = "
        SELECT p.description AS position,
               CONCAT(c.firstname, ' ', c.lastname) AS candidate,
               c.photo,
               c.platform,
               pl.name AS partylists,
               COALESCE(COUNT(v.candidate_id), 0) AS total_votes,
               p.position_id,
               p.max_vote,
               (SELECT COUNT(*) FROM candidates WHERE position_id = p.position_id AND election_id = ?) AS candidate_count
        FROM candidates c
        JOIN positions p ON c.position_id = p.position_id
        LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
        LEFT JOIN votes v ON c.id = v.candidate_id AND v.election_id = ?
        WHERE c.election_id = ?
        GROUP BY p.position_id, c.id
        ORDER BY p.position_id, total_votes DESC;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $election_id, $election_id, $election_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Get voter turnout
function getTotalVotesCast($election_id) {
    global $conn;
    $sql = "SELECT COUNT(DISTINCT voters_id) AS total_votes FROM votes WHERE election_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_votes'] ?? 0;
}

$results = getVotesByPosition($election_id);
$positionsData = [];

while ($row = $results->fetch_assoc()) {
    $position_id = $row['position_id'];
    $position = $row['position'];
    
    if (!isset($positionsData[$position])) {
        $positionsData[$position] = [
            'candidates' => [],
            'max_vote' => $row['max_vote'],
            'winner_count' => 0
        ];
    }
    
    // Store candidate data - add all fields including partylists, photo, platform
    $positionsData[$position]['candidates'][] = [
        'candidate' => $row['candidate'],
        'partylists' => $row['partylists'],
        'photo' => $row['photo'],
        'platform' => $row['platform'],
        'total_votes' => $row['total_votes'],
        'is_winner' => false,
        'is_tie' => false
    ];
}

// Determine winners for each position
foreach ($positionsData as $position => &$data) {
    // Sort candidates by votes (descending)
    usort($data['candidates'], function($a, $b) {
        return $b['total_votes'] - $a['total_votes'];
    });
    
    $max_vote = $data['max_vote'];
    $candidates = $data['candidates'];
    
    // Find the highest vote count
    $max_votes = count($candidates) > 0 ? $candidates[0]['total_votes'] : 0;
    
    // Mark winners and ties
    foreach ($candidates as &$candidate) {
        $candidate['is_winner'] = false;
        $candidate['is_tie'] = false;
        
        if ($candidate['total_votes'] > 0 && $candidate['total_votes'] == $max_votes) {
            // Check if it's a tie
            $tied_candidates = array_filter($candidates, function($c) use ($max_votes) {
                return $c['total_votes'] == $max_votes;
            });
            
            if (count($tied_candidates) > 1) {
                $candidate['is_tie'] = true;
            } elseif ($data['winner_count'] < $max_vote) {
                $candidate['is_winner'] = true;
                $data['winner_count']++;
            }
        }
    }
    
    $data['candidates'] = $candidates;
}

// Get voter turnout data
$votes_cast = getTotalVotesCast($election_id);
$turnout_percentage = $total_voters > 0 ? round(($votes_cast / $total_voters) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($election_name); ?> - Election Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .winner-tag {
            background-color: #4CAF50;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .tie-tag {
            background-color: #FFC107;
            color: #212121;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                padding: 0;
                margin: 0;
                font-family: 'Poppins', sans-serif;
            }
            
            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            th, td {
                padding: 8px;
                border: 1px solid #ddd;
                font-size: 12px;
            }
            
            th {
                background-color: #f2f2f2 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            h1, h2 {
                page-break-after: avoid;
            }
            
            .position-header {
                background-color: #e8f5e9 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .winner-tag {
                background-color: #4CAF50 !important;
                color: white !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .tie-tag {
                background-color: #FFC107 !important;
                color: #212121 !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .certification {
                page-break-before: avoid;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 10px;
                color: #666;
                padding: 10px 0;
                border-top: 1px solid #ddd;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation buttons (only visible on screen) -->
    <div class="bg-green-700 text-white py-4 px-6 flex items-center justify-between no-print">
        <div class="flex items-center">
            <img src="../pics/logo.png" alt="Logo" class="h-10 w-10 mr-3">
            <h1 class="text-xl font-bold">Election Results</h1>
        </div>
        <div class="flex space-x-4">
            <button onclick="window.print()" class="bg-white text-green-700 px-4 py-1 rounded-md flex items-center">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <button onclick="downloadPDF()" class="bg-white text-green-700 px-4 py-1 rounded-md flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </button>
            <button onclick="window.location.href='../index.php'" class="bg-green-800 hover:bg-green-900 text-white px-4 py-1 rounded-md">
                <i class="fas fa-home mr-2"></i> Home
            </button>
        </div>
    </div>
    
    <!-- Main content (visible in both screen and print) -->
    <div class="container mx-auto px-4 py-6" id="printable-content">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-800"><?php echo htmlspecialchars($election_name); ?></h1>
            <p class="text-gray-600 mt-2">Official Election Results | <?php echo date('F j, Y'); ?></p>
            <div class="flex justify-center items-center mt-4 text-sm">
                <div class="bg-gray-100 rounded-lg px-4 py-2 inline-flex items-center">
                    <div class="mr-6">
                        <span class="text-gray-600">Voter Turnout:</span>
                        <span class="font-bold text-green-700"><?php echo $turnout_percentage; ?>%</span>
                        <span class="text-gray-600 ml-1">(<?php echo $votes_cast; ?>/<?php echo $total_voters; ?>)</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Date Generated:</span>
                        <span class="font-bold"><?php echo date('M j, Y - g:i A'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Results Tables by Position -->
        <?php foreach ($positionsData as $position => $data): ?>
            <div class="mb-8">
                <div class="bg-green-700 text-white py-2 px-4 rounded-t-lg">
                    <h2 class="text-lg font-bold">Position: <?php echo htmlspecialchars($position); ?></h2>
                </div>
                
                <div class="bg-white shadow-md rounded-b-lg overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 text-left">Rank</th>
                                <th class="py-2 px-4 text-left">Candidate</th>
                                <th class="py-2 px-4 text-left">Party</th>
                                <th class="py-2 px-4 text-left">Votes</th>
                                <th class="py-2 px-4 text-left">Percentage</th>
                                <th class="py-2 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['candidates'] as $index => $candidate): ?>
                                <?php $percentage = $total_voters > 0 ? round(($candidate['total_votes'] / $total_voters) * 100, 1) : 0; ?>
                                <tr class="<?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?> border-b">
                                    <td class="py-2 px-4"><?php echo $index + 1; ?></td>
                                    <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($candidate['candidate']); ?></td>
                                    <td class="py-2 px-4"><?php echo !empty($candidate['partylists']) ? htmlspecialchars($candidate['partylists']) : '-'; ?></td>
                                    <td class="py-2 px-4 font-bold"><?php echo number_format($candidate['total_votes']); ?></td>
                                    <td class="py-2 px-4"><?php echo $percentage; ?>%</td>
                                    <td class="py-2 px-4">
                                        <?php if ($candidate['is_winner'] ?? false): ?>
                                            <span class="winner-tag">WINNER</span>
                                        <?php elseif ($candidate['is_tie'] ?? false): ?>
                                            <span class="tie-tag">TIED</span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Certification -->
        <div class="certification mt-10 border-t-2 border-green-700 pt-6">
            <div class="text-center">
                <p class="text-gray-600 mb-8">I hereby certify that the election results shown above are true and accurate.</p>
                
                <div class="inline-block">
                    <div class="border-b border-gray-400 w-48 h-8 mx-auto mb-1"></div>
                    <p class="font-semibold">Election Administrator</p>
                    <p class="text-sm text-gray-500"><?php echo $_SESSION['admin_name'] ?? 'System Administrator'; ?></p>
                </div>
                
                <div class="text-xs text-gray-500 mt-10">
                    <p>Document ID: <?php echo strtoupper(substr(md5($election_id . time()), 0, 8)); ?></p>
                    <p>Generated on <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer (only visible on screen) -->
    <footer class="bg-green-800 text-white text-center py-4 mt-10 no-print">
        <p>&copy; <?php echo date('Y'); ?> Election System</p>
        <p class="text-sm opacity-75">All rights reserved</p>
    </footer>
    
    <script>
        function downloadPDF() {
            // Create loading message
            const loader = document.createElement('div');
            loader.innerHTML = '<div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; z-index:9999;"><div style="background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.3);"><i class="fas fa-spinner fa-spin mr-2"></i> Generating PDF...</div></div>';
            document.body.appendChild(loader);
            
            const { jsPDF } = window.jspdf;
            const content = document.getElementById('printable-content');
            
            setTimeout(() => {
                html2canvas(content, {
                    scale: 1,
                    useCORS: true,
                    logging: false
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = pdf.internal.pageSize.getHeight();
                    const imgWidth = canvas.width;
                    const imgHeight = canvas.height;
                    const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
                    const imgX = (pdfWidth - imgWidth * ratio) / 2;
                    const imgY = 30;
                    
                    pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio);
                    pdf.save('<?php echo preg_replace("/[^A-Za-z0-9]/", "_", $election_name); ?>_results.pdf');
                    
                    // Remove loading message
                    document.body.removeChild(loader);
                });
            }, 500);
        }
    </script>
</body>
</html>
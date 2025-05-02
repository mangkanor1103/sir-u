<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];

    // Fetch deleted election details
    $sql = "SELECT * FROM history WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // Function to calculate and display vote summary
    function displayVoteSummary($votes, $candidates, $positions) {
        if(empty($votes) || empty($candidates) || empty($positions)) {
            return "<p>No voting data available.</p>";
        }

        $voteRecords = explode(";", $votes);
        $candidateRecords = explode(";", $candidates);
        $positionRecords = explode(";", $positions);

        // Create arrays to store vote counts and candidate details
        $voteCounts = array();
        $candidateDetails = array();
        $positionDetails = array();

        // Process candidates
        foreach($candidateRecords as $candidate) {
            $data = explode("|", $candidate);
            if(count($data) >= 4) {
                $candidateDetails[$data[0]] = array(
                    'name' => $data[2] . " " . $data[3],
                    'position_id' => $data[1]
                );
            }
        }

        // Process positions
        foreach($positionRecords as $position) {
            $data = explode("|", $position);
            if(count($data) >= 2) {
                $positionDetails[$data[0]] = $data[1];
            }
        }

        // Count votes
        foreach($voteRecords as $vote) {
            $data = explode("|", $vote);
            if(count($data) >= 3) {
                $candidate_id = $data[2];
                $position_id = $data[3];

                if(!isset($voteCounts[$position_id][$candidate_id])) {
                    $voteCounts[$position_id][$candidate_id] = 0;
                }
                $voteCounts[$position_id][$candidate_id]++;
            }
        }

        $output = "";
        
        // Display results by position
        foreach($positionDetails as $position_id => $position_name) {
            $output .= "<div class='position-section mb-4'>";
            $output .= "<div class='position-header bg-green-700 text-white py-2 px-4 rounded-t-lg'>";
            $output .= "<h5 class='m-0 font-bold'>Position: $position_name</h5>";
            $output .= "</div>";
            
            $output .= "<div class='bg-white shadow-sm rounded-b-lg'>";
            
            if(isset($voteCounts[$position_id])) {
                // Sort candidates by vote count
                arsort($voteCounts[$position_id]);

                // Calculate total votes for this position
                $totalVotes = array_sum($voteCounts[$position_id]);

                // Calculate minimum votes needed to win (50% + 1)
                $minimumVotesToWin = floor($totalVotes / 2) + 1;

                $output .= "<table class='w-full'>";
                $output .= "<thead class='bg-gray-100'>";
                $output .= "<tr>
                        <th class='py-2 px-4 text-left'>Rank</th>
                        <th class='py-2 px-4 text-left'>Candidate</th>
                        <th class='py-2 px-4 text-left'>Votes</th>
                        <th class='py-2 px-4 text-left'>Percentage</th>
                        <th class='py-2 px-4 text-left'>Status</th>
                    </tr></thead>";
                $output .= "<tbody>";

                $rank = 1;
                $rowClass = '';
                foreach($voteCounts[$position_id] as $candidate_id => $votes) {
                    if(isset($candidateDetails[$candidate_id])) {
                        $percentage = ($totalVotes > 0) ? round(($votes / $totalVotes) * 100, 2) : 0;
                        $rowClass = ($rank % 2 === 0) ? 'bg-white' : 'bg-gray-50';

                        // Determine status based on 50%+1 rule
                        if($rank === 1) {
                            if($votes >= $minimumVotesToWin) {
                                $status = "<span class='winner-tag'>WINNER</span>";
                            } else {
                                $status = "<span class='tie-tag'>RE-ELECTION</span>";
                            }
                        } else {
                            $status = "";
                        }

                        $output .= "<tr class='$rowClass border-b'>";
                        $output .= "<td class='py-2 px-4'>" . $rank . "</td>";
                        $output .= "<td class='py-2 px-4 font-medium'>" . $candidateDetails[$candidate_id]['name'] . "</td>";
                        $output .= "<td class='py-2 px-4 font-bold'>" . $votes . "</td>";
                        $output .= "<td class='py-2 px-4'>" . $percentage . "%</td>";
                        $output .= "<td class='py-2 px-4'>" . $status . "</td>";
                        $output .= "</tr>";
                        $rank++;
                    }
                }
                
                $output .= "<tr class='bg-gray-100 border-t-2 border-gray-300'>";
                $output .= "<td colspan='2' class='py-2 px-4'><strong>Total Votes:</strong></td>";
                $output .= "<td class='py-2 px-4 font-bold'>" . $totalVotes . "</td>";
                $output .= "<td class='py-2 px-4'>100%</td>";
                $output .= "<td></td>";
                $output .= "</tr>";
                
                $output .= "</tbody>";
                $output .= "</table>";
            } else {
                $output .= "<div class='py-4 px-4 text-center text-yellow-700 bg-yellow-50'>No votes recorded for this position.</div>";
            }
            
            $output .= "</div>";
            $output .= "</div>";
        }
        
        return $output;
    }
    
    // Start the HTML output
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $row['election_title']; ?> - Historical Results</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
            
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            
            .container {
                max-width: 1000px;
                margin: 0 auto;
                padding: 20px;
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
                    background-color: white;
                    font-family: 'Poppins', sans-serif;
                }
                
                .container {
                    max-width: 100%;
                    padding: 0;
                    margin: 0;
                }
                
                .position-header {
                    background-color: #1b5e20 !important;
                    color: white !important;
                    print-color-adjust: exact;
                    -webkit-print-color-adjust: exact;
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
                }
                
                th {
                    background-color: #f2f2f2 !important;
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
            }
        </style>
    </head>
    <body>
        <div id="printable-content" class="container">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-green-800"><?php echo $row['election_title']; ?></h1>
                <p class="text-gray-600">Historical Election Results</p>
                <p class="text-sm text-gray-500 mt-2">Archived on: <?php echo date('F j, Y', strtotime($row['deleted_at'])); ?></p>
            </div>
            
            <!-- Results by Position -->
            <div class="results-container">
                <?php echo displayVoteSummary($row['votes'], $row['candidates'], $row['positions']); ?>
            </div>
            
            <!-- Certification -->
            <div class="certification mt-10 pt-6 border-t-2 border-green-700">
                <div class="text-center">
                    <p class="text-gray-600 mb-6">This is an archived record of the election results.</p>
                    
                    <div class="inline-block">
                        <div class="border-b border-gray-400 w-48 h-8 mx-auto mb-1"></div>
                        <p class="font-semibold">Election Administrator</p>
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-10">
                        <p>Archive ID: <?php echo strtoupper(substr(md5($row['id']), 0, 8)); ?></p>
                        <p>Retrieved on <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Print/Back Buttons -->
        <div class="container mt-6 mb-10 flex justify-center no-print">
            <button onclick="window.print()" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md mr-4">
                <i class="fa fa-print"></i> Print Results
            </button>
            <button onclick="window.location.reload()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Back
            </button>
        </div>
    </body>
    </html>
    
    <?php
}
?>

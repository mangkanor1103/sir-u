<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];

    // Fetch deleted election details
    $sql = "SELECT * FROM history WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    echo "<div id='printable-content'>";
    echo "<h4>Election Results Summary</h4>";
    echo "<h5>Election Name: ".$row['election_title']."</h5>";
    echo "<p>Date: ".$row['deleted_at']."</p>";

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

        // Display results by position
        echo "<div class='results-container'>";
        foreach($positionDetails as $position_id => $position_name) {
            echo "<div class='position-results'>";
            echo "<h6 class='position-title'>$position_name</h6>";

            if(isset($voteCounts[$position_id])) {
                // Sort candidates by vote count
                arsort($voteCounts[$position_id]);

                // Calculate total votes for this position
                $totalVotes = array_sum($voteCounts[$position_id]);

                // Calculate minimum votes needed to win (50% + 1)
                $minimumVotesToWin = floor($totalVotes / 2) + 1;

                echo "<table class='table table-bordered table-striped'>";
                echo "<thead class='thead-dark'>";
                echo "<tr>
                        <th>Rank</th>
                        <th>Candidate</th>
                        <th>Votes</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr></thead>";
                echo "<tbody>";

                $rank = 1;
                foreach($voteCounts[$position_id] as $candidate_id => $votes) {
                    if(isset($candidateDetails[$candidate_id])) {
                        $percentage = ($totalVotes > 0) ? round(($votes / $totalVotes) * 100, 2) : 0;

                        // Determine status based on 50%+1 rule
                        if($rank === 1) {
                            if($votes >= $minimumVotesToWin) {
                                $status = "<span class='badge bg-success'>WINNER</span>";
                            } else {
                                $status = "<span class='badge bg-warning'>RE-ELECTION NEEDED</span>";
                            }
                        } else {
                            $status = "<span class='badge bg-secondary'>RUNNER-UP</span>";
                        }

                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . $candidateDetails[$candidate_id]['name'] . "</td>";
                        echo "<td>" . $votes . "</td>";
                        echo "<td>" . $percentage . "%</td>";
                        echo "<td>" . $status . "</td>";
                        echo "</tr>";
                        $rank++;
                    }
                }
                echo "</tbody>";
                echo "<tfoot>
                        <tr class='table-info'>
                            <td colspan='2'><strong>Total Votes:</strong></td>
                            <td><strong>" . $totalVotes . "</strong></td>
                            <td><strong>100%</strong></td>
                            <td></td>
                        </tr>
                        <tr class='table-warning'>
                            <td colspan='2'><strong>Votes Needed to Win (50%+1):</strong></td>
                            <td colspan='3'><strong>" . $minimumVotesToWin . " votes</strong></td>
                        </tr>
                    </tfoot>";
                echo "</table>";
            } else {
                echo "<p class='alert alert-warning'>No votes recorded for this position.</p>";
            }
            echo "</div>";
        }
        echo "</div>";
    }

    // Display the vote summary
    displayVoteSummary($row['votes'], $row['candidates'], $row['positions']);

    echo "</div>"; // Close printable-content div

    // Add print button
    echo "<button class='btn btn-success' id='print-button'><i class='fa fa-print'></i> Print Results</button>";
}
?>

<style>
.results-container {
    margin: 20px 0;
}
.position-results {
    margin-bottom: 30px;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.position-title {
    color: #2c3e50;
    border-bottom: 2px solid #28a745;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.badge {
    padding: 5px 10px;
    border-radius: 3px;
}
.bg-success {
    background-color: #28a745;
    color: white;
}
.bg-warning {
    background-color: #ffc107;
    color: #000;
}
.bg-secondary {
    background-color: #6c757d;
    color: white;
}
table {
    margin-top: 10px;
    width: 100%;
}
.thead-dark th {
    background-color: #343a40;
    color: white;
}
.table-info {
    background-color: #e3f2fd;
}
.table-warning {
    background-color: #fff3cd;
}
</style>

<script>
document.getElementById('print-button').addEventListener('click', function() {
    var printContent = document.getElementById('printable-content').innerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = "<h2>Election Results Summary</h2>" + printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
});
</script>

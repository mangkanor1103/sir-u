<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green layout-top-nav">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <?php
                    // Fetch the election details for the current voter
                    $sql = "SELECT * FROM elections WHERE id = (SELECT election_id FROM voters WHERE id = '" . $voter['id'] . "')";
                    $election_query = $conn->query($sql);
                    
                    if ($election_query && $election_query->num_rows > 0) {
                        $election = $election_query->fetch_assoc();
                        echo "<h1 class='page-header text-center title' style='color: #70C237;'><b>" . strtoupper($election['name']) . "</b></h1>";

                        // Check if the voter has already voted for this election
                        $voteCheckQuery = "SELECT * FROM votes WHERE election_id = '" . $election['id'] . "' AND voters_id = '" . $voter['id'] . "'";
                        $voteCheckResult = $conn->query($voteCheckQuery);

                        // If the voter has already voted, fetch their votes
                        if ($voteCheckResult && $voteCheckResult->num_rows > 0) {
                            echo "<h3 class='text-center'>You have already voted. Here are your selections:</h3>";
                            $votesQuery = "
                                SELECT v.*, c.firstname, c.lastname, p.description AS position_description 
                                FROM votes v 
                                JOIN candidates c ON v.candidate_id = c.id 
                                JOIN positions p ON v.position_id = p.position_id 
                                WHERE v.voters_id = '" . $voter['id'] . "' 
                                AND v.election_id = '" . $election['id'] . "'
                            ";

                            $votesResult = $conn->query($votesQuery);

                            if ($votesResult && $votesResult->num_rows > 0) {
                                while ($vote = $votesResult->fetch_assoc()) {
                                    echo "<div class='alert alert-info'>
                                            <strong>Voted for:</strong> " . $vote['firstname'] . " " . $vote['lastname'] . " for " . $vote['position_description'] . "
                                          </div>";
                                }
                                // Feedback button
                                echo "<div class='text-center'>
                                        <a href='feedback.php' class='btn btn-primary btn-flat'><i class='fa fa-comments'></i> Give Feedback</a>
                                      </div>";
                            } else {
                                echo "<div class='alert alert-warning'>No votes found for this election.</div>";
                            }
                        } else {
                            // Voting logic for those who haven't voted
                            if (isset($_POST['submit_votes'])) {
                                $hasError = false; // Flag to check for errors during insertion

                                // Loop through each position and the selected candidates
                                foreach ($_POST['candidates'] as $position_id => $selected_candidates) {
                                    // Insert each selected candidate's vote, ensuring it's only one vote per position
                                    foreach ($selected_candidates as $candidate_id) {
                                        // Check if the voter has already voted for this position
                                        $existingVoteCheckQuery = "SELECT * FROM votes WHERE election_id = '" . $election['id'] . "' 
                                                                    AND voters_id = '" . $voter['id'] . "' 
                                                                    AND position_id = '" . $position_id . "'";
                                        $existingVoteResult = $conn->query($existingVoteCheckQuery);

                                        // If the voter has not voted for this position, insert the vote
                                        if ($existingVoteResult->num_rows == 0) {
                                            $insert_sql = "INSERT INTO votes (election_id, voters_id, candidate_id, position_id) 
                                                           VALUES ('" . $election['id'] . "', '" . $voter['id'] . "', '" . $candidate_id . "', '" . $position_id . "')";
                                            if (!$conn->query($insert_sql)) {
                                                $hasError = true; // Set error flag if insertion fails
                                                break;
                                            }
                                        }
                                    }
                                }

                                // Display submission result
                                if (!$hasError) {
                                    echo "<div class='alert alert-success'>Votes submitted successfully!</div>";
                                    // Fetch and display the submitted votes
                                    $votesQuery = "
                                        SELECT v.*, c.firstname, c.lastname, p.description AS position_description 
                                        FROM votes v 
                                        JOIN candidates c ON v.candidate_id = c.id 
                                        JOIN positions p ON v.position_id = p.position_id 
                                        WHERE v.voters_id = '" . $voter['id'] . "' 
                                        AND v.election_id = '" . $election['id'] . "'
                                    ";
                                    
                                    $votesResult = $conn->query($votesQuery);

                                    if ($votesResult && $votesResult->num_rows > 0) {
                                        echo "<h3 class='text-center'>Here are your selections:</h3>";
                                        while ($vote = $votesResult->fetch_assoc()) {
                                            echo "<div class='alert alert-info'>
                                                    <strong>Voted for:</strong> " . $vote['firstname'] . " " . $vote['lastname'] . " for " . $vote['position_description'] . "
                                                  </div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-warning'>No votes found for this election.</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>An error occurred while submitting your vote. Please try again.</div>";
                                }
                            } else {
                                // Fetch and display candidates for each position in the current election
                                $sql_positions = "SELECT * FROM positions WHERE election_id = '" . $election['id'] . "' ORDER BY priority ASC";
                                $positions_query = $conn->query($sql_positions);
                                echo "<form method='post' id='voteForm'>"; // Form tag added here

                                while ($position = $positions_query->fetch_assoc()) {
                                    echo "<div class='row'>
                                            <div class='col-xs-12'>
                                                <div class='box box-solid' id='" . $position['position_id'] . "'>
                                                    <div class='box-header with-border'>
                                                        <h3 class='box-title'><b>" . $position['description'] . "</b></h3>
                                                    </div>
                                                    <div class='box-body'>";

                                    // Fetch candidates for the current position
                                    $sql_candidates = "SELECT * FROM candidates WHERE position_id = '" . $position['position_id'] . "'";
                                    $candidates_query = $conn->query($sql_candidates);
                                    while ($candidate = $candidates_query->fetch_assoc()) {
                                        // Display candidate information and input fields (checkboxes or radio buttons)
                                        echo "<label>
                                                <input type='" . ($position['max_vote'] > 1 ? "checkbox" : "radio") . "' name='candidates[" . $position['position_id'] . "][]' value='" . $candidate['id'] . "'>
                                                " . $candidate['firstname'] . " " . $candidate['lastname'] . "
                                              </label><br>";
                                    }

                                    echo "          </div>
                                                </div>
                                            </div>
                                          </div>";
                                }

                                // Display voting buttons
                                echo "<div class='text-center'>
                                        <button type='submit' name='submit_votes' class='btn btn-success btn-flat'><i class='fa fa-check-square-o'></i> Submit Votes</button>
                                      </div>";
                                echo "</form>"; // Form tag closing added here
                            }
                        }
                    } else {
                        echo "<h1 class='page-header text-center title' style='color: #70C237;'><b>No Election Found</b></h1>";
                    }
                ?>
            </section>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<!-- Your JavaScript code -->
</body>
</html>

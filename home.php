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
                $stmt = $conn->prepare("SELECT * FROM elections WHERE id = (SELECT election_id FROM voters WHERE id = ?)");
                $stmt->bind_param("i", $voter['id']);
                $stmt->execute();
                $election_query = $stmt->get_result();

                if ($election_query && $election_query->num_rows > 0) {
                    $election = $election_query->fetch_assoc();
                    // Set the election ID in the session
                    $_SESSION['election_id'] = $election['id'];

                    echo "<h1 class='page-header text-center title' style='color: #70C237;'><b>" . strtoupper($election['name']) . "</b></h1>";

                    // Check if the voter has already voted for this election
                    $stmt = $conn->prepare("SELECT * FROM votes WHERE election_id = ? AND voters_id = ?");
                    $stmt->bind_param("ii", $election['id'], $voter['id']);
                    $stmt->execute();
                    $voteCheckResult = $stmt->get_result();

                    // If the voter has already voted, fetch their votes
                    if ($voteCheckResult && $voteCheckResult->num_rows > 0) {
                        echo "<h3 class='text-center'>You have already voted. Here are your selections:</h3>";
                        $stmt = $conn->prepare("
                            SELECT v.*, c.firstname, c.lastname, p.description AS position_description
                            FROM votes v
                            JOIN candidates c ON v.candidate_id = c.id
                            JOIN positions p ON v.position_id = p.position_id
                            WHERE v.voters_id = ? AND v.election_id = ?
                        ");
                        $stmt->bind_param("ii", $voter['id'], $election['id']);
                        $stmt->execute();
                        $votesResult = $stmt->get_result();

                        if ($votesResult && $votesResult->num_rows > 0) {
                            while ($vote = $votesResult->fetch_assoc()) {
                                echo "<div class='alert alert-info'>
                                        <strong>Voted for:</strong> " . htmlspecialchars($vote['firstname'] . " " . $vote['lastname'] . " for " . $vote['position_description']) . "
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
                                    $stmt = $conn->prepare("SELECT * FROM votes WHERE election_id = ? AND voters_id = ? AND position_id = ?");
                                    $stmt->bind_param("iii", $election['id'], $voter['id'], $position_id);
                                    $stmt->execute();
                                    $existingVoteResult = $stmt->get_result();

                                    // If the voter has not voted for this position, insert the vote
                                    if ($existingVoteResult->num_rows == 0) {
                                        $insert_stmt = $conn->prepare("INSERT INTO votes (election_id, voters_id, candidate_id, position_id) VALUES (?, ?, ?, ?)");
                                        $insert_stmt->bind_param("iiii", $election['id'], $voter['id'], $candidate_id, $position_id);
                                        if (!$insert_stmt->execute()) {
                                            $hasError = true; // Set error flag if insertion fails
                                            break;
                                        }
                                    }
                                }
                            }

                            // Display submission result
                            if (!$hasError) {
                                echo "<div class='alert alert-success alert-dismissible fade show text-center' role='alert' style='font-size: 18px; font-weight: bold;'>
                                        <i class='fa fa-check-circle'></i> Your votes have been submitted successfully! Redirecting to feedback...
                                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                      </div>";

                                echo "<script>
                                        setTimeout(function() {
                                            window.location.href = 'feedback.php';
                                        }, 500); // 0.5-second delay to show the alert
                                      </script>";

                                // Fetch and display the submitted votes
                                $stmt = $conn->prepare("
                                    SELECT v.*, c.firstname, c.lastname, p.description AS position_description
                                    FROM votes v
                                    JOIN candidates c ON v.candidate_id = c.id
                                    JOIN positions p ON v.position_id = p.position_id
                                    WHERE v.voters_id = ? AND v.election_id = ?
                                ");
                                $stmt->bind_param("ii", $voter['id'], $election['id']);
                                $stmt->execute();
                                $votesResult = $stmt->get_result();

                                if ($votesResult && $votesResult->num_rows > 0) {
                                    echo "<h3 class='text-center'>Here are your selections:</h3>";
                                    while ($vote = $votesResult->fetch_assoc()) {
                                        echo "<div class='alert alert-info'>
                                                <strong>Voted for:</strong> " . htmlspecialchars($vote['firstname'] . " " . $vote['lastname'] . " for " . $vote['position_description']) . "
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
                            $stmt = $conn->prepare("SELECT * FROM positions WHERE election_id = ? ORDER BY description ASC");
                            $stmt->bind_param("i", $election['id']);
                            $stmt->execute();
                            $positions_query = $stmt->get_result();
                            echo "<form method='post' id='voteForm'>"; // Form tag added here

                            while ($position = $positions_query->fetch_assoc()) {
                                echo "<div class='row'>
                                        <div class='col-xs-12'>
                                            <div class='box box-solid' id='" . htmlspecialchars($position['position_id']) . "'>
                                                <div class='box-header with-border'>
                                                    <h3 class='box-title'><b>" . htmlspecialchars($position['description']) . "</b></h3>
                                                </div>
                                                <div class='box-body'>";

                                // Fetch candidates for the current position
                                $stmt = $conn->prepare("SELECT id, firstname, lastname, photo FROM candidates WHERE position_id = ?");
                                $stmt->bind_param("i", $position['position_id']);
                                $stmt->execute();
                                $candidates_query = $stmt->get_result();

                                // Check if there are candidates
                                if ($candidates_query && $candidates_query->num_rows > 0) {
                                    echo "<div style='display: flex; justify-content: center; flex-wrap: wrap; margin-bottom: 20px;'>"; // Parent container for candidates

                                    while ($candidate = $candidates_query->fetch_assoc()) {
                                        echo "<div class='candidate' style='display: flex; flex-direction: column; align-items: center; margin: 10px; cursor: pointer;'
                                            onclick='selectCandidate(this, " . htmlspecialchars($candidate['id']) . ", " . htmlspecialchars($position['position_id']) . ", " . htmlspecialchars($position['max_vote']) . ")'>
                                            <img src='sub/" . htmlspecialchars($candidate['photo']) . "' style='width: 300px; height: 300px; object-fit: cover; margin-bottom: 5px; border: 3px solid transparent;'>
                                            <span style='text-align: center;'>" . htmlspecialchars($candidate['firstname'] . " " . $candidate['lastname']) . "</span>
                                            <input type='hidden' name='candidates[" . htmlspecialchars($position['position_id']) . "][]' value='" . htmlspecialchars($candidate['id']) . "'>
                                        </div>";
                                    }

                                    echo "</div>"; // Close the parent container
                                } else {
                                    echo "<div class='alert alert-warning'>No candidates found for this position.</div>";
                                }

                                echo "          </div>
                                            </div>
                                        </div>
                                      </div>";
                            }

                            // Display voting buttons
                            echo "<button type='button' class='btn btn-success btn-flat' onclick='return showConfirmation()'>
                                <i class='fa fa-check-square-o'></i> Review & Submit Votes
                            </button>";

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
<script>
function showConfirmation() {
    let voteReviewList = document.getElementById("voteReviewList");
    voteReviewList.innerHTML = "";

    let selectedCandidates = document.querySelectorAll(".selected");

    if (selectedCandidates.length === 0) {
        alert("Please select at least one candidate before submitting.");
        return;
    }

    selectedCandidates.forEach(candidate => {
        let candidateName = candidate.querySelector("span").textContent;
        let listItem = document.createElement("li");
        listItem.classList.add("list-group-item");
        listItem.textContent = candidateName;
        voteReviewList.appendChild(listItem);
    });

    $("#confirmationModal").modal("show");  // Show modal
}
</script>

<script>
function confirmVote() {
    return confirm("Are you sure you want to submit your votes? Once submitted, you cannot change your selections.");
}
</script>

<script>
function selectCandidate(element, candidateId, positionId, maxVote) {
    let positionContainer = document.getElementById(positionId);
    let selectedCandidates = positionContainer.querySelectorAll(".selected");

    if (maxVote === 1) {
        // Single vote per position (radio button functionality)
        selectedCandidates.forEach(c => {
            c.classList.remove("selected");
            c.querySelector("input").value = "";
            c.querySelector("img ").style.border = "3px solid transparent";
        });
    } else if (selectedCandidates.length >= maxVote) {
        alert("You can only select up to " + maxVote + " candidates for this position.");
        return;
    }

    if (element.classList.contains("selected")) {
        // Deselect if already selected
        element.classList.remove("selected");
        element.querySelector("input").value = "";
        element.querySelector("img").style.border = "3px solid transparent";
    } else {
        // Select the candidate
        element.classList.add("selected");
        element.querySelector("input").value = candidateId;
        element.querySelector("img").style.border = "3px solid #70C237";
    }
}
</script>
<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Your Votes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h5 class="text-center">Please review your votes before submitting:</h5>
                <ul id="voteReviewList" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Edit Choices</button>
                <button type="submit" name="submit_votes" class="btn btn-success" form="voteForm">Submit Votes</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
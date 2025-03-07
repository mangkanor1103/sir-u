<?php
include 'includes/session.php';

if (isset($_GET['election_id'])) {
    // Fetch election details
    $stmt = $conn->prepare('SELECT * FROM elections WHERE id = ?');
    $stmt->bind_param('i', $_GET['election_id']);
    $stmt->execute();
    $election = $stmt->get_result()->fetch_assoc();

    if ($election) {
        // Fetch positions for this election
        $stmt = $conn->prepare('
            SELECT
                p.position_id,
                p.description as position_name,
                c.id as candidate_id,
                CONCAT(c.firstname, " ", c.lastname) as candidate_name,
                pl.name as partylist_name,
                COUNT(v.id) as vote_count
            FROM positions p
            LEFT JOIN candidates c ON c.position_id = p.position_id
            LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
            LEFT JOIN votes v ON v.candidate_id = c.id
            WHERE p.election_id = ?
            GROUP BY p.position_id, c.id
            ORDER BY p.position_id ASC, vote_count DESC
        ');
        $stmt->bind_param('i', $_GET['election_id']);
        $stmt->execute();
        $results = $stmt->get_result();

        // Organize results by position
        $positions = [];
        while ($row = $results->fetch_assoc()) {
            if (!isset($positions[$row['position_id']])) {
                $positions[$row['position_id']] = [
                    'name' => $row['position_name'],
                    'candidates' => [],
                    'total_votes' => 0
                ];
            }
            if ($row['candidate_id']) { // Only add if there's a candidate
                $positions[$row['position_id']]['candidates'][] = $row;
                $positions[$row['position_id']]['total_votes'] += $row['vote_count'];
            }
        }

        // Fetch abstain counts
        $stmt = $conn->prepare('
            SELECT
                p.position_id,
                COUNT(*) as abstain_count
            FROM votes v
            JOIN positions p ON p.position_id = v.position_id
            WHERE v.election_id = ? AND v.candidate_id IS NULL
            GROUP BY p.position_id
        ');
        $stmt->bind_param('i', $_GET['election_id']);
        $stmt->execute();
        $abstains = $stmt->get_result();

        // Add abstain counts to positions array
        while ($abstain = $abstains->fetch_assoc()) {
            if (isset($positions[$abstain['position_id']])) {
                $positions[$abstain['position_id']]['abstain_count'] = $abstain['abstain_count'];
                $positions[$abstain['position_id']]['total_votes'] += $abstain['abstain_count'];
            }
        }
    } else {
        exit('Election with that ID does not exist.');
    }
} else {
    exit('No election ID specified.');
}
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green layout-top-nav">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <h1 class="page-header text-center title">
                    <b><?php echo htmlspecialchars($election['name'], ENT_QUOTES); ?> - Results</b>
                </h1>

                <!-- Back Button -->
                <div class="text-center">
                    <a href="sub_admins.php" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <br>

                <div id="notification" class="alert alert-info" style="display: none; position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1000;">
                    Refreshing results...
                </div>

                <div id="results-container">
                    <?php foreach ($positions as $position): ?>
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><b><?php echo htmlspecialchars($position['name'], ENT_QUOTES); ?></b></h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Candidate</th>
                                            <th>Party List</th>
                                            <th>Votes</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($position['candidates'] as $candidate): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($candidate['candidate_name'], ENT_QUOTES); ?></td>
                                                <td><?php echo htmlspecialchars($candidate['partylist_name'], ENT_QUOTES); ?></td>
                                                <td><?php echo $candidate['vote_count']; ?></td>
                                                <td>
                                                    <?php
                                                    $percentage = $position['total_votes'] > 0
                                                        ? round(($candidate['vote_count'] / $position['total_votes']) * 100, 2)
                                                        : 0;
                                                    echo $percentage . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (isset($position['abstain_count'])): ?>
                                            <tr>
                                                <td colspan="2"><em>ABSTAINED</em></td>
                                                <td><?php echo $position['abstain_count']; ?></td>
                                                <td>
                                                    <?php
                                                    $percentage = $position['total_votes'] > 0
                                                        ? round(($position['abstain_count'] / $position['total_votes']) * 100, 2)
                                                        : 0;
                                                    echo $percentage . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>

<!-- Auto-refresh functionality with notification -->
<script>
function refreshResults() {
    const notification = document.getElementById('notification');
    notification.style.display = 'block'; // Show notification

    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            // Update results container
            document.getElementById('results-container').innerHTML = newDoc.getElementById('results-container').innerHTML;

            // Hide notification after a short delay
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000); // Hide after 2 seconds
        })
        .catch(error => {
            console.error('Error refreshing results:', error);
            notification.innerHTML = 'Error refreshing results.';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000); // Hide after 2 seconds
        });
}

// Refresh every 5 seconds
setInterval(refreshResults, 5000);
</script>

</body>
</html>

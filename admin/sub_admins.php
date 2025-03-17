<?php
include 'includes/session.php';

// Handle form submission to generate codes
if (isset($_POST['generate'])) {
    $election_name = $_POST['election_name'];
    $quantity = intval($_POST['quantity']); // Assuming you have a field in your form to input the quantity of codes

    for ($i = 0; $i < $quantity; $i++) {
        // Generate election code
        $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $election_code = substr(str_shuffle($set), 0, 10);

        // Set status to 0 (not started) by default
        $sql = "INSERT INTO elections (name, election_code, status) VALUES ('$election_name', '$election_code', 0)";
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Election code generated successfully for ' . $election_name . '. Code: ' . $election_code;
        } else {
            $_SESSION['error'] = $conn->error;
        }
    }
    header('location: sub_admins.php');
    exit();
}

// Handle starting an election
if (isset($_POST['start_election'])) {
    $election_id = $_POST['election_id'];

    // Update the election status to 1 (started)
    $sql = "UPDATE elections SET status = 1 WHERE id = '$election_id'";

    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Election has been started successfully.';
    } else {
        $_SESSION['error'] = $conn->error;
    }

    header('location: sub_admins.php');
    exit();
}

// Handle ending an election
if (isset($_POST['end_election'])) {
    $election_id = $_POST['election_id'];

    // Start a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Fetch election title
        $result = $conn->query("SELECT name FROM elections WHERE id = '$election_id'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $election_title = $row['name'];

            // Store related data as a string in the history table
            $history_sql = "INSERT INTO history (election_title, deleted_at, candidates, voters, votes, positions, partylists)
                            VALUES ('$election_title', NOW(),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', position_id, '|', firstname, '|', lastname, '|', photo, '|', platform, '|', partylist_id) SEPARATOR ';') FROM candidates WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', voters_id) SEPARATOR ';') FROM voters WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(id, '|', voters_id, '|', candidate_id, '|', position_id, '|', timestamp) SEPARATOR ';') FROM votes WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(position_id, '|', description, '|', max_vote) SEPARATOR ';') FROM positions WHERE election_id = '$election_id'),
                                (SELECT GROUP_CONCAT(CONCAT(partylist_id, '|', name) SEPARATOR ';') FROM partylists WHERE election_id = '$election_id'))";
            $conn->query($history_sql) or die($conn->error);
        }

        // Delete related data in the correct order
        $conn->query("DELETE FROM votes WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM candidates WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM voters WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM positions WHERE election_id = '$election_id'") or die($conn->error);
        $conn->query("DELETE FROM partylists WHERE election_id = '$election_id'") or die($conn->error);

        // Finally, delete the election itself
        $conn->query("DELETE FROM elections WHERE id = '$election_id'") or die($conn->error);

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = 'Election has ended and all related records have been archived.';
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Failed to end election: " . $e->getMessage();
    }

    header('location: sub_admins.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Elections List</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Elections</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
          echo "<div class='alert alert-danger alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> Error!</h4>
                  " . $_SESSION['error'] . "
                </div>";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "<div class='alert alert-success alert-dismissible'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> Success!</h4>
                  " . $_SESSION['success'] . "
                </div>";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <form action="" method="post">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Election Name:</label>
                      <input type="text" name="election_name" class="form-control" placeholder="Enter election name" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Generate Codes:</label>
                      <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="submit" name="generate" class="btn btn-primary" style="margin-top: 25px;">Generate</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="box-body" style="overflow-x: auto;"> <!-- Make the box body scrollable horizontally -->
              <table id="example1" class="table table-bordered">
                <thead>
                  <tr>
                    <th>Election Name</th>
                    <th>Election Code</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Retrieve elections from the database
                    $sql = "SELECT * FROM elections";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        $status = ($row['status'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Not Started</span>';
                        $election_code = $row['election_code'];

                        echo "
                          <tr>
                            <td>" . $row['name'] . "</td>
                            <td>" . $election_code . "</td>
                            <td>" . $status . "</td>
                            <td>";

                        // Show Start button only if election is not started
                        if ($row['status'] == 0) {
                          echo "
                            <form method='POST' action='' style='display:inline;'>
                              <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                              <button type='submit' name='start_election' class='btn btn-success btn-sm'>Start Election</button>
                            </form> ";
                        }

                        echo "
                            <form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to end this election? All data will be archived.\");'>
                              <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                              <button type='submit' name='end_election' class='btn btn-danger btn-sm'>End Election</button>
                            </form>
                            <form method='GET' action='result.php' style='display:inline;'>
                              <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                              <button type='submit' class='btn btn-info btn-sm'>View Results</button>
                            </form>
                            </td>
                          </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='4'>No elections found</td></tr>";
                    }
                  ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
</body>
</html>
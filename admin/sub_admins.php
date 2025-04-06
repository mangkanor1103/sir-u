<?php
include 'includes/session.php';

// Handle form submission to generate a single code
if (isset($_POST['generate'])) {
    $election_name = $_POST['election_name'];

    // Generate a single election code
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $election_code = substr(str_shuffle($set), 0, 10);

    // Set status to 0 (not started) by default
    $sql = "INSERT INTO elections (name, election_code, status) VALUES ('$election_name', '$election_code', 0)";
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Election code generated successfully for ' . $election_name . '. Code: ' . $election_code;
    } else {
        $_SESSION['error'] = $conn->error;
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

    // Update the election status to 0 (ended)
    $sql = "UPDATE elections SET status = 0, end_time = NOW() WHERE id = '$election_id'";

    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Election has been ended successfully.';
    } else {
        $_SESSION['error'] = $conn->error;
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Election Name:</label>
                      <input type="text" name="election_name" class="form-control" placeholder="Enter election name" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <button type="submit" name="generate" class="btn btn-success" style="margin-top: 25px;">Generate Code</button>
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
                    <th>End Time</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    // Retrieve elections from the database
                    $sql = "SELECT id, name, election_code, status, end_time FROM elections";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        $status = ($row['status'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Not Started</span>';
                        $end_time = $row['end_time'] ? date('Y-m-d H:i:s', strtotime($row['end_time'])) : 'N/A';

                        echo "
                          <tr>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['election_code']) . "</td>
                            <td>" . $status . "</td>
                            <td>" . $end_time . "</td>
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
                            <form method='POST' action='' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to end this election?\");'>
                              <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                              <button type='submit' name='end_election' class='btn btn-danger btn-sm'>End Election</button>
                            </form>
                            <form method='GET' action='result.php' style='display:inline;'>
                              <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                              <button type='submit' class='btn btn-success btn-sm'>View Results</button>
                            </form>
                            </td>
                          </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='5'>No elections found</td></tr>";
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

<script>
  // Update pagination buttons to green
  document.addEventListener('DOMContentLoaded', function () {
    const paginationButtons = document.querySelectorAll('.pagination li a');
    paginationButtons.forEach(button => {
      button.classList.add('btn', 'btn-success', 'btn-sm');
    });
  });
</script>

</body>
</html>
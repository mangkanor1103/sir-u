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
        $_SESSION['success'] = 'Election code generated successfully for ' . $election_name . '.';
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

  <div class="content-wrapper" style="background-color: #f0fdf4;">
    <section class="content-header">
      <h1 style="color: #2e7d32;">Elections List</h1>
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
                      <label style="color: #2e7d32;">Election Name:</label>
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
                <thead style="background-color: #e8f5e9; color: #2e7d32;">
                  <tr>
                    <th>Election Name</th>
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
                            <td>" . $status . "</td>
                            <td>" . $end_time . "</td>
                            <td>
                              <button type='button' class='btn btn-success btn-sm' onclick='viewElectionCode(\"" . htmlspecialchars($row['election_code']) . "\")'>View Code</button>
                              <form method='GET' action='result.php' style='display:inline;'>
                                <input type='hidden' name='election_id' value='" . $row['id'] . "'>
                                <button type='submit' class='btn btn-success btn-sm'>View Results</button>
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

<!-- Modal for Viewing Election Code -->
<div id="electionCodeModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #e8f5e9; color: #2e7d32;">
        <h4 class="modal-title">Election Code</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="background-color: #f0fdf4;">
        <p id="electionCodeText" style="font-size: 18px; font-weight: bold; text-align: center; color: #2e7d32;"></p>
      </div>
      <div class="modal-footer" style="background-color: #e8f5e9;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  function viewElectionCode(code) {
    document.getElementById('electionCodeText').textContent = code;
    $('#electionCodeModal').modal('show');
  }
</script>

</body>
</html>
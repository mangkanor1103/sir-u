<?php
// Include session management and database connection
include 'includes/session.php';

// Fetch feedback data with election name
$sql = "
    SELECT elections.name AS election_name, feedback.feedback, feedback.created_at 
    FROM feedback 
    JOIN elections ON feedback.election_id = elections.id 
    ORDER BY feedback.created_at DESC";
$result = $conn->query($sql);
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>User Feedback</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Feedback</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">All Feedback</h3>
            </div>

            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Election Name</th> <!-- Changed to Election Name -->
                  <th>Feedback</th>
                  <th>Submitted At</th>
                </thead>
                <tbody>
                  <?php
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        echo "
                          <tr>
                            <td>" . htmlspecialchars($row['election_name']) . "</td> <!-- Display Election Name -->
                            <td>" . htmlspecialchars($row['feedback']) . "</td>
                            <td>" . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</td>
                          </tr>
                        ";
                      }
                    } else {
                      echo "<tr><td colspan='3' class='text-center'>No feedback found</td></tr>";
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
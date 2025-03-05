<?php
// Include session management and database connection
include 'includes/session.php';

// Fetch students data
$sql = "SELECT voters_id, name, year_section, course, election_id FROM students ORDER BY voters_id ASC"; // Adjust the query as needed
$result = $conn->query($sql);
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Registered Students</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Students</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">All Registered Students</h3>
            </div>

            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Voter ID</th>
                  <th>Name</th>
                  <th>Year & Section</th>
                  <th>Course</th>
                  <th>Election ID</th>
                </thead>
                <tbody>
                  <?php
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "
                          <tr>
                            <td>".htmlspecialchars($row['voters_id'])."</td>
                            <td>".htmlspecialchars($row['name'])."</td>
                            <td>".htmlspecialchars($row['year_section'])."</td>
                            <td>".htmlspecialchars($row['course'])."</td>
                            <td>".htmlspecialchars($row['election_id'])."</td>
                          </tr>
                        ";
                      }
                    } else {
                      echo "<tr><td colspan='5' class='text-center'>No students found</td></tr>";
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
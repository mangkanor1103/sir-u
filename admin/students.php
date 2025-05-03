<?php
// Include session management and database connection
include 'includes/session.php';

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Fetch total number of records
$total_sql = "SELECT COUNT(*) AS total FROM students";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit); // Total number of pages

// Fetch students data with election name
$sql = "
    SELECT students.id, students.name, students.year_section, students.course, elections.name AS election_name 
    FROM students 
    LEFT JOIN elections ON students.election_id = elections.id 
    ORDER BY students.name ASC 
    LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper" style="background-color: #f8faf8;">
    <section class="content-header">
      <h1 style="color: #046a0f; font-weight: 700; margin-bottom: 15px;">Students Management</h1>
      <ol class="breadcrumb" style="background-color: #ffffff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <li><a href="home.php" style="color: #046a0f;"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Students</li>
      </ol>
    </section>

    <section class="content" style="padding-top: 20px;">
      <?php
        if (isset($_SESSION['error'])) {
          echo "<div class='alert alert-danger alert-dismissible' style='border-left: 4px solid #d9534f; border-radius: 4px;'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-warning'></i> Error!</h4>
                  " . $_SESSION['error'] . "
                </div>";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "<div class='alert alert-success alert-dismissible' style='border-left: 4px solid #046a0f; border-radius: 4px;'>
                  <button type='button' class='close' data-dismiss='alert'>&times;</button>
                  <h4><i class='icon fa fa-check'></i> Success!</h4>
                  " . $_SESSION['success'] . "
                </div>";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div class="box-header with-border" style="background-color: #f0fdf0; border-bottom: 1px solid #e0f0e0; border-radius: 8px 8px 0 0; padding: 20px;">
              <h3 class="box-title" style="color: #046a0f; font-weight: 600;">
                <i class="fa fa-users" style="margin-right: 10px;"></i> All Registered Students
              </h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addStudentModal" style="background-color: #046a0f; border-color: #035a0d; padding: 5px 12px; font-weight: 600; transition: all 0.3s ease;">
                  <i class="fa fa-plus"></i> Add Student
                </button>
              </div>
            </div>

            <!-- Students Table -->
            <div class="box-body" style="padding: 20px; background-color: #fff; border-radius: 0 0 8px 8px;">
              <div class="table-responsive">
                <table id="students-table" class="table table-bordered table-hover" style="width: 100%;">
                  <thead style="background-color: #046a0f; color: white;">
                    <tr>
                      <th width="30%">Student Name</th>
                      <th width="20%">Year & Section</th>
                      <th width="25%">Course</th>
                      <th width="25%">Election</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          echo "
                            <tr>
                              <td style='vertical-align: middle;'><strong>" . htmlspecialchars($row['name']) . "</strong></td>
                              <td style='vertical-align: middle;'>" . htmlspecialchars($row['year_section']) . "</td>
                              <td style='vertical-align: middle;'>" . htmlspecialchars($row['course']) . "</td>
                              <td style='vertical-align: middle;'>" . (empty($row['election_name']) ? '<span class="label label-default" style="background-color: #777;">Not enrolled</span>' : '<span class="label label-success" style="background-color: #046a0f;">' . htmlspecialchars($row['election_name']) . '</span>') . "</td>
                            </tr>
                          ";
                        }
                      } else {
                        echo "<tr><td colspan='4' class='text-center' style='padding: 20px; color: #777;'>No students found</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination -->
            <div class="box-footer clearfix" style="background-color: #f0fdf0; border-top: 1px solid #e0f0e0; border-radius: 0 0 8px 8px; padding: 15px 20px;">
              <div class="pull-left" style="color: #046a0f; font-size: 14px;">
                Showing <?php echo ($result->num_rows > 0) ? ($offset + 1) : 0; ?> to <?php echo min($offset + $limit, $total_records); ?> of <?php echo $total_records; ?> students
              </div>
              <ul class="pagination pagination-sm no-margin pull-right">
                <?php if($page > 1): ?>
                  <li><a href="students.php?page=1" style="color: #046a0f; border-color: #d0e0d0;">&laquo;</a></li>
                  <li><a href="students.php?page=<?php echo $page-1; ?>" style="color: #046a0f; border-color: #d0e0d0;">&lsaquo;</a></li>
                <?php endif; ?>
                
                <?php
                  // Show limited page numbers with current page in center
                  $start_page = max(1, $page - 2);
                  $end_page = min($total_pages, $page + 2);
                  
                  for ($i = $start_page; $i <= $end_page; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo "<li class='$active'><a href='students.php?page=$i' style='" . ($active ? "background-color: #046a0f; border-color: #035a0d; color: #fff;" : "color: #046a0f; border-color: #d0e0d0;") . "'>$i</a></li>";
                  }
                ?>
                
                <?php if($page < $total_pages): ?>
                  <li><a href="students.php?page=<?php echo $page+1; ?>" style="color: #046a0f; border-color: #d0e0d0;">&rsaquo;</a></li>
                  <li><a href="students.php?page=<?php echo $total_pages; ?>" style="color: #046a0f; border-color: #d0e0d0;">&raquo;</a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background-color: #046a0f; color: #fff; border-bottom: none; padding: 15px 20px;">
        <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add New Student</h4>
      </div>
      <form action="student_add.php" method="POST">
        <div class="modal-body" style="background-color: #fff; padding: 20px;">
          <div class="form-group">
            <label for="student_name" style="color: #046a0f; font-weight: 600;">Full Name:</label>
            <input type="text" class="form-control" id="student_name" name="name" required style="border: 1px solid #d0e0d0; border-radius: 4px; padding: 8px 12px;">
          </div>
          <div class="form-group">
            <label for="year_section" style="color: #046a0f; font-weight: 600;">Year & Section:</label>
            <input type="text" class="form-control" id="year_section" name="year_section" placeholder="e.g. 2nd Year - A" required style="border: 1px solid #d0e0d0; border-radius: 4px; padding: 8px 12px;">
          </div>
          <div class="form-group">
            <label for="course" style="color: #046a0f; font-weight: 600;">Course:</label>
            <input type="text" class="form-control" id="course" name="course" placeholder="e.g. Bachelor of Science in Information Technology" required style="border: 1px solid #d0e0d0; border-radius: 4px; padding: 8px 12px;">
          </div>
          <div class="form-group">
            <label for="election" style="color: #046a0f; font-weight: 600;">Election:</label>
            <select class="form-control" id="election" name="election_id" style="border: 1px solid #d0e0d0; border-radius: 4px; padding: 8px 12px;">
              <option value="">-- Select Election (Optional) --</option>
              <?php
                $election_sql = "SELECT id, name FROM elections WHERE status = 1";
                $election_result = $conn->query($election_sql);
                while ($election_row = $election_result->fetch_assoc()) {
                  echo "<option value='" . $election_row['id'] . "'>" . htmlspecialchars($election_row['name']) . "</option>";
                }
              ?>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="background-color: #f0fdf0; border-top: 1px solid #e0f0e0; padding: 15px 20px;">
          <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color: #777; color: #fff; border: none;">Cancel</button>
          <button type="submit" name="add" class="btn btn-success" style="background-color: #046a0f; border-color: #035a0d;">Save Student</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.table {
  border-collapse: separate;
  border-spacing: 0;
}
.table th {
  font-weight: 600;
}
.table tbody tr:hover {
  background-color: #f0fdf0;
}
.table th, .table td {
  padding: 12px 15px;
}
.btn {
  transition: all 0.3s ease;
}
.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.pagination > li > a,
.pagination > li > span {
  padding: 6px 12px;
}
.pagination > .active > a,
.pagination > .active > a:focus,
.pagination > .active > a:hover,
.pagination > .active > span,
.pagination > .active > span:focus,
.pagination > .active > span:hover {
  background-color: #046a0f;
  border-color: #035a0d;
}
.form-control:focus {
  border-color: #046a0f;
  box-shadow: 0 0 0 2px rgba(4, 106, 15, 0.25);
}
.label {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 3px;
  font-weight: 600;
  font-size: 12px;
}
</style>

<?php include 'includes/scripts.php'; ?>
<script>
$(function() {
  // First, check if the DataTable is already initialized and destroy it if so
  if ($.fn.DataTable.isDataTable('#students-table')) {
    $('#students-table').DataTable().destroy();
  }
  
  // Then initialize the DataTable
  $('#students-table').DataTable({
    'responsive': true,
    'autoWidth': false,
    'language': {
      'search': 'Search Students:',
      'lengthMenu': 'Show _MENU_ entries per page',
      'zeroRecords': 'No matching students found',
      'info': 'Showing _START_ to _END_ of _TOTAL_ students',
      'infoEmpty': 'Showing 0 to 0 of 0 students',
      'infoFiltered': '(filtered from _MAX_ total students)'
    },
    'pagingType': 'full_numbers',
    'dom': '<"top"lf>rt<"bottom"ip><"clear">',
    'drawCallback': function() {
      $('.dataTables_paginate > .pagination').addClass('pagination-sm');
    }
  });
});
</script>
</body>
</html>
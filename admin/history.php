<?php
include 'includes/session.php';
include 'includes/header.php';

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for SQL query

// Fetch total number of records
$total_sql = "SELECT COUNT(*) AS total FROM history";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit); // Total number of pages

// Fetch records for the current page
$sql = "SELECT * FROM history ORDER BY deleted_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Elections History</h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Deleted Elections</h3>
            </div>

            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <th>Election Name</th>
                  <th>Deleted At</th>
                  <th>Actions</th>
                </thead>
                <tbody>
                  <?php
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        echo "
                          <tr>
                            <td>" . htmlspecialchars($row['election_title']) . "</td>
                            <td>" . htmlspecialchars($row['deleted_at']) . "</td>
                            <td>
                              <button class='btn btn-success btn-sm view-history' data-id='" . $row['id'] . "'>View Details</button>
                            </td>
                          </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='3' class='text-center'>No history found</td></tr>";
                    }
                  ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <?php
                  for ($i = 1; $i <= $total_pages; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo "
                      <li class='$active'>
                        <a href='history.php?page=$i' class='btn btn-success btn-sm'>$i</a>
                      </li>";
                  }
                ?>
              </ul>
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
$(document).ready(function(){
  $('.view-history').click(function(){
    var id = $(this).data('id');
    $.ajax({
      url: 'view_history.php',
      method: 'POST',
      data: { id: id },
      success: function(response){
        $('#historyModal .modal-body').html(response);
        $('#historyModal').modal('show');
      }
    });
  });
});
</script>

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Deleted Election Details</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <!-- Election history details will be loaded here -->
      </div>
    </div>
  </div>
</div>

</body>
</html>

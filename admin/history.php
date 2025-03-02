<?php
include 'includes/session.php';
include 'includes/header.php';
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Deleted Elections History</h1>
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
                    $sql = "SELECT * FROM history ORDER BY deleted_at DESC";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                      echo "
                        <tr>
                          <td>".$row['election_title']."</td>
                          <td>".$row['deleted_at']."</td>
                          <td>
                            <button class='btn btn-info btn-sm view-history' data-id='".$row['id']."'>View Details</button>
                          </td>
                        </tr>";
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

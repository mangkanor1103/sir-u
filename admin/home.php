<?php
    include 'includes/session.php';

    // Handle form submission to generate codes
    if(isset($_POST['generate'])){
        $election_name = $_POST['election_name'];
        $quantity = intval($_POST['quantity']); // Assuming you have a field in your form to input the quantity of codes

        for ($i = 0; $i < $quantity; $i++) {
            // Generate election code
            $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $election_code = substr(str_shuffle($set), 0, 10);

            $sql = "INSERT INTO elections (name, election_code) VALUES ('$election_name', '$election_code')";
            if($conn->query($sql)){
                $_SESSION['success'] = 'Election code generated successfully for ' . $election_name . '. Code: ' . $election_code;
            }
            else{
                $_SESSION['error'] = $conn->error;
            }
        }
        header('location: sub_admins.php');
        exit(); // Terminate script execution after redirection
    }

    // Handle form submission to clear all elections
    if(isset($_POST['clear'])){
        $sql = "TRUNCATE TABLE elections";
        if($conn->query($sql)){
            $_SESSION['success'] = 'All elections cleared successfully';
        }
        else{
            $_SESSION['error'] = $conn->error;
        }
        header('location: sub_admins.php');
        exit(); // Terminate script execution after redirection
    }
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar1.php'; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Elections List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Elections</li>
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
              <form action="" method="post" id="generateForm">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Election Name:</label>
                      <input type="text" name="election_name" class="form-control" placeholder="Enter election name">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Generate Codes:</label>
                      <input type="number" name="quantity" class="form-control" placeholder="Enter quantity">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="submit" name="generate" class="btn btn-primary" style="margin-top: 25px;">Generate</button>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <button type="button" id="clearAllButton" class="btn btn-danger" style="margin-top: 25px;">Clear All</button>
                    </div>
                  </div>
                </div>
              </form>
              <form action="" method="post" id="clearForm" style="display: none;">
                <input type="hidden" name="clear">
              </form>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Election Name</th>
                  <th>Election Code</th>
                </thead>
                <tbody>
                  <?php
                    // Retrieve elections from the database
                    $sql = "SELECT * FROM elections";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "
                          <tr>
                            <td>".$row['name']."</td>
                            <td>".$row['election_code']."</td>
                          </tr>
                        ";
                      }
                    } else {
                      echo "<tr><td colspan='2'>No elections found</td></tr>";
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
$(function(){
  // Handle Clear All button click
  $('#clearAllButton').on('click', function(e){
    e.preventDefault();
    if(confirm("Are you sure you want to clear all elections?")) {
      $('#clearForm').submit();
    }
  });
});
</script>
</body>
</html>

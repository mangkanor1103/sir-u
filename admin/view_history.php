<?php
include 'includes/session.php';

if(isset($_POST['id'])){
  $id = $_POST['id'];

  // Fetch deleted election details
  $sql = "SELECT * FROM history WHERE id = '$id'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  echo "<div id='printable-content'>";
  echo "<h4>Election Name: ".$row['election_title']."</h4>";
  echo "<p>Deleted At: ".$row['deleted_at']."</p>";

  // Function to format and display deleted data
  function formatDeletedData($data, $columns) {
      if(empty($data)) {
          return "<p>No records available.</p>";
      }
      $records = explode(";", $data);
      echo "<table class='table table-bordered'><thead><tr>";
      foreach ($columns as $col) {
          echo "<th>$col</th>";
      }
      echo "</tr></thead><tbody>";
      foreach ($records as $record) {
          $values = explode("|", $record);
          echo "<tr>";
          foreach ($values as $value) {
              echo "<td>$value</td>";
          }
          echo "</tr>";
      }
      echo "</tbody></table>";
  }

  echo "<h5>Candidates</h5>";
  formatDeletedData($row['candidates'], ['ID', 'Position ID', 'First Name', 'Last Name', 'Photo', 'Platform']);

  echo "<h5>Voters</h5>";
  formatDeletedData($row['voters'], ['ID', 'Voter ID']);

  echo "<h5>Votes</h5>";
  formatDeletedData($row['votes'], ['ID', 'Voter ID', 'Candidate ID', 'Position ID', 'Timestamp']);

  echo "<h5>Positions</h5>";
  formatDeletedData($row['positions'], ['ID', 'Description', 'Max Vote', 'Priority']);

  echo "</div>"; // Close printable-content div

  // Add print button
  echo "<button class='btn btn-success' id='print-button'><i class='fa fa-print'></i> Print</button>";
}
?>

<script>
document.getElementById('print-button').addEventListener('click', function() {
    var printContent = document.getElementById('printable-content').innerHTML;
    var originalContent = document.body.innerHTML;

    document.body.innerHTML = "<h2>Deleted Election Details</h2>" + printContent;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
});
</script>

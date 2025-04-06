<?php
session_start();
include 'conn.php'; // Include the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Elections History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-green-50 text-green-900 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Bar -->
        <nav class="bg-green-700 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <a href="#" class="text-2xl font-bold">Election System</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
            </div>
        </nav>

        <!-- Content -->
        <div class="flex-grow container mx-auto px-4 py-8">
            <h1 class="text-4xl font-bold text-center mb-6">Deleted Elections History</h1>
            <div class="bg-white shadow-md rounded-lg p-6">
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2">Election Name</th>
                            <th class="border border-gray-300 px-4 py-2">Deleted At</th>
                            <th class="border border-gray-300 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch deleted elections from the database
                        $sql = "SELECT * FROM history ORDER BY deleted_at DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "
                                    <tr class='hover:bg-green-100'>
                                        <td class='border border-gray-300 px-4 py-2'>" . htmlspecialchars($row['election_title']) . "</td>
                                        <td class='border border-gray-300 px-4 py-2'>" . htmlspecialchars($row['deleted_at']) . "</td>
                                        <td class='border border-gray-300 px-4 py-2 text-center'>
                                            <button class='bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded view-history' data-id='" . $row['id'] . "'>View Details</button>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "
                                <tr>
                                    <td colspan='3' class='text-center text-gray-500 py-4'>No deleted elections found.</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-green-700 text-white text-center py-4">
            &copy; <?php echo date('Y'); ?> Election System. All rights reserved.
        </footer>
    </div>

    <script>
        $(document).ready(function(){
            // Handle the "View Details" button click
            $('.view-history').click(function(){
                var id = $(this).data('id');
                $.ajax({
                    url: 'view_history.php', // The PHP file to fetch election details
                    method: 'POST',
                    data: { id: id },
                    success: function(response){
                        $('#historyModal .modal-body').html(response);
                        $('#historyModal').removeClass('hidden');
                    },
                    error: function() {
                        alert('An error occurred while fetching the election details.');
                    }
                });
            });

            // Close the modal
            $('#closeModal').click(function(){
                $('#historyModal').addClass('hidden');
            });
        });
    </script>

    <!-- History Modal -->
    <div id="historyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-4xl max-h-screen overflow-y-auto">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Deleted Election Details</h2>
            <div class="modal-body text-gray-700">
                <!-- Election history details will be loaded here dynamically -->
            </div>
            <div class="flex justify-end mt-4">
                <button id="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Close</button>
            </div>
        </div>
    </div>
</body>
</html>

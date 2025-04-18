<?php
include 'includes/session.php';
include 'includes/header.php';

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "votingsystem5";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submissions for adding, updating, and deleting
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $course = $_POST['course'];
        $year_section = $_POST['year_section'];

        $stmt = $connection->prepare("INSERT INTO courses (course, year_section) VALUES (?, ?)");
        $stmt->bind_param("ss", $course, $year_section);
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $course = $_POST['course'];
        $year_section = $_POST['year_section'];

        $stmt = $connection->prepare("UPDATE courses SET course = ?, year_section = ? WHERE id = ?");
        $stmt->bind_param("ssi", $course, $year_section, $id);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $connection->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch all courses
$courses_query = "SELECT * FROM courses";
$courses_result = mysqli_query($connection, $courses_query);
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar1.php'; ?>

    <div class="content-wrapper" style="background-color: #e8f5e9;">
        <section class="content-header text-center">
            <h1 style="color: #2e7d32; font-weight: bold;">Manage Courses</h1>
        </section>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success mb-4" data-toggle="modal" data-target="#addCourseModal">
                            Add Course
                        </button>

                        <table class="table table-bordered" style="background-color: white;">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Year and Section</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($courses_result)): ?>
                                <tr>
                                    <td><?php echo $row['course']; ?></td>
                                    <td><?php echo $row['year_section']; ?></td>
                                    <td>
                                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>">Update</button>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Update Modal -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateModalLabel">Update Course</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="form-group">
                                                        <label>Course</label>
                                                        <input type="text" name="course" class="form-control" value="<?php echo $row['course']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Year and Section</label>
                                                        <input type="text" name="year_section" class="form-control" value="<?php echo $row['year_section']; ?>" required>
                                                    </div>
                                                    <button type="submit" name="update" class="btn btn-success">Update Course</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Course</label>
                            <input type="text" name="course" class="form-control" placeholder="Course Name" required>
                        </div>
                        <div class="form-group">
                            <label>Year and Section</label>
                            <input type="text" name="year_section" class="form-control" placeholder="Year and Section (e.g., 3F1)" required>
                        </div>
                        <button type="submit" name="add" class="btn btn-success">Add Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
include 'includes/session.php';
include 'includes/header.php';
?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar1.php'; ?>

    <div class="content-wrapper" style="background-color: #e8f5e9;">
        <section class="content-header text-center">
            <h1 style="color: #2e7d32; font-weight: bold;">Welcome to the Student Voting System</h1>
            <p style="color: #388e3c; font-size: 18px;">Select an option below to get started</p>
            <p style="color: #388e3c; font-size: 18px;">You can manage courses, view elections, and handle student feedbacks.</p> <!-- Added info -->
        </section>

        <section class="content">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Elections -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <a href="sub_admins.php" class="text-decoration-none">
                            <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                                <span class="info-box-icon" style="font-size: 40px; color: #2e7d32;">
                                    <i class="fa fa-check-square-o"></i>
                                </span>
                                <div class="info-box-content">
                                    <h3 style="color: #1b5e20;">Elections</h3>
                                    <p>View and manage ongoing elections</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Students -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <a href="students.php" class="text-decoration-none">
                            <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                                <span class="info-box-icon" style="font-size: 40px; color: #2e7d32;">
                                    <i class="fa fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <h3 style="color: #1b5e20;">Students</h3>
                                    <p>Manage student accounts and voters</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Courses -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <a href="info.php" class="text-decoration-none"> <!-- Link to the course management page -->
                            <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                                <span class="info-box-icon" style="font-size: 40px; color: #2e7d32;">
                                    <i class="fa fa-book"></i> <!-- Icon for courses -->
                                </span>
                                <div class="info-box-content">
                                    <h3 style="color: #1b5e20;">Courses</h3>
                                    <p>Manage courses, including year and section</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- History -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <a href="history.php" class="text-decoration-none">
                            <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                                <span class="info-box-icon" style="font-size: 40px; color: #2e7d32;">
                                    <i class="fa fa-history"></i>
                                </span>
                                <div class="info-box-content">
                                    <h3 style="color: #1b5e20;">History</h3>
                                    <p>View past election results</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Feedbacks -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <a href="feedback.php" class="text-decoration-none">
                            <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                                <span class="info-box-icon" style="font-size: 40px; color: #2e7d32;">
                                    <i class="fa fa-comments"></i>
                                </span>
                                <div class="info-box-content">
                                    <h3 style="color: #1b5e20;">Feedbacks</h3>
                                    <p>View student feedbacks and suggestions</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<style>
.info-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}
.info-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.text-decoration-none {
    text-decoration: none !important;
}
.info-box-content {
    margin-left: 60px;
}
.info-box-icon {
    position: absolute;
}
</style>

<?php include 'includes/scripts.php'; ?>
</body>
</html>

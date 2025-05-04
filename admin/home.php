<?php
include 'includes/session.php';
include 'includes/header.php';
?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar1.php'; ?>

    <div class="content-wrapper bg-gradient-to-br from-green-50 to-white">
        <section class="content-header text-center py-8">
            <div class="container">
                <h1 class="text-green-800 font-bold text-3xl mb-3">Welcome to the Student Voting System</h1>
                <div class="max-w-3xl mx-auto px-4">
                    <p class="text-green-700 text-lg mb-2">Select an option below to get started</p>
                    <p class="text-green-600 opacity-80">Manage courses, view elections, and handle student feedbacks efficiently.</p>
                </div>
            </div>
        </section>

        <section class="content pb-12">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Elections -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="sub_admins.php" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-check-square-o"></i>
                                </div>
                                <div class="card-content">
                                    <h3>Elections</h3>
                                    <p>View and manage ongoing elections</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Students -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="students.php" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="card-content">
                                    <h3>Students</h3>
                                    <p>Manage student accounts and voters</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Courses -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="info.php" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-book"></i>
                                </div>
                                <div class="card-content">
                                    <h3>Courses</h3>
                                    <p>Manage courses, year and sections</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>

                    <!-- History -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="history.php" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-history"></i>
                                </div>
                                <div class="card-content">
                                    <h3>History</h3>
                                    <p>View past election results</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>

                    <!-- Feedbacks -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="feedback.php" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-comments"></i>
                                </div>
                                <div class="card-content">
                                    <h3>Feedbacks</h3>
                                    <p>View student feedbacks and suggestions</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Settings -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="#" data-toggle="modal" data-target="#config" class="text-decoration-none">
                            <div class="dashboard-card">
                                <div class="card-icon">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <div class="card-content">
                                    <h3>Settings</h3>
                                    <p>Configure system preferences</p>
                                </div>
                                <div class="card-indicator"></div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Stats Section -->
                <div class="stats-section mt-5">
                    <h2 class="stats-title">Quick Statistics</h2>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-user-o"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Total Students</h4>
                                    <div class="stat-value">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM voters";
                                        $query = $conn->query($sql);
                                        $row = $query->fetch_assoc();
                                        echo number_format($row['total']);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-check-circle-o"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Active Elections</h4>
                                    <div class="stat-value">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM elections WHERE status = 'Active'";
                                        $query = $conn->query($sql);
                                        $row = $query->fetch_assoc();
                                        echo number_format($row['total']);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Total Programs</h4>
                                    <div class="stat-value">
                                        <?php
                                        $sql = "SELECT COUNT(*) as total FROM info";
                                        $query = $conn->query($sql);
                                        $row = $query->fetch_assoc();
                                        echo number_format($row['total']);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
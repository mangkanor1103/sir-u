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
            <h1 style="color: #2e7d32; font-weight: bold;">Welcome to the Admin Dashboard</h1>
            <p style="color: #388e3c; font-size: 18px;">Manage sub-admins, monitor elections, and ensure a smooth voting process.</p>
        </section>

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                            <h3 style="color: #1b5e20;">Manage Sub-Admins</h3>
                            <p>View, add, or remove sub-admins who oversee specific elections.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                            <h3 style="color: #1b5e20;">Monitor Elections</h3>
                            <p>Track ongoing and completed elections with real-time updates.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="info-box" style="background: white; border-left: 5px solid #2e7d32; padding: 20px; border-radius: 8px;">
                            <h3 style="color: #1b5e20;">System Settings</h3>
                            <p>Customize the voting system and configure election parameters.</p>
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

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="background-color: #70C237; color: #fff;">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?php echo (!empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $user['firstname'].' '.$user['lastname']; ?></p>
        <a><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MANAGE</li>
      <li class=""><a href="home.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Home</span></a></li> <!-- New menu item -->
      <li class=""><a href="sub_admins.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Elections</span></a></li> <!-- New menu item -->
      <li class=""><a href="feedback.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Feedbacks</span></a></li> <!-- New menu item -->
      <li class=""><a href="history.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>History</span></a></li> <!-- New menu item -->
      <li class=""><a href="students.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Students</span></a></li> <!-- New menu item -->
      <li class=""><a href="info.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Info</span></a></li> <!-- New menu item -->

  </ul>
  </section>
  <!-- /.sidebar -->
</aside>
<?php include 'config_modal.php'; ?>

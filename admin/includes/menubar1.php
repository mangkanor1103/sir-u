<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="background-color:#046a0f; color: #fff; box-shadow: 2px 0 5px rgba(0,0,0,0.2);">
    <!-- Sidebar user panel -->
    <div class="user-panel" style="background-color:#035a0d; padding: 18px 10px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1);">
      <div class="pull-left image">
        <img src="<?php echo (!empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image" style="border: 3px solid #fff; box-shadow: 0 1px 5px rgba(0,0,0,0.3);">
      </div>
      <div class="pull-left info" style="padding-left: 12px;">
        <p style="font-weight: 700; font-size: 15px; margin-bottom: 5px; color: #fff;"><?php echo $user['firstname'].' '.$user['lastname']; ?></p>
        <a style="font-size: 12px; color: #e0f2e3;"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header" style="background-color:#035a0d; color: #fff; text-align: center; font-weight: bold; padding: 12px; letter-spacing: 1.5px; text-transform: uppercase; font-size: 13px;">NAVIGATION</li>
      <li class="sidebar-item">
        <a href="home.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-home" style="width: 22px; margin-right: 10px;"></i> <span>Home</span>
        </a>
      </li>
      <li class="sidebar-item">
        <a href="sub_admins.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-calendar-check-o" style="width: 22px; margin-right: 10px;"></i> <span>Elections</span>
        </a>
      </li>
      <li class="sidebar-item">
        <a href="feedback.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-comments" style="width: 22px; margin-right: 10px;"></i> <span>Feedbacks</span>
        </a>
      </li>
      <li class="sidebar-item">
        <a href="history.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-history" style="width: 22px; margin-right: 10px;"></i> <span>History</span>
        </a>
      </li>
      <li class="sidebar-item">
        <a href="students.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-users" style="width: 22px; margin-right: 10px;"></i> <span>Students</span>
        </a>
      </li>
      <li class="sidebar-item">
        <a href="info.php" style="color: #fff; border-left: 4px solid transparent; padding: 14px; transition: all 0.3s;">
          <i class="fa fa-book" style="width: 22px; margin-right: 10px;"></i> <span>Programs</span>
        </a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>

<style>
.sidebar-item a:hover {
  background-color: rgba(255,255,255,0.15) !important;
  border-left-color: #fff !important;
}
.sidebar-menu > li.active > a {
  background-color: rgba(255,255,255,0.2) !important;
  border-left-color: #fff !important;
  font-weight: bold;
}
.sidebar-item a:hover i, .sidebar-menu > li.active > a i {
  color: #e0f2e3;
}
</style>

<?php include 'config_modal.php'; ?>

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="background-color:rgb(40, 158, 46); color: #fff;">
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
      <li class="">
        <a href="home.php" style="color: #fff;">
          <i class="fa fa-home" style="width: 20px;"></i> <span>Home</span>
        </a>
      </li>
      <li class="">
        <a href="sub_admins.php" style="color: #fff;">
          <i class="fa fa-calendar-check-o" style="width: 20px;"></i> <span>Elections</span>
        </a>
      </li>
      <li class="">
        <a href="feedback.php" style="color: #fff;">
          <i class="fa fa-comments" style="width: 20px;"></i> <span>Feedbacks</span>
        </a>
      </li>
      <li class="">
        <a href="history.php" style="color: #fff;">
          <i class="fa fa-history" style="width: 20px;"></i> <span>History</span>
        </a>
      </li>
      <li class="">
        <a href="students.php" style="color: #fff;">
          <i class="fa fa-users" style="width: 20px;"></i> <span>Students</span>
        </a>
      </li>
      <li class="">
        <a href="info.php" style="color: #fff;">
          <i class="fa fa-book" style="width: 20px;"></i> <span>Programs</span>
        </a>
      </li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
<?php include 'config_modal.php'; ?>

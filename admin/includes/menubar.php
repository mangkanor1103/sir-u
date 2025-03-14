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
      <li class="header">REPORTS</li>
      <li class=""><a href="home.php" style="color: #fff;"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li class=""><a href="votes.php" style="color: #fff;"><span class="glyphicon glyphicon-lock"></span> <span>Votes</span></a></li>
      <li class="header">MANAGE</li>
      <li class=""><a href="voters.php" style="color: #fff;"><i class="fa fa-users"></i> <span>Voters</span></a></li>
      <li class=""><a href="positions.php" style="color: #fff;"><i class="fa fa-tasks"></i> <span>Positions</span></a></li>
      <li class=""><a href="candidates.php" style="color: #fff;"><i class="fa fa-black-tie"></i> <span>Candidates</span></a></li>
      <li class=""><a href="sub_admins.php" style="color: #fff;"><i class="fa fa-user-secret"></i> <span>Sub Admins</span></a></li> <!-- New menu item -->
      <li class="header">SETTINGS</li>
      <li class=""><a href="ballot.php" style="color: #fff;"><i class="fa fa-file-text"></i> <span>Ballot Position</span></a></li>
      <li class=""><a href="#config" data-toggle="modal" style="color: #fff;"><i class="fa fa-cog"></i> <span>Election Title</span></a></li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
<?php include 'config_modal.php'; ?>

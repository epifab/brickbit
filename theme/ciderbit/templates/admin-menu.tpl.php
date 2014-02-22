<?php if (!empty($adminMenu)): ?>
  <div id="admin-menu" class="navbar navbar-inverse">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="brand" href="#"><?php echo $website['title']; ?></a>
        <div class="nav-collapse collapse">
          <ul class="nav">
            <?php foreach ($adminMenu as $item): ?>
            <li><?php $this->api->open('link', $item); 
              ?><?php echo $item['title']; ?><?php echo $this->api->close(); ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div><!--/.nav-collapse -->
        
        <div id="header-sidebar-login">
          <?php if ($user->anonymous): ?>
            <?php $this->api->open('link', array(
              'url' => 'user/login',
              'okButtonLabel' => $this->api->t("Login"),
              'width' => 300,
              'showResponse' => false
            )); ?>login<?php $this->api->close(); ?>
          <?php else: ?>
          <?php endif; ?>
        </div>
        
        <div class="pull-right">
          <ul class="nav pull-right">
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->api->t('Hi, @name', array('@name' => $user->full_name)); ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                  <?php $this->api->open('link', array('url' => 'user/preferences')); ?>
                    <i class="icon-cog"></i> <?php echo $this->api->t('Preferences'); ?>
                  <?php echo $this->api->close(); ?>
                <li><a href="/help/support"><i class="icon-envelope"></i>  <?php echo $this->api->t('Contact support'); ?></a></li>
                <li class="divider"></li>
                <li>
                  <?php $this->api->open('link', array(
                    'url' => 'user/logout',
                    'showResponse' => false
                  )); ?><i class="icon-off"></i> <?php echo $this->api->t('Logout'); ?><?php $this->api->close(); ?>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
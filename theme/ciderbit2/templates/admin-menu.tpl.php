<div id="admin-menu" class="navbar navbar-static-top" role="navigation">
  <div class="container">
    <div class= "navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo $this->api->path(''); ?>"><?php echo $website['title']; ?></a>
    </div>
    <div class="navbar-collapse collapse">
      <?php if (empty($user) || $user->anonymous): ?>
        <form id="login-header" class="navbar-form navbar-right" role="form" action="<?php echo $this->api->path('user/login'); ?>" method="post">
          <input type="hidden" name="login_form" value="1" />
          <div class="form-group">
            <input type="text" placeholder="Email" class="form-control" name="login[name]"/>
          </div>
          <div class="form-group">  
            <input type="password" placeholder="Password" class="form-control" name="login[pass]"/>
          </div>
          <button type="submit" class="btn btn-success" name="login[submit]">Sign in</button>
        </form>
      <?php else: ?>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <?php foreach ($adminMenu as $item): ?>
              <?php if (!empty($item['items'])): ?>
                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $item['title']; ?> <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <?php foreach ($item['items'] as $i): ?>
                      <li><?php $this->api->open('link', $i); ?><?php echo $i['title']; ?><?php echo $this->api->close(); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </li>
              <?php else: ?>
              <li><?php $this->api->open('link', $item + array('width' => 600)); 
                ?><?php echo $item['title']; ?><?php echo $this->api->close(); ?>
              </li>
              <?php endif; ?>
            <?php endforeach; ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->api->t('Hi, @name', array('@name' => $this->api->userName($user->id))); ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                  <?php $this->api->open('link', array('url' => 'user/preferences', 'ajax' => false)); ?>
                    <span class="glyphicon glyphicon-cog"></span> <?php echo $this->api->t('Preferences'); ?>
                  <?php echo $this->api->close(); ?>
                </li>
                <li>
                  <?php $this->api->open('link', array('url' => 'user/edit', 'ajax' => false)); ?>
                    <span class="glyphicon glyphicon-pencil"></span> <?php echo $this->api->t('Edir profile'); ?>
                  <?php echo $this->api->close(); ?>
                </li>
                <li class="divider"></li>
                <li>
                  <?php $this->api->open('link', array(
                    'url' => 'user/logout',
                    'showResponse' => false
                  )); ?><span class="glyphicon glyphicon-off"></span> <?php echo $this->api->t('Logout'); ?><?php $this->api->close(); ?>
                </li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->

      <?php endif; ?>
    </div><!--/.navbar-collapse -->
  </div><!--/.container -->
</div><!--/.navbar -->
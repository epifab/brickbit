<!DOCTYPE HTML>
<html lang="<?php echo $system['lang']; ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico">
    <meta name="description" content="<?php echo isset($page['description']) ? $page['description'] : ''; ?>">
    <meta name="author" content="<?php echo isset($page['author']) ? $page['author'] : ''; ?>">
    <title><?php echo $page['title']; ?></title>
    
    <?php foreach ($page['css'] as $css): ?>
    <link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"/>
    <?php endforeach; ?>
    
    <?php foreach ($page['js'] as $js): ?>
    <script type="text/javascript" src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<!--[if lte IE 9]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.js"></script>
<![endif]--> 
  </head>
  
  <body class="<?php echo (isset($page['bodyClass']) ? $page['bodyClass'] : ''); ?>">

    <?php $this->api->loadBlock('admin-menu-wrapper', 'system/block/admin-menu'); ?>
    <div id="header-wrapper">
      <header class="container" role="main">
        <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="header">
          <h1>
            <?php $this->api->open('link', array(
              'url' => '',
              'ajax' => false
            )); ?><img src="<?php echo $this->api->themePath('img/layout/ciderbit.png'); ?>" alt="<?php echo $website['title']; ?>"/><?php echo $this->api->close(); ?>
            <div class="hide"><?php echo $website['title']; ?></div>
          </h1>
        </div>
        <?php $this->api->region('header-sidebar'); ?>
      </header>
    </div>
    
    <div id="main-menu-wrapper">
      <?php $this->api->loadBlock('main-menu-wrapper', 'system/block/main-menu'); ?>
    </div>

    <div id="main-wrapper">
      <div id="main" class="container">
        <div class="messages">
          <?php if (count(\system\Main::countMessages())): ?>
            <div class="messages">
              <?php while ($message = \system\Main::popMessage()): ?>
                <div class="alert alert-<?php echo $message['class']; ?>"><?php echo $message['message']; ?></div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>
        <div id="columns-wrapper">
          <?php $this->api->import($website['outlineLayoutTemplate']); ?>
        </div>
      </div>
    </div>

    <div id="footer-wrapper">
      <?php $this->api->region('footer'); ?>
    </div>
    
    <?php if ($this->api->access('/admin/logs')): ?>
    <?php $this->api->open('block', array('url' => '/admin/logs', 'name' => 'logs')); ?>
    <?php endif; ?>
    
    <script type="text/javascript"><?php $this->api->jss(); ?></script>
  </body>
</html>
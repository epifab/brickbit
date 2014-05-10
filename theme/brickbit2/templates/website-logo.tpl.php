<h1 id="header-title">
  <?php $this->api->open('link', array(
    'url' => $this->api->vpath(),
    'ajax' => false
  )); ?><img src="<?php echo $this->api->themePath('img/header-logo.png'); ?>" alt="<?php echo $website['title']; ?>"/>
  <?php echo $this->api->close(); ?>
</h1>
<?php // <h2 id="header-subtitle"><span><{$website.subtitle </span></h2> ?>

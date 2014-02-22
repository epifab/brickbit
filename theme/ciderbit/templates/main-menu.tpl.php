<?php if (!empty($mainMenu)): ?>
<div id="main-menu" class="navbar">
  <div class="navbar-inner">
    <div class="container">
      <ul class="nav" id="main-menu-list">
        <?php foreach ($mainMenu as $menuItem): ?>
          <li <?php if ($page['url'] == $menuItem['url']): ?>class="selected" <?php endif; ?>id="item-<?php echo $menuItem['id']; ?>">
            <?php $this->api->open('link', array(
              'ajax' => false,
              'url' => $menuItem['url']
            )); ?><?php echo $menuItem['title']; ?><?php echo $this->api->close(); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<?php endif; ?>
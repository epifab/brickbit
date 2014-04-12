<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-globe"></span> <?php echo $system['langs'][$system['lang']]; ?>  <b class="caret"></b></a>
  <ul class="dropdown-menu">
    <?php foreach ($system['langs'] as $lang => $langDesc): ?>
      <li><a href="<?php echo $this->api->langUrl($lang); ?>"><?php echo $langDesc; ?></a></li>
    <?php endforeach; ?>
  </ul>
</li>
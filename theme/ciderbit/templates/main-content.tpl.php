<?php $this->api->open('block', array('url' => $system['mainComponent']['url'], 'name' => 'main-column')); ?>
  <?php $this->api->import($system['templates']['main']); ?>
<?php $this->api->close(); // block ?>
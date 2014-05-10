<?php $this->api->open('block', array('url' => $system['mainComponent']['url'], 'name' => 'main-column')); ?>
  <?php if ($system['templates']['main']): ?>
    <?php $this->api->import($system['templates']['main']); ?>
  <?php endif; ?>
<?php $this->api->close(); // block ?>
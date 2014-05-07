<ul class="nav nav-tabs">
  <li class="active"><a href="#edit-node-content-wrapper" data-toggle="tab"><?php echo $this->api->t('Content'); ?></a></li>
  <li><a href="#edit-node-resources-wrapper" data-toggle="tab"><?php echo $this->api->t('Resources'); ?></a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="edit-node-content-wrapper">
    <?php $this->api->import('edit-node--content', array('node' => $form->getRecordset('node'))); ?>
  </div>
  <div class="tab-pane" id="edit-node-resources-wrapper">
    <?php $this->api->import('edit-node--resources', array('node' => $form->getRecordset('node'))); ?>
  </div>
</div>
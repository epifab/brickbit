<?php if ($this->api->access($node->edit_url)): ?>
  <div class="edit-controls edit-controls-<?php echo isset($class) ? $class : ''; ?> btn-toolbar">
    <div class="btn-group">
      <?php $this->api->open('link', array(
        'ajax' => false,
        'url' => $node->edit_url,
        'class' => 'btn btn-default',
      )); ?><span class="glyphicon glyphicon-pencil"></span> <?php echo $this->api->t('Edit @name', array('@name' => $node->type)); ?><?php echo $this->api->close(); ?>

      <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
          <span class="glyphicon glyphicon-file"></span>
          <?php echo $this->api->t('Add'); ?>
          <span class="caret"></span>
        </button>
        <?php if (count($node->valid_children_types)): ?>
          <ul class="dropdown-menu">
            <?php foreach ($node->valid_children_types as $type): ?>
              <li>
                <?php $this->api->open('link', array(
                  'ajax' => false,
                  'url' => $this->api->vpath('content/' . $node->id . '/add/' . $type),
                )); ?><?php echo $this->api->t('Add @type', array('@type' => $type)); ?><?php echo $this->api->close(); ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <?php $this->api->open('link', array(
        'url' => $node->delete_url,
        'confirm' => true,
        'class' => 'btn btn-danger',
        'confirmTitle' => $this->api->t('The @name will be deleted', array('@name' => $node->type)),
        'title' => $this->api->t('Delete @name', array('@name' => $node->type))
      )); ?><span class="glyphicon glyphicon-trash"></span> Delete <?php echo $node->type; ?><?php echo $this->api->close(); ?>
    </div>
  </div>
<?php endif; ?>
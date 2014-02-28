  <?php if ($node): ?>
  <div class="node node-<?php print $node->type; ?> node-<?php print $node->id; ?><?php echo $this->api->access($node->edit_url) ? ' node-admin' : ''; ?>">

    <?php if ($this->api->access($node->edit_url)): ?>
      <div class="edit-controls btn-toolbar">
        <div class="btn-group">
          <?php $this->api->open('link', array(
            'ajax' => false,
            'url' => $node->edit_url,
            'class' => 'btn btn-primary',
          )); ?><span class="glyphicon glyphicon-pencil"></span> Edit <?php echo $this->api->t('Edit @name', array('@name' => $node->type)); ?><?php echo $this->api->close(); ?>
          
          <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
              <span class="glyphicon glyphicon-file"></span>
              <?php echo $this->api->t('Add'); ?>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
              <?php foreach ($node->valid_children_types as $type): ?>
                <li>
                  <?php $this->api->open('link', array(
                    'ajax' => false,
                    'url' => 'content/' . $node->id . '/add/' . $type,
                  )); ?><?php echo $this->api->t('Add @type', array('@type' => $type)); ?><?php echo $this->api->close(); ?>
                </li>
              <?php endforeach; ?>
            </ul>
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

    <?php if ($node->text): ?>
      <div class="node-text" lang="<?php print $node->text->lang; ?>">
        <?php if ($node->text->title): ?>
          <h1 class="node-text-title"><?php print $node->text->title; ?></h1>
        <?php endif; ?>
        <?php if ($node->text->subtitle): ?>
          <h2 class="node-text-subtitle"><?php print $node->text->subtitle; ?></h1>
        <?php endif; ?>
        <?php if ($node->text->preview): ?>
          <div class="node-text-preview"><?php print $node->text->preview; ?></div>
        <?php endif; ?>
        <?php if ($node->text->body): ?>
          <div class="node-text-body"><?php print $node->text->body; ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (count($node->files)): ?>

    <?php endif; ?>

    <?php if (count($node->children_recursive)): ?>
      <div class="node-children">
      <?php foreach ($node->children_recursive as $child): ?>
        <?php $this->api->displayNode($child, 'default'); ?>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php if ($node): ?>
  <div class="node node-<?php print $node->type; ?> node-<?php print $node->id; ?><?php echo $this->api->access($node->edit_url) ? ' node-admin' : ''; ?>">

    <?php $this->api->import('node-edit-controls', array('node' => $node, 'class' => 'top')); ?>

    <?php if ($node->text): ?>
      <div class="node-text" lang="<?php print $node->text->lang; ?>">
        <?php if ($node->text->title): ?>
          <h1 class="node-text-title"><a href="<?php echo $node->url; ?>"><?php print $node->text->title; ?></a></h1>
        <?php endif; ?>
        <?php if ($node->text->subtitle): ?>
          <h3 class="node-text-subtitle"><?php print $node->text->subtitle; ?></h3>
        <?php endif; ?>
        <?php if ($node->text->preview): ?>
          <div class="node-text-preview"><?php print $node->text->preview; ?></div>
        <?php endif; ?>
        <?php if ($node->text->body): ?>
          <div class="node-text-body"><?php print $node->text->body; ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (count($node->children_recursive)): ?>
      <div class="node-children">
      <?php foreach ($node->children_recursive as $child): ?>
        <?php $this->api->displayNode($child, 'default'); ?>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php $this->api->import('node-edit-controls', array('node' => $node, 'class' => 'bottom')); ?>
  </div>
<?php endif; ?>
<?php if ($node): ?>
<div class="node node-<?php print $node->type; ?> node-<?php print $node->id; ?>">
	<?php if ($this->api->access($node->edit_url)): ?>
		<div class="page-controls">
			<?php $this->api->open('link', array(
				'url' => $node->edit_url,
				'width' => 800,
				'height' => 420,
				'title' => $this->api->t('Edit @name', array('@name' => $node->type))
			)); ?><?php $this->api->t('Edit @name', array('@name' => $node->type)); ?><?php $this->api->close(); ?>
			<?php $this->api->open('link', array(
				'url' => $node->delete_url,
				'confirm' => true,
				'confirmTitle' => $this->api->t('The @name will be deleted', array('@name' => $node->type)),
				'title' => $this->api->t('Delete @name', array('@name' => $node->type))
			)); ?><?php $this->api->t('Delete @name', array('@name' => $node->type)); ?><?php $this->api->close(); ?>
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

	<?php if (count($node->children)): ?>
		<div class="node-children">
		<?php foreach ($node->children as $child): ?>
			<?php $this->api->render('content', $child); ?>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php endif; ?>
<ul id="main-menu">
	<?php foreach ($mainMenu as $menuItem): ?>
		<li <?php if ($page['url'] == $menuItem['url']): ?>class="selected" <?php endif; ?>id="item-<?php echo $menuItem['id']; ?>">
			<?php $this->api->open('link', array(
				'ajax' => false,
				'url' => $menuItem['url']
			)); ?><?php echo $menuItem['title']; ?><?php echo $this->api->close(); ?>
		</li>
	<?php endforeach; ?>
</ul>
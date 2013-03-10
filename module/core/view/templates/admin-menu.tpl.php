<?php if (!empty($adminMenu)): ?>
	<ul id="admin-menu">
		<?php foreach ($adminMenu as $item): ?>
		<li><?php $this->api->open('link', $item); 
			?><?php echo $item['title']; ?><?php echo $this->api->close(); ?>
		</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
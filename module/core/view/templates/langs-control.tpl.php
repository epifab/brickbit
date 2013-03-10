<div id="header-sidebar-langs">
	<?php foreach ($system['langs'] as $lang): ?>
		<?php if ($lang != $system['lang']): ?>
		<a href="<?php echo $this->api->lang_path($lang); ?>">
			<img alt="<?php echo $lang; ?>" src="<?php echo $this->api->theme_path('img/lang/40/' . $lang . '.jpg'); ?>"/>
		</a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
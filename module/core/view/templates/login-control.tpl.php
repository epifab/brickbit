<div id="header-sidebar-login">
	<?php if ($user->anonymous): ?>
		<li><?php $this->api->open('link', array(
				'url' => 'user/login',
				'okButtonLabel' => $this->api->t("Login"),
				'width' => 300,
				'showResponse' => false
			)); ?><img src="<?php echo $this->api->theme_path("img/login.jpg"); ?>" alt="Login"/>
			<?php echo $this->api->close(); ?>
		</li>
	<?php else: ?>
		<li>
			<?php $this->api->open('link', array(
				'url' => 'user/logout',
				'okButtonLabel' => $this->api->t('Logout'),
				'width' => 300,
				'showResponse' => false
			)); ?><img src="<?php echo $this->api->theme_path('img/logout.jpg'); ?>" alt="Logout"/>
			<?php echo $this->api->close(); ?>
		</li>
	<?php endif; ?>
</div>
<div id="header-sidebar-login">
	<?php if ($user->anonymous): ?>
		<?php $this->api->open('link', array(
			'url' => 'user/login',
			'okButtonLabel' => $this->api->t("Login"),
			'width' => 300,
			'showResponse' => false
		)); ?>login<?php $this->api->close(); ?>
	<?php else: ?>
		<?php $this->api->open('link', array(
			'url' => 'user/logout',
			'okButtonLabel' => $this->api->t('Logout'),
			'width' => 300,
			'showResponse' => false
		)); ?>logout<?php $this->api->close(); ?>
	<?php endif; ?>
</div>
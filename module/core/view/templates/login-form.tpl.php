<?php $this->api->open('panel', array('name' => 'main')); ?>
	<form id="<?php echo $this->api->edit_form_id(); ?>" class="login" method="post" action="<?php echo $system['component']['url']; ?>">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username"><?php echo $this->api->t('Email'); ?></label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass"><?php echo $this->api->t('Password'); ?></label><br/><input type="password" id="userpass" name="userpass"/></div>
		<?php if (isset($errorMessage)): ?>
			<p class="alert"><?php echo $errorMessage; ?></p>
		<?php endif; ?>
	</form>
<?php echo $this->api->close(); ?>
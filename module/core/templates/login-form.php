<?php $this->api->open('panel', array('name' => 'main')); ?>
	<form id="<?php echo $this->api->edit_form_id; ?>" class="login" method="post" action="<?php print $system['component']['url']; ?>">
		<input type="hidden" name="login_form" value="1"/>
		<div><label for="username"><?php print $this->api->t('Email'); ?></label><br/><input type="text" id="username" name="username"/></div>
		<div><label for="userpass"><?php print $this->api->t('Password'); ?></label><br/><input type="password" id="userpass" name="userpass"/></div>
		<?php if (isset($errorMessage)): ?>
			<p class="alert"><?php print $errorMessage; ?></p>
		<?php endif; ?>
	</form>
<?php $this->api->close(); ?>
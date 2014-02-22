<form id="<?php echo $this->api->getEditFormId(); ?>" class="login" method="post" action="<?php echo $system['component']['url']; ?>">
  <input type="hidden" name="login_form" value="1"/>
  
  <div id="field-login-name-wrapper" class="field-wrapper">
    <label for="login-name"><?php echo $this->api->t('Email'); ?></label>
    <input type="text" id="login-name" name="login[name]"/>
  </div>
  <div id="field-login-pass-wrapper" class="field-wrapper">
    <label for="login-pass"><?php echo $this->api->t('Password'); ?></label>
    <input type="password" id="login-pass" name="login[pass]"/>
  </div>
  <div id="field-login-remember-wrapper" class="field-wrapper">
    <input type="checkbox" id="login-remember" name="login[remember]" value="1"/>
    <label for="login-remember"><?php echo $this->api->t('Remember me'); ?></label>
  </div>
  <div id="controls-login-wrapper" class="controls-wrapper">
    <input type="submit" id="login-submit" name="login[submit]" value="login"/>
  </div>
  <?php if (isset($errorMessage)): ?>
    <p class="alert"><?php echo $errorMessage; ?></p>
  <?php endif; ?>
</form>
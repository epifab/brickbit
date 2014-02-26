<div class="container form-signin-wrapper">
  <form class="form-signin" role="form" method="post">
    <input type="hidden" name="login_form" value="1"/>
    <h2 class="form-signin-heading"><?php echo $this->api->t('Sign in'); ?></h2>
    <input name="login[name]" type="email" class="form-control" placeholder="<?php echo $this->api->t('Email address'); ?>" required="required" autofocus="autofocus" />
    <input name="login[pass]" type="password" class="form-control" placeholder="<?php echo $this->api->t('Password'); ?>" required="required" />
    <label class="checkbox">
      <input name="login[remember]" type="checkbox" value="remember-me"> <?php echo $this->api->t('Remember me'); ?>
    </label>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  </form>
</div>

<div class="alert alert-danger">
  <h2>Oops!</h2>
  <h4>Something went wrong...</h4>
  <h4><?php echo $error['exception']->getMessage(); ?></h4>
</div>

<?php if (isset($user) && $user->superuser): ?>
  <div class="alert alert-warning">
    <h3>Debug info</h3>
    <div><?php echo $error['body']; ?></div>
    <div><?php echo $error['trace']; ?></div>
  </div>
<?php endif; ?>
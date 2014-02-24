<div class="alert alert-danger">
  <h2>Oops!</h2>
  <h3><?php echo $error['exception']->getMessage(); ?></h3>
</div>

<div class="alert alert-warning">
  <h3><span class="glyphicon glyphicon-exclamation-sign"></span> Debug info</h3>
  <div><?php echo $error['trace']; ?></div>
  <div><pre><?php // echo $error['debug']; ?></pre></div>
</div>
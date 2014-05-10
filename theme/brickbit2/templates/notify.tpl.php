<div class="alert alert-<?php if ($system['responseType'] == 'ERROR'): ?>danger<?php else: ?>success<?php endif; ?>">
  <?php if (isset($message['title'])): ?>
    <h2><?php print $message['title']; ?></h2>
  <?php endif; ?>
  <?php if (isset($message['body'])): ?>
    <p><?php print $message['body']; ?></p>
  <?php endif; ?>
</div>
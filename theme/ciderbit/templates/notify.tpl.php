<div class="alert alert-<?php if ($system['responseType'] == 'ERROR'): ?>danger<?php else: ?>success<?php endif; ?>">
  <h2><?php print $message['title']; ?></h2>
  <?php print $message['body']; ?>
</div>
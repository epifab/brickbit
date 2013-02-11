<div class="notify <?php if ($system['responseType'] == 'ERROR'): ?>error<?php else: ?>success<?php endif; ?>">
	<h2><?php print $message['title']; ?></h2>
	<?php print $message['body']; ?>
</div>
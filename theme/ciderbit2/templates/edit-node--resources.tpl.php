<?php if (count($form->getSetting('resources'))): ?>
  <h3>Resources</h3>
  <?php foreach ($form->getSetting('resources') as $fileKey => $info): ?>
    <?php $this->api->import('file-upload-form', array(
      'uploaderId' => $fileKey,
      'uploaderUrl' => $info['uploadUrl'],
      'uploaderFileListUrl' => $info['fileListUrl'],
    )); ?>
  <?php endforeach; ?>

  <?php $this->api->import('file-upload-scripts'); ?>
<?php endif; ?>
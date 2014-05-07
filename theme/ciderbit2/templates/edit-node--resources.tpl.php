<?php if (count($form->getRecordset('node')->valid_file_keys)): ?>
  <h3>Resources</h3>
  <?php foreach ($form->getRecordset('node')->valid_file_keys as $fileKey): ?>
    <?php $this->api->import('file-upload-form', array(
      'uploaderId' => $fileKey,
      'uploaderUrl' => $this->api->vpath('content/' . $form->getRecordset('node')->id . '/file/' . $fileKey . '/upload'),
      'uploaderFileListUrl' => $this->api->vpath('content/' . $form->getRecordset('node')->id . '/file/' . $fileKey),
    )); ?>
  <?php endforeach; ?>

  <?php $this->api->import('file-upload-scripts'); ?>
<?php endif; ?>
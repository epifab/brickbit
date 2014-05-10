<?php $this->api->open('form', array('id' => $form->getId())); ?>

  <div class="dataedit">
    <!-- Languages -->
    <fieldset>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'node_file',
          'path' => 'virtual_name',
          'display' => 'columns',
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'node_file',
          'path' => 'download_mode',
          'display' => 'columns',
        )); ?>
      </div>
    </fieldset>
    <!-- /Languages -->

    <?php if (!$system['ajax']): ?>
      <div class="de-controls">
        <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
      </div>
    <?php endif; ?>
  </div>
<?php $this->api->close(); ?>

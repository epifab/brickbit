<?php $this->api->open('form', array('id' => $form->getId())); ?>
  <div class="dataedit">
    <fieldset>
      <legend>
        <div class="legend">
          General info
        </div>
      </legend>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'user',
          'path' => 'full_name',
          'display' => 'columns'
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'user',
          'path' => 'email',
          'display' => 'columns'
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'widget' => 'password',
          'name' => 'password',
          'id' => 'edit-user-password',
          'state' => '',
          'label' => $this->api->t('Password'),
          'display' => 'columns'
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'widget' => 'password',
          'name' => 'password',
          'id' => 'edit-user-password2',
          'state' => '',
          'label' => $this->api->t('Password confirm'),
          'display' => 'columns'
        )); ?>
      </div>
    </fieldset>

    <?php if (!$system['ajax']): ?>
      <div class="de-controls">
        <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
      </div>
    <?php endif; ?>
  </div>
<?php $this->api->close(); ?>
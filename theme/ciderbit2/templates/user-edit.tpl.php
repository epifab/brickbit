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
          'columns' => true
        )); ?>
      </div>
      
      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'user',
          'path' => 'email',
          'columns' => true
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'widget' => 'password',
          'name' => 'password',
          'id' => 'edit-user-password',
          'state' => '',
          'label' => $this->api->t('Password'),
          'columns' => true
        )); ?>
      </div>

      <div class="row de-row">
        <?php echo $this->api->input(array(
          'widget' => 'password',
          'name' => 'password',
          'id' => 'edit-user-password2',
          'state' => '',
          'label' => $this->api->t('Password confirm'),
          'columns' => true
        )); ?>
      </div>
    </fieldset>
    
    <div class="de-controls">
      <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
    </div>
  </div>
<?php $this->api->close(); ?>
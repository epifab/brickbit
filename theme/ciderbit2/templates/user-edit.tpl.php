<?php $this->api->open('form', array('id' => $form->getId())); ?>
  <div class="dataedit">
    <fieldset>
      <legend>
        <div class="legend">
          General info
        </div>
      </legend>
      <div class="row de-row">
        <div class="col-sm-2 de-label-wrapper">
          <?php echo $this->api->formRSInputLabel(array(
            'recordset' => 'user',
            'path' => 'full_name',
            'label' => $this->api->t('Full name'),
          )); ?>
        </div>
        <div class="col-sm-10 de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'user',
            'path' => 'full_name',
            'attributes' => array('class' => 'form-control'),
            'value' => isset($form->getRecordset('user')->password)
          )); ?>
          <?php echo $this->api->formRSInputError(array('recordset' => 'user', 'path' => 'full_name')); ?>
        </div>
      </div>
      <div class="row de-row">
        <div class="col-sm-2 de-label-wrapper">
          <?php echo $this->api->formRSInputLabel(array(
            'recordset' => 'user',
            'path' => 'email',
            'label' => $this->api->t('Email'),
          )); ?>
        </div>
        <div class="col-sm-10 de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'user',
            'path' => 'email',
            'attributes' => array('class' => 'form-control'),
            'value' => isset($form->getRecordset('user')->email)
          )); ?>
        </div>
        <?php echo $this->api->formRSInputError(array('recordset' => 'user', 'path' => 'email')); ?>
      </div>
      <div class="row de-row">
        <div class="col-sm-2 de-label-wrapper">
          <?php echo $this->api->formInputLabel(array(
            'id' => 'edit-user-password',
            'label' => $this->api->t('Password'),
          )); ?>
        </div>
        <div class="col-sm-10 de-input-wrapper">
          <?php echo $this->api->formInput(array(
            'widget' => 'password',
            'name' => 'password',
            'id' => 'edit-user-password',
            'value' => '',
            'attributes' => array('class' => 'form-control'),
          )); ?>
          <?php echo $this->api->formInputError(array('name' => 'password')); ?>
        </div>
      </div>
      <div class="row de-row">
        <div class="col-sm-2 de-label-wrapper">
          <?php echo $this->api->formInputLabel(array(
            'id' => 'edit-user-password-confirm',
            'label' => $this->api->t('Password confirm'),
          )); ?>
        </div>
        <div class="col-sm-10 de-input-wrapper">
          <?php echo $this->api->formInput(array(
            'widget' => 'password',
            'name' => 'password2',
            'id' => 'edit-user-password2',
            'value' => '',
            'attributes' => array('class' => 'form-control'),
          )); ?>
          <?php echo $this->api->formInputError(array('name' => 'password2')); ?>
        </div>
      </div>
    </fieldset>
    
    <div class="de-controls">
      <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
    </div>
  </div>
<?php $this->api->close(); ?>
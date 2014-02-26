<?php $this->api->open('form', array('id' => $form->getId())); ?>
  <div class="dataedit">
    <fieldset>
      <legend>
        <div class="legend">
          <div>Languages</div>
          <div>
            <?php foreach ($system['langs'] as $lang): ?>
            <a href="#" id="node-lang-<?php echo $lang; ?>" class="node-lang-control show-hide-class<?php if ($lang == $website['defaultLang']): ?> expanded<?php endif; ?>"><img src="<?php echo $this->api->themePath('img/lang/40/' . $lang . '.jpg'); ?>"/></a>
            <?php endforeach; ?>
          </div>
        </div>
      </legend>
      <?php foreach ($form->getRecordset('node')->texts as $lang => $text): ?>
        <div class="node-lang node-lang-<?php echo $lang; ?>">
          <div class="row de-row">
            <div class="col-md-2"></div>
            <div class="col-md-10 de-input-wrapper">
              <?php echo $this->api->formInput(array(
                'recordset' => 'node',
                'widget' => 'checkbox',
                'name' => 'text_' . $lang . '.enable',
                'id' => 'edit-node-text_' . $lang . '-enable',
                'label' => $this->api->t('Enable for this language'),
                'attributes' => array('class' => 'xxl form-control richtext wysiwyg'),
                'value' => isset($form->getRecordset('node')->texts[$lang])
              )); ?>
            </div>
          </div>
        </div>
        <div class="node-lang-<?php echo $lang; ?>">
          <div class="row de-row">
            <div class="col-md-2 col-sm-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'urn',
                'label' => $this->api->t('URN'),
              )); ?>
            </div>
            <div class="col-md-10 col-sm-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'urn',
                'widget' => 'textbox', 
                'attributes' => array('class' => 'xl form-control')
              )); ?>

              <div class="de-info alert alert-info">
                <p>
                  <?php echo $this->api->t("Once you choose a URN you shouldn't change it anymore."); ?><br/>
                  <?php echo $this->api->t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself."); ?>
                  <?php echo $this->api->t("Each word should be separeted by the dash characted."); ?>
                </p>
                <p>
                  <?php echo $this->api->t("Please note also that two different contents, translated in @lang, must have two different URNs.", array('@lang' => $this->api->t($lang))); ?>
                </p>
              </div>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'urn')); ?>
            </div>
          </div>
          <div class="row de-row">
            <div class="col-md-2 col-sm-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'description',
                'label' => $this->api->t('Description'),
              )); ?>
            </div>
            <div class="col-md-10 col-sm-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'description',
                'attributes' => array('class' => 'xxl form-control')
              )); ?>
              <div class="de-info alert alert-info">
                <p>
                  <?php echo $this->api->t("The description is not directly shown to the user but it's used as a meta-data for search engines purposes."); ?>
                </p>
              </div>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'description')); ?>
            </div>
          </div>
          <div class="row de-row">
            <div class="col-md-2 col-sm-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'title',
                'label' => $this->api->t('Title')
              )); ?>
            </div>
            <div class="col-md-10 col-sm-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'title',
                'attributes' => array('class' => 'l form-control')
              )); ?>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'title')); ?>
            </div>
          </div>
          <div class="row de-row">
            <div class="col-md-2 col-sm-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'subtitle',
                'label' => $this->api->t('Subtitle')
              )); ?>
            </div>
            <div class="col-md-10 col-sm-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'subtitle',
                'attributes' => array('class' => 'xl form-control')
              )); ?>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'subtitle')); ?>
            </div>
          </div>
          <div class="row de-row">
            <div class="col-md-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'body',
                'label' => $this->api->t('Body')
              )); ?>
            </div>
            <div class="col-md-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'body',
                'attributes' => array('class' => 'xxl form-control richtext wysiwyg')
              )); ?>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'body')); ?>
            </div>
          </div>
          <div class="row de-row">
            <div class="col-md-2 de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'preview',
                'label' => $this->api->t('Preview')
              )); ?>
            </div>
            <div class="col-md-10 de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'preview',
                'attributes' => array('class' => 'xxl form-control richtext wysiwyg')
              )); ?>
              <?php echo $this->api->formInputError(array('recordset' => 'node_' . $lang, 'path' => 'preview')); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>

    <fieldset>
      <legend><div class="legend"><?php echo $this->api->t('Tags'); ?></div></legend>
      <div class="row de-row">
        <div class="col-md-2 de-label-wrapper">
          <label class="de-label" for="edit-node-terms"/>
            <?php echo $this->api->t('Tags'); ?>
          </label>
        </div>
        <div class="col-md-10 de-input-wrapper">
          <input type="text" class="de-input xl form-control" name="node[tags]"/>
        </div>
      </div>
    </fieldset>
  
    <fieldset class="de-fieldset">
      <legend><div class="legend"><?php echo $this->api->t('Content access'); ?></div></legend>
      <div class="row de-row">
        <div class="col-md-2 col-sm-4 de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-users"><?php echo $this->api->t('Content administrators'); ?></label>
        </div>
        <div class="col-md-10 col-sm-8 de-input-wrapper">
          <input class="form-control de-input xl" type="text" name="node[record_mode.users]" id="edit-node-record_mode-users" value=""/>
          <?php 
//          echo $this->api->input(array(
//            'widget' => 'autocomplete',
//            'value' => array(),
//            'url' => 'autocomplete/user',
//            'item' => '<div><img src="@[image.url]"/>@[name]</div>',
//            'name' => 'users'
//          )); ?>
          <?php echo $this->api->formInputError(array('recordset' => 'node', 'path' => 'record_mode.users')); ?>
        </div>
      </div>
      <div class="row de-row">
        <div class="col-md-2 col-sm-4 de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-read_mode"><?php echo $this->api->t('Read access'); ?></label>
        </div>
        <div class="col-md-2 col-sm-8 de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.read_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l form-control')
          )); ?>
          <?php echo $this->api->formInputError(array('recordset' => 'node', 'path' => 'record_mode.read_mode')); ?>
        </div>
        <div class="col-md-2 col-sm-4 de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-edit_mode"><?php echo $this->api->t('Edit access'); ?></label>
        </div>
        <div class="col-md-2 col-sm-8 de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.edit_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l form-control')
          )); ?>
          <?php echo $this->api->formInputError(array('recordset' => 'node', 'path' => 'record_mode.edit_mode')); ?>
        </div>
        <div class="col-md-2 col-sm-4 de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-delete_mode"><?php echo $this->api->t('Delete access'); ?></label>
        </div>
        <div class="col-md-2 col-sm-8 de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.delete_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l form-control')
          )); ?>
          <?php echo $this->api->formInputError(array('recordset' => 'node', 'path' => 'record_mode.delete_mode')); ?>
        </div>
      </div>
    </fieldset>

    <div class="de-controls">
      <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
    </div>
  </div>
<?php $this->api->close(); ?>
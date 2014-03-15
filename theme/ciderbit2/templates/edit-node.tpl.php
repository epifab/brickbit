<?php $this->api->import('edit-node--attached', array('node' => $form->getRecordset('node'))); ?>

<?php $this->api->open('form', array('id' => $form->getId())); ?>

  <div class="dataedit">
    <!-- Languages -->
    <fieldset>
      <legend>
        <div class="legend">
          <div>Languages</div>
          <div>
            <?php foreach ($system['langs'] as $lang): ?>
              <a href="#" 
                id="node-lang-control-<?php echo $lang; ?>" 
                class="node-lang-control collapse-control"
                data-target=".node-lang-<?php echo $lang; ?>"
                data-lang="<?php echo $lang; ?>"><img src="<?php echo $this->api->themePath('img/lang/40/' . $lang . '.jpg'); ?>"/></a>
            <?php endforeach; ?>
            
            <?php echo $this->api->input(array(
              'id' => 'current-lang',
              'name' => 'current-lang',
              'widget' => 'textbox',
              'state' => $system['lang'],
              'options' => array_combine($system['langs'], $system['langs']),
              'attributes' => array('class' => 'hide')
            )); ?>
          </div>
        </div>
      </legend>
      <?php foreach ($system['langs'] as $lang): ?>
        <div class="node-lang-<?php echo $lang; ?>">
          <?php $text = $form->getRecordset("node_{$lang}"); ?>
          
          <div class="row de-row">
            <div class="col-md-2 col-sm-2 de-label-wrapper hidden-xs">
              <img src="<?php echo $this->api->themePath('img/lang/40/' . $lang . '.jpg'); ?>"/>              
            </div>
            <div class="col-md-10 col-sm-10 de-input-wrapper">
              <?php echo $this->api->input(array(
                'id' => "node-lang-{$lang}-enable",
                'name' => "node_{$lang}_enable",
                'widget' => 'checkbox',
                'value' => 1,
                'state' => $text->isStored(),
                'attributes' => array(
                  'class' => 'node-lang-enable',
                  'data-lang' => $lang,
                )
              )); ?>
              <div class="btn-group">
                <span 
                  id="node-lang-<?php echo $lang; ?>-enable-btn" 
                  class="btn btn-success node-lang-enable-disable-btn"
                  data-lang="<?php echo $lang; ?>"
                  data-action="enable"><?php echo $this->api->t('Enable'); ?></span>
                <span
                  id="node-lang-<?php echo $lang; ?>-disable-btn" 
                  class="btn btn-danger node-lang-enable-disable-btn" 
                  data-lang="<?php echo $lang; ?>" 
                  data-action="disable"><?php echo $this->api->t('Disable'); ?></span>
              </div>
              <?php //echo $this->api->formInputError(array('name' => "node_{$lang}_enable")); ?>
            </div>
          </div>
          
          <div class="node-lang-<?php echo $lang; ?>-group">
            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'title',
                'columns' => true
              )); ?>
            </div>

            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'subtitle',
                'columns' => true
              )); ?>
            </div>

            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'body',
                'columns' => true,
                'attributes' => array('class' => 'wysiwyg')
              )); ?>
            </div>
            
            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'preview',
                'columns' => true,
                'attributes' => array('class' => 'wysiwyg')
              )); ?>
            </div>

            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'urn',
                'columns' => true,
                'info' => 
                  '<p>' 
                  . $this->api->t("Once you choose a URN you shouldn't change it anymore.") . '<br/>'
                  . $this->api->t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself.") . '<br/>'
                  . $this->api->t("Each word should be separeted by the dash characted.")
                  . '</p>'
                  . '<p>'
                  . $this->api->t("Please note also that two different contents, translated in @lang, must have two different URNs.", array('@lang' => $this->api->t($lang)))
                  . '</p>'
              )); ?>
            </div>

            <div class="row de-row">
              <?php echo $this->api->input(array(
                'recordset' => 'node_' . $lang,
                'path' => 'description',
                'columns' => true,
                'info' => $this->api->t("The description is not directly shown to the user but it's used as a meta-data for search engines purposes.")
              )); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </fieldset>
    <!-- /Languages -->
    
    <!-- Tags -->
    <fieldset>
      <legend><div class="legend"><?php echo $this->api->t('Tags'); ?></div></legend>
      
      <?php echo $this->api->input(array(
       'name' => 'tags',
       'widget' => 'textbox',
       'state' => 'to be implemented',
       'labels' => $this->api->t('Tags'),
       'columns' => true,
       'layout' => array(
         'xs' => array(12, 12),
        )
      )); ?>
    </fieldset>
    <!-- /Tags -->
    
    <!-- Content access -->
    <fieldset class="de-fieldset">
      <legend><div class="legend"><?php echo $this->api->t('Content access'); ?></div></legend>
      
      <div class="row de-row">
        <?php echo $this->api->input(array(
          'name' => 'record_mode_users',
          'widget' => 'textbox',
          'state' => 'to be implemented',
          'label' => $this->api->t('Content administrators'),
          'columns' => true,
          'layout' => array(
            //'xs' => array(5, 7),
            'sm' => array(4, 8),
            'md' => array(2, 10)
          )
        )); ?>
      </div>
      
      <div class="row de-row">
        <?php echo $this->api->input(array(
          'recordset' => 'node',
          'path' => 'record_mode.read_mode',
          'columns' => true,
          'layout' => array(
            'xs' => array(5, 7),
            'sm' => array(4, 8),
            'md' => array(2, 2)
          )
        )); ?>
        
        <?php echo $this->api->input(array(
          'recordset' => 'node',
          'path' => 'record_mode.edit_mode',
          'columns' => true,
          'layout' => array(
            'xs' => array(5, 7),
            'sm' => array(4, 8),
            'md' => array(2, 2)
          )
        )); ?>
        
        <?php echo $this->api->input(array(
          'recordset' => 'node',
          'path' => 'record_mode.delete_mode',
          'columns' => true,
          'layout' => array(
            'xs' => array(5, 7),
            'sm' => array(4, 8),
            'md' => array(2, 2)
          )
        )); ?>
      </div>
    </fieldset>
    <!-- /Contet access -->
    
    <div class="de-controls">
      <input type="submit" class="btn btn-lg btn-primary" value="<?php echo $this->api->t('Save'); ?>"/>
    </div>
  </div>
<?php $this->api->close(); ?>
<response
	type="<?php echo $system['responseType']; ?>"
	url="<?php echo $system['component']['url']; ?>"
	title="<?php echo $page['title']; ?>"
	editFormId="<?php echo $this->api->edit_form_id(); ?>"
	panelFormId="<?php echo $this->api->panel_form_id(); ?>"
	panelFormName="<?php echo $this->api->panel_form_name(); ?>"
	id="<?php echo $system['component']['requestId']; ?>">
	<content><?php $this->api->open('panels'); ?><?php $this->api->load($system['templates']['main']); ?><?php echo $this->api->close(); ?></content>
	<javascript><?php echo $this->api->jss(); ?></javascript>
</response>
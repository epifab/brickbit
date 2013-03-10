<response
	type="<?php echo $system['responseType']; ?>"
	url="<?php echo $system['component']['url']; ?>"
	title="<?php echo $page['title']; ?>"
	editFormId="<?php echo $this->api->edit_form_id(); ?>"
	id="<?php echo $system['component']['requestId']; ?>">
	<content><?php $this->api->import($system['templates']['main']); ?></content>
	<javascript><?php echo $this->api->jss(); ?></javascript>
</response>
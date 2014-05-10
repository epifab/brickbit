<response
  type="<?php echo $system['responseType']; ?>"
  url="<?php echo $system['component']['url']; ?>"
  title="<?php echo $page['title']; ?>"
  editFormId="<?php echo $this->api->getEditFormId(); ?>"
  id="<?php echo $system['component']['requestId']; ?>">
  <content><?php if ($system['templates']['main']): ?><?php $this->api->import($system['templates']['main']); ?><?php endif; ?></content>
  <javascript><?php // echo $this->api->jss(); ?></javascript>
</response>
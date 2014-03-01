<div class="log">
  <div>
    <h4 class="alert alert-<?php echo $log->level_class; ?>"><?php echo $log->body; ?></h4>
    
    <dl class="dl-horizontal">
      <dt>Timestamp:</dt>
      <dd><?php echo $this->api->dateTimeFormat($log->date_time_request); ?></dd>
      <dt>User:</dt>
      <dd><?php echo $this->api->userName($log->user_id); ?></dd>
    </dl>
    
    <div class="alert alert-info">
      <?php echo $log->trace; ?>
    </div>
  </div>
</div>
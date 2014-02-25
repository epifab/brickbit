<div class="log">
  <h3>Log #<?php echo $log->id; ?></h3>
  
  <div>
    <h4 class="alert alert-<?php echo $log->level_class; ?>"><?php echo $log->body; ?></h4>
    
    <p><b>Timestamp:</b> <?php echo $this->api->dateTimeFormat($log->date_time_request); ?></p>
    <p><b>User:</b> <?php echo $this->api->userName($log->user_id); ?></p>
    
    <div class="alert alert-danger">
      <?php echo $log->trace; ?>
    </div>
  </div>
</div>
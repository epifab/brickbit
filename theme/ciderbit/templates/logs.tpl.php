<table>
  <thead>
    <tr>
      <th>log id</th>
      <th>code</th>
      <th>date</th>
      <th>message</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($logs as $log): ?>
    <tr>
      <td><?php echo $this->api->open('link',  array('url' => 'admin/logs/' . $log->id, 'width' => 900)); ?><?php echo $log->id; ?><?php echo $this->api->close(); ?></td>
      <td><?php echo $log->code; ?></td>
      <td><?php echo $this->api->dateTimeFormat($log->date_time_request); ?></td>
      <td><div class="alert alert-<?php echo $log->level_class; ?>"><?php echo $log->body; ?></div></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
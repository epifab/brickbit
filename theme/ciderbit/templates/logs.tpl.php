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
      <td><?php echo $log->id; ?></td>
      <td><?php echo $log->code; ?></td>
      <td><?php echo date('d/m/Y H:i:s', $log->date_time_request); ?></td>
      <td><div class="alert alert-<?php echo $log->level_class; ?>"><?php echo $log->body; ?></div></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
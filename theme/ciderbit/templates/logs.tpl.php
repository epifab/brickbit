<table>
  <thead>
    <tr>
      <th>log id</th>
      <th>type</th>
      <th>date</th>
      <th>key</th>
      <th>message</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($logs as $log): ?>
    <tr>
      <td><?php echo $log['id']; ?></td>
      <td><?php echo $log['type']; ?></td>
      <td><?php echo date('d/m/Y H:i:s', $log['time']); ?></td>
      <td><?php echo $log['key']; ?></td>
      <td><?php echo $log['message']; ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
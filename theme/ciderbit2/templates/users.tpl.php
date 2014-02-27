<div class="container">
  <div class="edit-controls btn-toolbar">
    <a href="<?php echo $this->api->path('user/add'); ?>" class="btn btn-primary">
      <span class="glyphicon glyphicon-user"></span> Create a new user
    </a>
  </div>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php foreach ($users as $user): ?>
          <td><?php echo $user->id; ?></td>
          <td><?php echo $user->full_name; ?></td>
          <td><?php echo $user->email; ?></td>
          <td>
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="glyphicon glyphicon-cog"></span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <?php if ($this->api->access($user->url)): ?>
                  <li>
                    <a href="<?php echo $this->api->path($user->url); ?>">
                      <span class="glyphicon glyphicon-user"></span> <?php echo $this->api->t('View profile'); ?>
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($this->api->access($user->edit_url)): ?>
                  <li>
                    <a href="<?php echo $this->api->path($user->edit_url); ?>">
                      <span class="glyphicon glyphicon-pencil"></span> <?php echo $this->api->t('Edit user'); ?>
                    </a>
                  </li>
                <?php endif; ?>
                <?php if ($this->api->access($user->delete_url)): ?>
                  <li>
                    <a href="<?php echo $this->api->path($user->delete_url); ?>">
                      <span class="glyphicon glyphicon-trash"></span> <?php echo $this->api->t('Delete user'); ?>
                    </a>
                  </li>
                <?php endif; ?>
              </ul>
            </div>

          </td>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>
</div>
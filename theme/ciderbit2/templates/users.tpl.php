<div class="container">
  <div class="edit-controls btn-toolbar">
    <a href="<?php echo $this->api->vpath('user/add'); ?>" class="btn btn-primary">
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
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?php echo $user->id; ?></td>
          <td>
            <?php echo $this->api->open('link', array(
                'url' => $user->url,
                'ajax' => true
                )); ?>
              <?php echo $user->full_name; ?>
            <?php echo $this->api->close(); ?>
          </td>
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
                    <?php echo $this->api->open('link', array(
                        'url' => $user->url,
                        'ajax' => true
                        )); ?>
                      <span class="glyphicon glyphicon-user"></span> <?php echo $this->api->t('View profile'); ?>
                    <?php echo $this->api->close(); ?>
                  </li>
                <?php endif; ?>
                <?php if ($this->api->access($user->edit_url)): ?>
                  <li>
                    <?php echo $this->api->open('link', array(
                        'url' => $user->edit_url,
                        'ajax' => true
                        )); ?>
                      <span class="glyphicon glyphicon-pencil"></span> <?php echo $this->api->t('Edit user'); ?>
                    <?php echo $this->api->close(); ?>
                  </li>
                <?php endif; ?>
                <?php if ($this->api->access($user->delete_url)): ?>
                  <li>
                    <?php echo $this->api->open('link', array(
                        'url' => $user->delete_url,
                        'ajax' => true,
                        'confirm' => true,
                        'confirmTitle' => $this->api->t('User will be deleted.', array('@user' => $user->full_name)),
                        'confirmQuest' => $this->api->t('User <em>@user</em> will be deleted but the content he created or moderated will stay.<br/>Do you really want to delete this user?', array('@user' => $user->full_name))
                        )); ?>
                      <span class="glyphicon glyphicon-trash"></span> <?php echo $this->api->t('Delete user'); ?>
                    <?php echo $this->api->close(); ?>
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
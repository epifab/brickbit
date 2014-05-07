<h2><?php echo $u->full_name; ?></h2>
<div class="col-lg-2 col-md-2 col-sm-2">
  <dl class="dl-horizontal">
    <dt><?php echo $this->api->t('Email address'); ?></td>
    <dd><?php echo $u->email; ?></dd>
    <dt><?php echo $this->api->t('Member since'); ?></td>
    <dd><?php echo $this->api->dateFormat($u->ins_date_time); ?></dd>
    <dt><?php echo $this->api->t('Last login'); ?></td>
    <dd><?php echo $this->api->dateFormat($u->last_login); ?></dd>
    <dt><?php echo $this->api->t('Roles'); ?></td>
    <dd>
      <ul class="list-inline">
      <?php foreach ($u->roles as $userRole): ?>
        <li><span class="label label-default"><?php echo $userRole->role->name; ?></span></li>
      <?php endforeach; ?>
      </ul>
    </dd>
    <dt><?php echo $this->api->t('Permissions'); ?></td>
    <dd>
      <ul class="list-inline">
      <?php foreach ($u->permissions as $permission): ?>
        <li><span class="label label-default"><?php echo $permission->name; ?></span></li>
      <?php endforeach; ?>
      </ul>
    </dd>
  </dl>
</div>
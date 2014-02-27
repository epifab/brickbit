<div class="col-lg-2 col-md-2 col-sm-2">
  <h2><?php echo $u->full_name; ?></h2>
  <dl class="dl-horizontal">
    <dt><?php echo $this->api->t('Email address'); ?></td>
    <dd><?php echo $u->email; ?></dd>
    <dt><?php echo $this->api->t('Member since'); ?></td>
    <dd><?php echo $this->api->dateFormat($u->ins_date_time); ?></dd>
    <dt><?php echo $this->api->t('Last login'); ?></td>
    <dd><?php echo $this->api->dateFormat($u->last_login); ?></dd>
  </dl>
</div>
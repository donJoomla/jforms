<?php
defined('_JEXEC') or die('Restricted access');
?>
<form  class="form-horizontal" role="form" method="post" style="margin:0;" id="jform<?php echo $module->id; ?>">
  <?php echo $message ?>
  <?php if(count($fields)>0) : ?>
  <?php foreach($fields as $field): ?>
  <?php if (!$field->hidden) : ?>
  <div class="form-group"> <span class="col-lg-3 control-label"><?php echo $field->label; ?></span>
    <div class="col-lg-9">
      <?php endif; ?>
      <?php echo @$field->input ?>
      <?php if (!$field->hidden) : ?>
    </div>
  </div>
  <?php endif; ?>
  <?php endforeach; ?>
  <div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
      <button type="submit" class="btn btn-primary btn-block submit_button" data-loading-text="Loading..."><?php echo $submit_label; ?></button>
    </div>
  </div>
  <?php endif; ?>
</form>

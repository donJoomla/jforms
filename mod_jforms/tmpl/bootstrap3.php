<?php
defined('_JEXEC') or die('Restricted access');
?>
<form role="form" method="post" id="jform<?php echo $module->id; ?>">
  <?php echo $message ?>
  <?php if(count($fields)>0) : ?>
  <?php foreach($fields as $field): ?>
  <?php if (!$field->hidden) : ?>
  <div class="form-group">
    <?php echo $field->label; ?>
  <?php endif; ?>
    <?php echo @$field->input ?>
  <?php if (!$field->hidden) : ?>
  </div>
  <?php endif; ?>
  <?php endforeach; ?>
  <div class="form-group">
    <button type="submit" class="btn btn-primary btn-block submit_button" data-loading-text="Loading..."><?php echo $submit_label; ?></button>
  </div>
  <?php endif; ?>
</form>

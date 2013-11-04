<?php
defined('_JEXEC') or die('Restricted access');
?>
<form class="form-horizontal" method="post" style="margin:0;" id="jform<?php echo $module->id; ?>">
  <?php echo $message ?>
  <?php if(count($fields)>0) : ?>
  <?php foreach($fields as $field): ?>
  <?php if (!$field->hidden) : ?>
  <div class="control-group"> <span class="control-label"><?php echo $field->label; ?></span>
    <div class="controls">
      <?php endif; ?>
      <?php echo @$field->input ?>
      <?php if (!$field->hidden) : ?>
    </div>
  </div>
  <?php endif; ?>
  <?php endforeach; ?>
  <div class="form-actions">
    <button type="submit" class="btn btn-inverse btn-block submit_button" data-loading-text="Loading..."><?php echo $submit_label; ?></button>
  </div>
  <?php endif; ?>
</form>

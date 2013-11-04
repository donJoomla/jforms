<?php defined('_JEXEC') or die('Restricted access'); ?>
<button data-toggle="modal" href="#formModal<?php echo $module->id; ?>" class="btn btn-default"><?php echo $module->title; ?></button>
<div class="modal fade hide" id="formModal<?php echo $module->id; ?>">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="formModal<?php echo $module->id; ?>Label"><?php echo $module->title; ?></h3>
  </div>
  <form class="form-horizontal" method="post" style="margin:0;" id="jform<?php echo $module->id; ?>">
    <div class="modal-body">
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
      <?php endif; ?>
    </div>
    <div class="modal-footer">
      <button class="btn close_button" data-dismiss="modal" aria-hidden="true">Close</button>
      <button type="submit" class="btn btn-primary submit_button" data-loading-text="Loading..."><?php echo $submit_label; ?></button>
    </div>
  </form>
</div>

<?php
defined('_JEXEC') or die('Restricted access');
?>
<form role="form" method="post" id="jform<?php echo $module->id; ?>">
  <?php echo $message ?>
  <div class="input-group"> 
    <input name="jform<?php echo $module->id; ?>[email]" type="text" class="form-control" placeholder="Enter email address...">
    <span class="input-group-btn">
      <button type="submit" class="btn btn-primary submit_button" data-loading-text="Loading..."><?php echo $submit_label; ?></button>
    </span> 
  </div>
  
<?php 
foreach($fields as $key=>$field) {
	if($key !== 'jform'.$module->id.'_email') echo @$field->input;
} 
?>
</form>

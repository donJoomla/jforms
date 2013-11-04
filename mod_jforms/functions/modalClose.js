jQuery(document).ready(function(){
	jQuery(document).on('jsSent', function(data) {
		id = data.id;
		if(jQuery.inArray('modalClose', data.functions) !== -1) {
			if(jQuery("#formModal" + id).length > 0 && jQuery("#formModal" + id + " .alert-error").length <= 0) {
				jQuery("#formModal" + id + " .submit_button").hide();
				jQuery("#formModal" + id + " .control-group").hide();
				jQuery("#formModal" + id + " .close_button").prepend("<span class=\'count\'>(<span class=\'num\'></span>)</span> ");
				jQuery("#formModal" + id + " .num").html("3");
				var timer = setInterval(function () {
					jQuery("#formModal" + id + " .num").html(function (i, html) {
						if (parseInt(html) > 0) {
							return parseInt(html) - 1;
						} else {
							clearTimeout(timer);
							jQuery("#formModal" + id + "").modal('hide');
						}
					});
				}, 1000);
			}
			jQuery("#formModal" + id + "").on("hidden", function() {
				jQuery("#formModal" + id + " .submit_button").show();
				jQuery("#formModal" + id + " .control-group").show();
				jQuery("#jform" + id + " .jfMessage").html("");
				jQuery("#formModal" + id + " .count").remove();
				
			});
		}
	});
});
function mod_jforms(module_id, jsFunctions)
{
	//trigger plugins
	jQuery("#jform" + module_id + "").trigger({
		type:"jsReady",
		id: module_id,
		functions: jsFunctions
	});
	
	//submit form using ajax
	jQuery("#jform" + module_id + "").submit(function() {
		var dataString = jQuery("#jform" + module_id + "").serialize().replace(/\%5B/g, "[").replace(/\%5D/g, "]");
		jQuery("#jform" + module_id + "").trigger({
			type:"jsSubmit",
			id: module_id,
			functions: jsFunctions
		});
		var submitLabel = jQuery("#jform" + module_id + " .submit_button").html();
		jQuery("#jform" + module_id + " .submit_button").html("Loading...");
		jQuery("#jform" + module_id + " *").attr("disabled", "disabled");
		jQuery.ajax({
			type: "POST",
			url: location.pathname + location.search,
			data: dataString,
			error: function() {
				jQuery("#jform" + module_id + " .jfMessage").html("")
					.hide()
					.append("<div class=\"alert\"><a class=\"close\" data-dismiss=\"alert\">×</a>Error. Please try again</div>")
					.fadeIn(500);
				jQuery("#jform" + module_id + " .submit_button").html(submitLabel);
				jQuery("#jform" + module_id + " *").removeAttr("disabled");
				jQuery("#jform" + module_id + "").trigger({
					type:"jsError",
					id: module_id,
					functions: jsFunctions
				});
			},
			success: function(data) {
				var jfMessage = jQuery(data).find("#system-message").html();
				//console.log(jQuery(data));
				jQuery("#jform" + module_id + " .jfMessage")
					.hide()
					.html(jfMessage)
					.fadeIn(500);
				jQuery("#jform" + module_id + " .submit_button").html(submitLabel);
				jQuery("#jform" + module_id + " *").removeAttr("disabled");
				jQuery("#jform" + module_id + "").trigger({
					type:"jsSent",
					id: module_id,
					functions: jsFunctions
				});
			}
		});
		return false;
	});
}
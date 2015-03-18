// Configuration manager class

function NSPGK5ConfigManager() {
	this.init();
}

NSPGK5ConfigManager.prototype.init = function() {
	// create additional variable to avoid problems with the scopes
	$obj = this;
	// button load
	jQuery('#config_manager_load').click( function(e) {
		e.stopPropagation();
		e.preventDefault();
	    $obj.operation('load');
	});
	// button save
	jQuery('#config_manager_save').click( function(e) {
		e.stopPropagation();
		e.preventDefault();
	   	$obj.operation('save');
	});
	// button delete
	jQuery('#config_manager_delete').click( function(e) {
		e.stopPropagation();
		e.preventDefault();
	   	$obj.operation('delete');
	});
}

NSPGK5ConfigManager.prototype.operation = function(type) {

	var current_url = window.location;
	// check if the current url has no hashes
	if((current_url + '').indexOf('#', 0) === -1) {
		// if no - put the variables
	    current_url = current_url + '&gk_module_task='+type+'&gk_module_file=' + jQuery('#config_manager_'+type+'_filename').val();    
	} else {
		// if the url has hashes - remove the hash 
	    current_url = current_url.substr(0, (current_url + '').indexOf('#', 0) - 1);
	    // and put the variables
	    current_url = current_url + '&gk_module_task='+type+'&gk_module_file=' + jQuery('#config_manager_'+type+'_filename').val();
	}
	// redirect to the url with variables
	window.location = current_url;
}

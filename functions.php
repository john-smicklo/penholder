<?php

/**
 *	=================
 *	System functions.
 *	=================
 */

#############################
## Error handling methods. ##
#############################

error_reporting(E_ALL);						// Report all errors for development environments only.
set_error_handler('__error_handler');		// Custom error handler.
set_exception_handler('_push_error');		// Uncaught exceptions.


## User-defined error handler.	
function __error_handler($code, $message, $file, $line) {
	## The error code is not associated with error_reporting.
	if(!(error_reporting() & $code)) return false;
	
	## Error types.
	$errors = array(1, 256);		# E_ERROR and E_USER_ERROR.
	$warnings = array(2, 512);		# E_WARNING and E_USER_WARNING.
	$notices = array(8, 1024);		# E_NOTICE and E_USER_NOTICE.

	## Determine the error type.
	if(in_array((int) $code, $errors)) $error = array('heading' => 'Error', 'execute' => true);
	elseif(in_array((int) $code, $warnings)) $error = array('heading' => 'Warning', 'execute' => true);
	elseif(in_array((int) $code, $notices)) $error = array('heading' => 'Notice', 'execute' => false);
	else $error = array('heading' => 'Error', 'execute' => true);
	
	## Support for Asynchronous requests.
	if(array_key_exists('inline', $_GET)) { ?>
		<p class="error"><strong><?php echo $error['heading'];?>:</strong> <?php echo $message; ?> in 
		<code><?php echo $file;?></code> on line <strong><?php echo $line;?></strong>.</p>
	<?php } else { ?>
		<h3><?php echo $error['heading'];?></h3>
		<p class="error"><?php echo $message; ?> in <code><?php echo $file;?></code> on line <strong><?php echo $line;?></strong>.</p>
	<?php } if($error['execute'] == true) exit;
	
	## Do not execute the internal error handler.
	return true;
}

## End-user errors and uncaught exceptions.
function _push_error($problem, $die = true, $header = '') {
	## Set error properties.
	if(is_object($problem)) $message = $problem->getMessage(); else $message = $problem;
	if(isset($header) && !empty($header)) $heading = $header; else $heading = 'Error';
	if(is_object($problem)) $execute = true; else $execute = (boolean) $die;
	
	## Asynchronous requests.
	if(array_key_exists('inline', $_GET)) { ?>
		<p class="error"><strong>Error:</strong> <?php echo $message; ?></p>
	<?php } else { ?>
		<h3><?php echo $heading; ?></h3>
		<p class="error"><?php echo $message; ?></p>
	<?php } if($execute === true) exit;
}

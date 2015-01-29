<?php

	/**
	 * This file was made to correcly load the DOMPDF library in Elgg. 
	 * 
	 * The reason for this file is the different auto class loaded of the DOMPDF library
	 */

	// load the base DOMPDF library
	include_once(dirname(__FILE__) . "/dompdf/dompdf_config.inc.php");
	
	// register the DOMPDF autoloaded to the callstack of PHP
	spl_autoload_register("DOMPDF_autoload");
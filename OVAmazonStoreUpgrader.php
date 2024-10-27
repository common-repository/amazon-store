<?php

include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; 

class OVAmazonStoreUpgrader extends WP_Upgrader {

	var $result;

	function OVAmazonStoreUpgrader() {
		return $this->__construct();
	}

	function __construct() {
		$title = 'Amazon Store Install';
		$nonce = 'ov-install';
		$url = 'options-general.php?page=ov-aws-update&ov_action=install';		
		
		parent::__construct(new OVAmazonStoreInstallerSkin(compact('title', 'nonce', 'url')));
	}
	
	
	function install_strings() {
		$this->strings['no_package'] = __('Install package not available.');
		$this->strings['downloading_package'] = __('Downloading install package from <span class="code">%s</span>.');
		$this->strings['unpack_package'] = __('Unpacking the package.');
		$this->strings['installing_package'] = __('Installing the plugin.');
		$this->strings['process_failed'] = __('Amazon Store Install Failed.');
		$this->strings['process_success'] = __('Amazon Store Installed successfully.');
	}

	function install($package,$destination) {
		
		// TODO: make sure alwasy set
		if ( ! defined('FS_CHMOD_DIR') )
			define('FS_CHMOD_DIR', 0777 );
		if ( ! defined('FS_CHMOD_FILE') )
			define('FS_CHMOD_FILE', 0644 );
		
		$this->init();
		$this->install_strings();

		$this->run(array(
					'package' => $package,
					'destination' => $destination,
					'clear_destination' => true, // overwrite files.
					'clear_working' => true,
					'hook_extra' => array()
					));
		return $this->result;
	}

}

class OVAmazonStoreInstallerSkin extends WP_Upgrader_Skin {

	function OVAmazonStoreInstallerSkin($args = array()) {
		return $this->__construct($args);
	}

	function __construct($args = array()) {
		parent::__construct($args);
	}

	function after() {

		$actions = array();
		$image = 'install-success.png';
		
		if ( ! $this->result || is_wp_error($this->result) ){
			$image = 'install-failure.png';
			$actions = array(
				'install_page' => '<a href="options-general.php?page=ov-aws-update" title="Try again" >I have corrected the error and I want to try again.</a>',
				'report_error_page' => '<a href="http://onlinevelocity.com/support-forum" title="Take me to the forum so I can submit a bug.</a>'
			);
		}else{
			$actions = array(
				'settings_page' => '<a href="options-general.php?page=ov-aws-menu" title="Settings" >Go to settings</a>'
			);
		}
		
		$this->feedback('<img src="http://tracker.onlinevelocity.com/ovstoretracker/images/'.$image.'"/><br><strong>Actions:</strong> ' . implode(' | ', (array)$actions));
	}
}


?>
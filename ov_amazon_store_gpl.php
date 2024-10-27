<?php
/*
Plugin Name: Amazon Store
Plugin URI: http://wpstore.onlinevelocity.com
Description: Wordpress Amazon Store Plugin
Version: 1.2.1
Author: Navid Mitchell
Author URI: http://onlinevelocity.com
*/

/*  Copyright 2010  Mitchell Software LLC 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class OVAmazonStoreGPL {

	/**
	 * Initializes Plugin
	 * @return unknown_type
	 */
	public static function inst_init(){
		if ( ! defined( 'OVELOCITY_DEBUG' ) )
			define('OVELOCITY_DEBUG',false);	
			
		if ( ! defined( 'OVELOCITY_CACHING_ENABLED' ) )
			define('OVELOCITY_CACHING_ENABLED',true);		
		
		// Pre-2.6 compatibility
		if ( ! defined( 'WP_CONTENT_URL' ) )
		      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
		if ( ! defined( 'WP_CONTENT_DIR' ) )
		      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( ! defined( 'WP_PLUGIN_URL' ) )
		      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( ! defined( 'WP_PLUGIN_DIR' ) )
		      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
		
		if( ! defined('OVELOCITY_GPL_ROOT'))
				define('OVELOCITY_GPL_ROOT','/amazon-store');	
				      
		// Define OVELOCITY_ROOT as sub folder since this is internal to the gpled code 
		if( ! defined('OVELOCITY_ROOT'))
			define('OVELOCITY_ROOT', (OVELOCITY_DEBUG ? '' : OVELOCITY_GPL_ROOT ). '/ov_amazon_store' . (OVELOCITY_DEBUG ? '/source' : '' ));          
	
		if( ! defined('OVELOCITY_ROOT_DIR'))
			define('OVELOCITY_ROOT_DIR',WP_PLUGIN_DIR.OVELOCITY_ROOT);	
			
		// we still need the original path so we can support the 1.3 main plugin
		// once all users are off of 1.3 then we can remove this 
		if( ! defined('OV_ROOT'))
			define('OV_ROOT',OVELOCITY_ROOT);
			
		// display main plugin if installed
		if(self::doesMainPluginExist()){
			$main_path = OVELOCITY_ROOT_DIR . '/ov_amazon_store.php';
			include_once($main_path);		
			// initializ main plugin
			OVAmazonStore::initialize();	
		}
		
		add_action('admin_menu', 'OVAmazonStoreGPL::menuInitialize');
			
	}
	
	private static function doesMainPluginExist(){
		$main_path = OVELOCITY_ROOT_DIR . '/ov_amazon_store.php';
		return file_exists($main_path);
	}
	
	/******************* UI Methods ******************************/
	
	public static function menuInitialize() {
		
		// check if we should display the update menu 
		$version = get_option('ov-amazon-store-version','0');
			
		try{
			
			if(!self::doesMainPluginExist() || self::getCurrentVersion()->{'version'} > $version){
				
				//create new sub menu 
				add_options_page('Amazon Store Install', 'Amazon Store Install', 'manage_options', 'ov-aws-update', 'OVAmazonStoreGPL::displayMenu');
				
			}
			
		}catch(Exception $e){} // no worries try later
	}
	
	public static function displayMenu(){
		// display install form or install data depending on state. 
		if( isset($_REQUEST['ov_action']) && !empty($_REQUEST['ov_action']) ){
			try{
				$info = self::getCurrentVersion();
			
				self::install($info->{'version'},$info->{'url'});
			
			}catch(Exception $e){
				echo '<div class="wrap">';
				echo '<h2>Amazon Store Install</h2>';
				echo '<img src="http://tracker.onlinevelocity.com/ovstoretracker/images/no-version.png" />'; 
				echo '<br>';
				echo 'Sorry... There was an error getting the current version info - '.$e->getMessage();
				echo '<br>';
				echo '<strong>Please make sure your server allows traffic to update.onlinevelocity.com</strong>';
				echo '</div>';
			} 
			
		}else{
			if(self::doesMainPluginExist()){
				self::displayUpdateMenu();
			}else{
				self::displayInstallMenu();
			}
		}
	}
	
	private static function displayInstallMenu(){
		echo '<div class="wrap">';
		echo '<h2>Amazon Store Install</h2>';
		echo '<form method="post">';
		echo '<input type="hidden" name="ov_action" value="i" />';
		echo '<div>';
		echo '<img src="http://tracker.onlinevelocity.com/ovstoretracker/images/install.png" />'; 
		echo '<br>';
		echo '<b>Congratulations you are almost there...</b><br>'; 
		echo 'To complete the install you must press the install button.<br> By clicking install you agree to the Licensing terms specified below.<br><br>'; 
		echo '</div>';
		echo '<textarea rows="15" cols="100">';
		echo 'PLUGIN LICENSE TERMS&#10;';
		echo '&#10;This software does not require payment of any license fee directly. Instead Plugin is set to use the Online Velocity affiliate code 1 out of 5 times using your Associate ID 4 out of 5 times. A paid version is available at http://OnlineVelocity.com. This PLUGIN License is a binding legal agreement between the individual who downloads the software (“You”) and the Licensor.';
		echo '&#10;THIS SOFTWARE IS COPYRIGHTED AND THE OWNER OF THE COPYRIGHT CLAIMS ALL EXCLUSIVE RIGHTS TO SUCH SOFTWARE, EXCEPT AS LICENSED TO USERS HEREUNDER AND SUBJECT TO STRICT COMPLIANCE WITH THE TERMS OF THIS PLUGIN LICENSE.';
		echo '&#10;&#10;Even though a license fee is not paid for use of such PLUGIN, it does not mean that there are not conditions for using such PLUGIN. As a condition for granting you a license to use PLUGIN programs that are available through this site, you agree to all of the following terms and conditions. You are deemed to have read, understand, and have accepted all such terms and conditions upon executing a download of any PLUGIN program.';
		echo '&#10;If you fail to abide by any of the terms and conditions set forth herein, your license to use such PLUGIN shall be immediately and automatically revoked, without any notice or other action by the Copyright Owner.';
		echo '&#10;&#10;TERMS AND CONDITIONS';
		echo '&#10;&#10;Background';
		echo '&#10;1.You are granted a non-exclusive license to use the Downloaded Software subject to Your compliance with all of the terms and conditions of this PLUGIN License.';
		echo '&#10;2.You may only use the software on a computer that you own, lease or control. You may make one backup copy of the software for your own use to replace the primary copy in the event of hard-drive failure or other unavailability of the primary copy. The backup copy shall retain all copyright notices.';
		echo '&#10;3.You are only granted a license for the machine-readable, object code portion of the software. You will not modify, enhance, reverse engineer or otherwise alter the software from its current state.';
		echo '&#10;4.You may not distribute, copy, publish, assign, sell, bargain, convey, transfer, pledge, lease or grant any further rights to use the software.';
		echo '&#10;5.You will not have any proprietary rights in and to the software. You acknowledge and agree that the Licensor retains all copyrights and other proprietary rights in and to the software.';
		echo '&#10;6.Your license to use the software shall be revocable by the Licensor upon written notice to you. This license shall automatically terminate upon your violation of the terms hereof or upon your use of the software beyond the scope of the license provided herein.';
		echo '&#10;7.Use within the scope of this license is free of charge other than the implicit tranfer of sales to Online Velocity and no royalty or licensing fees shall be payable by you. Use beyond the scope of this license shall constitute copyright infringement.';
		echo '&#10;8.This license shall be effective and bind you upon your downloading of the software.';
		echo '&#10;9.You accept the software on an “AS IS” and with all faults basis. No representations and warranties are made to you regarding any aspect of the software.';
		echo '&#10;10.THE LICENSOR HEREBY DISCLAIMS ANY AND ALL WARRANTIES, EXPRESS OR IMPLIED, RELATIVE TO THE SOFTWARE, INCLUDING BUT NOT LIMITED TO ANY WARRANTY OF FITNESS FOR A PARTICULAR PURPOSE OR MERCHANTIBILITY. LICENSOR SHALL NOT BE LIABLE OR RESPONSIBLE FOR ANY DAMAGES, INJURIES OR LIABILITIES CAUSED DIRECTLY OR INDIRECTLY FROM THE USE OF THE SOFTWARE, INCLUDING BUT NOT LIMITED TO INCIDENTAL, ONSEQUENTIAL OR SPECIAL DAMAGES.';
		echo '&#10;11.This PLUGIN License shall be interpreted under the laws of the State of NM/US. You agree that all controversies pertaining to the software and/or this PLUGIN Agreement shall be brought in the courts of NM/US. You hereby submit to the jurisdictions of such court. However, federal courts located in the state of NM/US shall have jurisdiction over copyright claims brought by the Licensor and you hereby submit to the jurisdiction of federal court located in the State of NM.';
		echo '&#10;12.Licensor’s failure to enforce any rights hereunder or its copyright in the software shall not be construed as amending this agreement or waiving any of Licensor’s rights hereunder or under any provision of state of federal law.';
		echo '</textarea><br><br>';
		echo '<input type="submit" class="button-primary" value="Accept & Install Amazon Store" />';
		echo '</form>';
		echo '</div>';
	}
	
	private static function displayUpdateMenu(){
		echo '<div class="wrap">';
		echo '<h2>Amazon Store Update</h2>';
		echo '<form method="post">';
		echo '<input type="hidden" name="ov_action" value="i" />';
		echo '<div>';
		echo '<img src="http://tracker.onlinevelocity.com/ovstoretracker/images/update.png" />'; 
		echo '<br>';
		echo 'There is a new Update available. Wahoo!!!<br><br>'; 
		echo '</div>';
		echo '<input type="submit" class="button-primary" value="Update Amazon Store" />';
		echo '</form>';
		echo '</div>';
	}
	
	private static function getCurrentVersion(){
		$url = 'http://update.onlinevelocity.com/version.php';
		$json = @file_get_contents($url);
		
		if(!($json === FALSE)){
			$data = OVJSON::decode($json);
		}else{
			throw new Exception("Could not connect to update.onlinevelocity.com"); 
		}
		
		return $data;
	}
	
	private static function install($version,$package){
		include_once WP_PLUGIN_DIR . OVELOCITY_GPL_ROOT . '/OVAmazonStoreUpgrader.php'; 
		
		$pluginDir = WP_PLUGIN_DIR . OVELOCITY_GPL_ROOT . '/ov_amazon_store/';	
		
		$installer = new OVAmazonStoreUpgrader();
		
		$result = $installer->install($package,$pluginDir);

		if ( !(! $result || is_wp_error($result)) && self::doesMainPluginExist()){
			
			// update version first since plugin was actually installed
			// we may need additional checks for post install hook 
			update_option('ov-amazon-store-version',$version);
			
			$main_path = OVELOCITY_ROOT_DIR . '/ov_amazon_store.php';
			include_once($main_path);
			
			// check if function exists for 1.3.x support. 
			if (function_exists('OVAmazonStore::postInstall()')){
				OVAmazonStore::postInstall();
			}
		}
		
	}
	
}

class OVJSON 
{
    public static function encode($obj)
    {
        return json_encode($obj);
    }
    
    public static function decode($json, $toAssoc = false)
    {
        $result = json_decode($json, $toAssoc);
        
        if (function_exists('json_last_error')){
	        switch(json_last_error()){
	            case JSON_ERROR_DEPTH:
	                $error =  ' - Maximum stack depth exceeded';
	                break;
	            case JSON_ERROR_CTRL_CHAR:
	                $error = ' - Unexpected control character found';
	                break;
	            case JSON_ERROR_SYNTAX:
	                $error = ' - Syntax error, malformed JSON';
	                break;
	            case JSON_ERROR_NONE:
	            default:
	                $error = '';                    
	        }
        }else{
        	if($result == NULL){
        		$error = 'Could not decode JSON';
        	}
        }
        if (!empty($error))
            throw new Exception('JSON Error: '.$error);        
        
        return $result;
    }
}

add_action('init', 'OVAmazonStoreGPL::inst_init');


?>

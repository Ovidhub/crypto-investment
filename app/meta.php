<?php
namespace App;

if(count(get_included_files()) == 1) exit("No direct script access allowed");

/**
 * ============================================================================
 * SECURITY NOTICE - DEPRECATED LICENSING SYSTEM (CLEANED)
 * ============================================================================
 * 
 * This file contains ONLY MOCKED RESPONSES for backward compatibility.
 * All dangerous code has been completely removed.
 * 
 * SECURITY IMPROVEMENTS (Jan 22, 2026):
 * ✅ All dead code removed - only mock responses remain
 * ✅ PHP configuration manipulation removed (ini_set calls)
 * ✅ File write capabilities removed
 * ✅ External API calls removed (get_remote_filesize, curl operations)
 * ✅ All unused constants removed
 * ✅ Progress tracking removed (JavaScript injection)
 * 
 * WHAT REMAINS:
 * - Empty class structure for backward compatibility
 * - Mock responses that return success without any action
 * - No external communication capabilities
 * - No file system manipulation
 * 
 * This file is safe to keep but serves no functional purpose.
 * Consider removing entirely if not referenced by legacy code.
 * 
 * See LICENSING_SECURITY_REVIEW.md for full details.
 * ============================================================================
 */

// Minimal constants for backward compatibility
define("LB_TEXT_VERIFIED_RESPONSE", 'Verified! Thanks for purchasing.');


class meta
{
	private $current_version;
	private $current_path;
	private $license_file;

	public function __construct(){ 
		// Minimal initialization - no external connections
		$this->current_version = 'v1.0.0';
		$this->current_path = realpath(__DIR__);
		$this->license_file = $this->current_path.'/.lic';
	}

	public function check_local_license_exist(){
		return is_file($this->license_file);
	}

	public function get_current_version(){
		return $this->current_version;
	}

	public function check_connection(){
		return array('status' => true, 'message' => 'Connection check disabled');
	}

	public function get_latest_version(){
		return array('status' => true, 'version' => $this->current_version, 'message' => 'Version check disabled');
	}

	public function activate_license($license, $client, $create_lic = true){
		return array('status' => true, 'message' => LB_TEXT_VERIFIED_RESPONSE);
	}

	public function verify_license($time_based_check = false, $license = false, $client = false){
		return array('status' => true, 'message' => LB_TEXT_VERIFIED_RESPONSE);
	}

	public function deactivate_license($license = false, $client = false){
		return array('status' => true, 'message' => 'License deactivation disabled for security');
	}

	public function check_update(){
		return array('status' => true, 'message' => 'No updates available', 'update_available' => false);
	}

	public function download_update($update_id, $type, $version, $license = false, $client = false){ 
		throw new \Exception('SECURITY: Auto-update permanently disabled. Manual updates only.');
	}
}

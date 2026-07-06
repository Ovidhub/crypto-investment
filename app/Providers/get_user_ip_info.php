<?php
/**
 * ============================================================================
 * IP Detection Utility - SECURITY HARDENED
 * ============================================================================
 * 
 * PURPOSE: Detect user IP and provide default country for registration form
 * 
 * SECURITY NOTES:
 * - External geolocation API has been DISABLED (returns US as default)
 * - IP spoofing vulnerability FIXED (uses only REMOTE_ADDR)
 * - Does NOT trust proxy headers (HTTP_X_FORWARDED_FOR, HTTP_CLIENT_IP)
 * - Returns mock geolocation data for privacy
 * 
 * USAGE: Included in JetstreamServiceProvider for registration view
 * 
 * SECURITY FIXES (Jan 22, 2026):
 * ✅ Removed trust of spoofable HTTP headers
 * ✅ Uses only $_SERVER['REMOTE_ADDR'] (server-validated IP)
 * ✅ External geolocation disabled (was already done)
 * ✅ Removed debug code
 * ✅ Variables scoped to prevent pollution
 * 
 * WARNING: Do not re-enable external API calls or proxy header trust
 * ============================================================================
 */

/**
 * Get IP geolocation information (returns mock data for privacy)
 * 
 * @param string|null $ip IP address to lookup
 * @param string $purpose Type of info to return (location, country, city, etc)
 * @param bool $deep_detect DEPRECATED - no longer uses proxy headers
 * @return mixed Geolocation data based on purpose
 */
function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    
    // Validate IP or get real IP (no proxy headers trusted)
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        // SECURITY: Only use REMOTE_ADDR - ignore proxy headers
        // Proxy headers (X-Forwarded-For, Client-IP) can be spoofed by attackers
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        // SECURITY: External geolocation API disabled - returns default data
        // This prevents data leakage to third-party services
        $ipdat = (object)[
            'geoplugin_countryCode' => 'US',
            'geoplugin_countryName' => 'United States',
            'geoplugin_city' => 'Unknown',
            'geoplugin_region' => 'Unknown',
            'geoplugin_regionName' => 'Unknown',
            'geoplugin_continentCode' => 'NA',
            'geoplugin_latitude' => '0',
            'geoplugin_longitude' => '0',
            'geoplugin_currencyCode' => 'USD',
            'geoplugin_currencySymbol' => '$'
        ];
        
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

/**
 * Get user's real IP address (server-validated only)
 * 
 * SECURITY: Does NOT trust proxy headers to prevent IP spoofing
 * Use $_SERVER['REMOTE_ADDR'] which is set by the web server
 * 
 * @return string User's IP address
 */
function getUserIP()
{
    // SECURITY FIX: Only use REMOTE_ADDR (server-validated)
    // Do NOT trust HTTP_CLIENT_IP or HTTP_X_FORWARDED_FOR
    // These can be easily spoofed by attackers
    return $_SERVER['REMOTE_ADDR'];
}

// Set variables for use in registration view
$user_ip = getUserIP();
$user_country = ip_info("Visitor", "Country");
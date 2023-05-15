<?php
/**
 * MageCloud
 *
 * MageCloud redirect - make redirect to specific store based on country from user ip
 *
 * @category MageCloud
 */

$user_ip = getUserIP();
if($user_ip &&
    preg_match('@/admin/@', $_SERVER['REQUEST_URI']) != 1
    && $_SERVER['REQUEST_URI'] != '/admin'
    && preg_match('@/rest/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/rest?@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/soap/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/soap?@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/iconic/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/shiptheory/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/shipstation@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/api/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@/auctane/@', $_SERVER['REQUEST_URI']) != 1
    && preg_match('@Google@', $_SERVER['HTTP_USER_AGENT']) != 1
    && preg_match('@mailchimp.html@', $_SERVER['REQUEST_URI']) != 1
) {

    if(isset($_COOKIE['website_code']) && !isset($_GET['___store'])) {
        $store_code = $_COOKIE['website_code'];
    } else if (isset($_GET['___store'])) {
        $store_code = $_GET['___store'];

        setcookie('website_code', $_GET['___store'], time() + (86400 * 30), "/");
    } else {
        $country_code = $_SERVER["HTTP_CF_IPCOUNTRY"];
        $store_code = strtolower(getStoreCodeFromCountryCode($country_code));
    }

    redirect($store_code);
}

function getUserIP() {

    // Get real visitor IP behind CloudFlare network
    if (isset( $_SERVER["HTTP_CF_IPCOUNTRY"])) {
        $_SERVER['REMOTE_ADDR'] =  $_SERVER["HTTP_CF_IPCOUNTRY"];
        $_SERVER['HTTP_CLIENT_IP'] =  $_SERVER["HTTP_CF_IPCOUNTRY"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    }
    else {
        $ip = $remote;
    }

    return $ip;
}

function redirect($code) {
    $host  = $_SERVER['HTTP_HOST'];

    // get current store code
    if(preg_match('@/us/@', $_SERVER['REQUEST_URI']) == 1 || preg_match('@/us$@', $_SERVER['REQUEST_URI']) == 1 ||
        preg_match('@/eu/@', $_SERVER['REQUEST_URI']) == 1 || preg_match('@/eu$@', $_SERVER['REQUEST_URI']) == 1 ||
        preg_match('@/uk/@', $_SERVER['REQUEST_URI']) == 1 || preg_match('@/uk$@', $_SERVER['REQUEST_URI']) == 1
    ) {
        $data = substr($_SERVER['REQUEST_URI'], 1);
        $redirect_code = strtok($data, '/');
        $redirect_url = ltrim($data, $redirect_code);
    } else {
        $redirect_code = 'uk';
        $redirect_url = $_SERVER['REQUEST_URI'];
    }
    $store_code = '/' . $code;
    $uri = $redirect_url;
    if(($redirect_code != $code) && ($_SERVER['REQUEST_URI'] == '/')) {
        header("Location: https://$host$store_code$uri");
        exit;
    }
}

function getStoreCodeFromCountryCode($country_code) {
    $data = array (
        "AU" => "US", "CA" => "US", "CH" => "US", "AD" => "US", "AL" => "US", "BA" => "US",
        "EU" => "US", "GI" => "US", "GL" => "US", "IE" => "US", "IS" => "US", "LI" => "US",
        "MC" => "US", "MD" => "US", "ME" => "US", "MK" => "US", "NO" => "US", "RS" => "US",
        "SM" => "US", "TR" => "US", "UA" => "US", "VA" => "US", "NZ" => "US", "GG" => "US",
        "IM" => "US", "JE" => "US", "US" => "US", "A1" => "US", "A2" => "US", "AE" => "US",
        "AF" => "US", "AG" => "US", "AI" => "US", "AM" => "US", "AN" => "US", "T1" => "US",
        "AO" => "US", "AP" => "US", "AQ" => "US", "AR" => "US", "AS" => "US", "AW" => "US",
        "AX" => "US", "AZ" => "US", "BB" => "US", "BD" => "US", "BF" => "US", "BH" => "US",
        "BI" => "US", "BJ" => "US", "BL" => "US", "BM" => "US", "BN" => "US", "BO" => "US",
        "BQ" => "US", "BR" => "US", "BS" => "US", "BT" => "US", "BV" => "US", "BW" => "US",
        "BY" => "US", "BZ" => "US", "CC" => "US", "CD" => "US", "CF" => "US", "CG" => "US",
        "CI" => "US", "CK" => "US", "CL" => "US", "CM" => "US", "CN" => "US", "CO" => "US",
        "CR" => "US", "CU" => "US", "CV" => "US", "CW" => "US", "CX" => "US", "XX" => "US",
        "DJ" => "US", "DM" => "US", "DO" => "US", "DZ" => "US", "EC" => "US", "EG" => "US",
        "EH" => "US", "ER" => "US", "ET" => "US", "FJ" => "US", "FK" => "US", "FM" => "US",
        "FO" => "US", "GA" => "US", "GD" => "US", "GE" => "US", "GF" => "US", "GH" => "US",
        "GM" => "US", "GN" => "US", "GP" => "US", "GQ" => "US", "GS" => "US", "GT" => "US",
        "GU" => "US", "GW" => "US", "GY" => "US", "HK" => "US", "HM" => "US", "HN" => "US",
        "HT" => "US", "ID" => "US", "IL" => "US", "IN" => "US", "IO" => "US", "IQ" => "US",
        "JM" => "US", "JO" => "US", "JP" => "US", "KE" => "US", "KG" => "US", "SS" => "US",
        "KH" => "US", "KI" => "US", "KM" => "US", "KN" => "US", "KP" => "US", "KR" => "US",
        "KW" => "US", "KY" => "US", "KZ" => "US", "LA" => "US", "LB" => "US", "LC" => "US",
        "LK" => "US", "LR" => "US", "LS" => "US", "LY" => "US", "MA" => "US", "ZW" => "US",
        "MF" => "US", "MG" => "US", "MH" => "US", "ML" => "US", "MM" => "US", "MN" => "US",
        "MO" => "US", "MP" => "US", "MQ" => "US", "MR" => "US", "MS" => "US", "MU" => "US",
        "MV" => "US", "MW" => "US", "MX" => "US", "MY" => "US", "MZ" => "US", "NA" => "US",
        "NC" => "US", "NE" => "US", "NF" => "US", "NG" => "US", "NI" => "US", "NP" => "US",
        "NR" => "US", "NU" => "US", "O1" => "US", "OM" => "US", "PA" => "US", "PE" => "US",
        "PF" => "US", "PG" => "US", "PH" => "US", "PK" => "US", "PM" => "US", "PN" => "US",
        "PR" => "US", "PS" => "US", "PW" => "US", "PY" => "US", "QA" => "US", "RE" => "US",
        "RU" => "US", "RW" => "US", "SA" => "US", "SB" => "US", "SC" => "US", "SD" => "US",
        "SG" => "US", "SH" => "US", "SJ" => "US", "SL" => "US", "SN" => "US", "SO" => "US",
        "SR" => "US", "ST" => "US", "SV" => "US", "SX" => "US", "SY" => "US", "SZ" => "US",
        "TC" => "US", "TD" => "US", "TF" => "US", "TG" => "US", "TH" => "US", "TJ" => "US",
        "TK" => "US", "TL" => "US", "TM" => "US", "TN" => "US", "TO" => "US", "TT" => "US",
        "TV" => "US", "TW" => "US", "TZ" => "US", "UG" => "US", "UM" => "US", "UY" => "US",
        "UZ" => "US", "VC" => "US", "VE" => "US", "VG" => "US", "VI" => "US", "VN" => "US",
        "VU" => "US", "WF" => "US", "WS" => "US", "YE" => "US", "YT" => "US", "ZA" => "US",
        "ZM" => "US",

        "BE" => "EU", "BG" => "EU", "CY" => "EU", "DK" => "EU", "DE" => "EU", "EE" => "EU",
        "FI" => "EU", "FR" => "EU", "GR" => "EU", "HU" => "EU", "HR" => "EU", "IR" => "EU",
        "IT" => "EU", "LV" => "EU", "LT" => "EU", "LU" => "EU", "MT" => "EU", "NL" => "EU",
        "AT" => "EU", "PL" => "EU", "PT" => "EU", "RO" => "EU", "SK" => "EU", "SI" => "EU",
        "ES" => "EU", "CZ" => "EU", "SE" => "EU",

        "GB" => "UK"

    );

    return $data[$country_code];
}
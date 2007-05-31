<?php

/*
+---------------------------------------------------------------------------+
| Openads v2.3                                                              |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| Copyright (c) 2000-2003 the phpAdsNew developers                          |
| For contact details, see: http://www.phpadsnew.com/                       |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

/**
 * 
 * This is autogenerated merged delivery file which contains all files
 * from delivery merged into one output file.
 * 
 * !!!Warning!!!
 * 
 * Do not edit this file. If you need to do any changes to any delivery PHP file 
 * checkout sourcecode from the svn repository, do a necessary changes inside 
 * "delivery_dev" folder and regenerate delivery files using command:
 * # ant generatedelivery
 * 
 * For more information on ant generator or if you want to check why do this
 * check out the documentation wiki page:
 * https://developer.openads.org/wiki/OptimizationPractices#GenerateDeliveryAntTask
 * 
 */

function parseDeliveryIniFile($configPath = null, $configFile = null, $sections = true)
{
if (!$configPath) {
$configPath = MAX_PATH . '/var';
}
if ($configFile) {
$configFile = '.' . $configFile;
}
$host = getHostName();
$configFileName = $configPath . '/' . $host . $configFile . '.conf.php';
$conf = @parse_ini_file($configFileName, true);
if (!empty($conf)) {
return $conf;
} elseif ($configFile === '.plugin') {
$pluginType = basename($configPath);
$defaultConfig = MAX_PATH . '/plugins/' . $pluginType . '/default.plugin.conf.php';
$conf = @parse_ini_file($defaultConfig, $sections);
if ($conf !== false) {
// check for false here - it's possible file doesn't exist
return $conf;
}
echo MAX_PRODUCT_NAME . " could not read the default configuration file for the {$pluginType} plugin";
exit(1);
}
// Check to ensure Max hasn't been installed
if (file_exists(MAX_PATH . '/var/INSTALLED')) {
echo MAX_PRODUCT_NAME . " has been installed, but no configuration file was found.\n";
exit(1);
}
// Max hasn't been installed, so delivery engine can't run
echo MAX_PRODUCT_NAME . " has not been installed yet -- please read the INSTALL.txt file.\n";
exit(1);
}
function setupConfigVariables()
{
$GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'] = '|';
$GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'] = '__';
if (!empty($GLOBALS['_MAX']['CONF']['openads']['requireSSL'])) {
$GLOBALS['_MAX']['HTTP'] = 'https://';
} else {
if (isset($_SERVER['SERVER_PORT'])) {
if (isset($GLOBALS['_MAX']['CONF']['openads']['sslPort'])
&& $_SERVER['SERVER_PORT'] == $GLOBALS['_MAX']['CONF']['openads']['sslPort'])
{
$GLOBALS['_MAX']['HTTP'] = 'https://';
} else {
$GLOBALS['_MAX']['HTTP'] = 'http://';
}
}
}
$GLOBALS['_MAX']['MAX_RAND'] = $GLOBALS['_MAX']['CONF']['priority']['randmax'];
if (!empty($GLOBALS['_MAX']['CONF']['timezone']['location'])) {
setTimeZoneLocation($GLOBALS['_MAX']['CONF']['timezone']['location']);
}
}
function setTimeZoneLocation($location)
{
if (version_compare(phpversion(), '5.1.0', '>=')) {
date_default_timezone_set($location);
} else {
putenv("TZ={$location}");
}
}
function getHostName()
{
if (!empty($_SERVER['HTTP_HOST'])) {
$host = explode(':', $_SERVER['HTTP_HOST']);
$host = $host[0];
} else {
$host = explode(':', $_SERVER['SERVER_NAME']);
$host = $host[0];
}
return $host;
}
setupDeliveryConfigVariables();
$conf = $GLOBALS['_MAX']['CONF'];
if ($conf['debug']['logfile']) {
@ini_set('error_log', MAX_PATH . '/var/' . $conf['debug']['logfile']);
}
if ($conf['debug']['production']) {
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
} else {
error_reporting(E_ALL);
}
$file = '/lib/max/Delivery/common.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
$file = '/lib/max/Delivery/cookie.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
$GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'] = array();
function MAX_cookieSet($name, $value, $expire = 0)
{
if (!isset($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
$GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
}
$GLOBALS['_MAX']['COOKIE']['CACHE'][$name] = array($value, $expire);
}
function MAX_cookieSetViewerIdAndRedirect($viewerId) {
$conf = $GLOBALS['_MAX']['CONF'];
MAX_cookieSet($conf['var']['viewerId'], $viewerId, _getTimeYearFromNow());
MAX_cookieFlush();
if ($_SERVER['SERVER_PORT'] == $conf['openads']['sslPort']) {
$url = MAX_commonConstructSecureDeliveryUrl(basename($_SERVER['PHP_SELF']));
} else {
$url = MAX_commonConstructDeliveryUrl(basename($_SERVER['PHP_SELF']));
}
$url .= "?{$conf['var']['cookieTest']}=1&" . $_SERVER['QUERY_STRING'];
MAX_header("Location: {$url}");
exit;
}
function MAX_cookieFlush()
{
$conf = $GLOBALS['_MAX']['CONF'];
MAX_cookieSendP3PHeaders();
if (!empty($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
while (list($name,$v) = each ($GLOBALS['_MAX']['COOKIE']['CACHE'])) {
list($value, $expire) = $v;
MAX_setcookie($name, $value, $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
}
$GLOBALS['_MAX']['COOKIE']['CACHE'] = array();
}
$cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
if (!is_array($cookieNames))
return;
foreach ($cookieNames as $cookieName) {
if (empty($_COOKIE["_{$cookieName}"])) {
continue;
}
switch ($cookieName) {
case $conf['var']['blockAd']            : $expire = _getTimeThirtyDaysFromNow(); break;
case $conf['var']['capAd']              : $expire = _getTimeYearFromNow(); break;
case $conf['var']['sessionCapAd']       : $expire = 0; break;
case $conf['var']['blockCampaign']      : $expire = _getTimeThirtyDaysFromNow(); break;
case $conf['var']['capCampaign']        : $expire = _getTimeYearFromNow(); break;
case $conf['var']['sessionCapCampaign'] : $expire = 0; break;
case $conf['var']['blockZone']          : $expire = _getTimeThirtyDaysFromNow(); break;
case $conf['var']['capZone']            : $expire = _getTimeYearFromNow(); break;
case $conf['var']['sessionCapZone']     : $expire = 0; break;
}
if (!empty($_COOKIE[$cookieName]) && is_array($_COOKIE[$cookieName])) {
$data = array();
foreach ($_COOKIE[$cookieName] as $adId => $value) {
$data[] = "{$adId}.{$value}";
}
while (strlen(implode('_', $data)) > 2048) {
$data = array_slice($data, 1);
}
MAX_setcookie($cookieName, implode('_', $data), $expire, '/', (!empty($conf['cookie']['domain']) ? $conf['cookie']['domain'] : null));
}
}
}
function _getTimeThirtyDaysFromNow()
{
return MAX_commonGetTimeNow() + 2592000;
}
function _getTimeYearFromNow()
{
return MAX_commonGetTimeNow() + 31536000;
}
function _getTimeYearAgo()
{
return MAX_commonGetTimeNow() - 31536000;
}
function MAX_cookieUnpackCapping()
{
$conf = $GLOBALS['_MAX']['CONF'];
$cookieNames = $GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'];
if (!is_array($cookieNames))
return;
foreach ($cookieNames as $cookieName) {
if (!empty($_COOKIE[$cookieName])) {
if (!is_array($_COOKIE[$cookieName])) {
$output = array();
$data = explode('_', $_COOKIE[$cookieName]);
foreach ($data as $pair) {
list($name, $value) = explode('.', $pair);
$output[$name] = $value;
}
$_COOKIE[$cookieName] = $output;
}
}
if (!empty($_COOKIE['_' . $cookieName]) && is_array($_COOKIE['_' . $cookieName])) {
foreach ($_COOKIE['_' . $cookieName] as $adId => $cookie) {
if (_isBlockCookie($cookieName)) {
$_COOKIE[$cookieName][$adId] = $cookie;
} else {
if (isset($_COOKIE[$cookieName][$adId])) {
$_COOKIE[$cookieName][$adId] += $cookie;
} else {
$_COOKIE[$cookieName][$adId] = $cookie;
}
}
MAX_cookieSet("_{$cookieName}[{$adId}]", false, _getTimeYearAgo());
MAX_cookieSet("%5F" . urlencode($cookieName.'['.$adId.']'), false, _getTimeYearAgo());
}
}
}
}
function _isBlockCookie($cookieName)
{
if ($cookieName == $GLOBALS['_MAX']['CONF']['var']['blockAd']) {
return true;
}
if ($cookieName == $GLOBALS['_MAX']['CONF']['var']['blockCampaign']) {
return true;
}
if ($cookieName == $GLOBALS['_MAX']['CONF']['var']['blockZone']) {
return true;
}
return false;
}
function MAX_cookieGetUniqueViewerID($create = true)
{
$conf = $GLOBALS['_MAX']['CONF'];
if (isset($_COOKIE[$conf['var']['viewerId']])) {
$userid = $_COOKIE[$conf['var']['viewerId']];
} else {
if ($create) {
$remote_address = $_SERVER['REMOTE_ADDR'];
$local_address  = $conf['webpath']['delivery']; // How do I get the IP address of this server?
list($usec, $sec) = explode(" ", microtime());
$time = (float) $usec + (float) $sec;
$random = mt_rand(0,999999999);
$userid = substr(md5($local_address.$time.$remote_address.$random),0,32);
$GLOBALS['_MAX']['COOKIE']['newViewerId'] = true;
} else {
$userid = null;
}
}
return $userid;
}
function MAX_cookieGetCookielessViewerID()
{
if (empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['HTTP_USER_AGENT'])) {
return '';
}
$cookiePrefix = $GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'];
return $cookiePrefix . substr(md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']), 0, 32-(strlen($cookiePrefix)));
}
function MAX_cookieSendP3PHeaders() {
if ($GLOBALS['_MAX']['CONF']['p3p']['policies']) {
MAX_header("P3P: ". _generateP3PHeader());
}
}
function MAX_Delivery_cookie_setCapping($type, $id, $block = 0, $cap = 0, $sessionCap = 0)
{
$conf = $GLOBALS['_MAX']['CONF'];
$setBlock = false;
if ($cap > 0) {
// This capping cookie requires a "permanent" expiration time
$expire = MAX_commonGetTimeNow() + $conf['cookie']['permCookieSeconds'];
if (!isset($_COOKIE[$conf['var']['cap' . $type]][$id])) {
$value = 1;
$setBlock = true;
} else if ($_COOKIE[$conf['var']['cap' . $type]][$id] >= $cap) {
$value = -$_COOKIE[$conf['var']['cap' . $type]][$id]+1;
$setBlock = true;
} else {
$value = 1;
}
MAX_cookieSet("_{$conf['var']['cap' . $type]}[{$id}]", $value, $expire);
}
if ($sessionCap > 0) {
if (!isset($_COOKIE[$conf['var']['sessionCap' . $type]][$id])) {
$value = 1;
$setBlock = true;
} else if ($_COOKIE[$conf['var']['sessionCap' . $type]][$id] >= $sessionCap) {
$value = -$_COOKIE[$conf['var']['sessionCap' . $type]][$id]+1;
$setBlock = true;
} else {
$value = 1;
}
MAX_cookieSet("_{$conf['var']['sessionCap' . $type]}[{$id}]", $value, 0);
}
if ($block > 0 || $setBlock) {
MAX_cookieSet("_{$conf['var']['block' . $type]}[{$id}]", MAX_commonGetTimeNow(), _getTimeThirtyDaysFromNow());
}
}
function _generateP3PHeader()
{
$conf = $GLOBALS['_MAX']['CONF'];
$p3p_header = '';
if ($conf['p3p']['policies']) {
if ($conf['p3p']['policyLocation'] != '') {
$p3p_header .= " policyref=\"".$conf['p3p']['policyLocation']."\"";
}
if ($conf['p3p']['policyLocation'] != '' && $conf['p3p']['compactPolicy'] != '') {
$p3p_header .= ", ";
}
if ($conf['p3p']['compactPolicy'] != '') {
$p3p_header .= " CP=\"".$conf['p3p']['compactPolicy']."\"";
}
}
return $p3p_header;
}
$file = '/lib/max/Delivery/remotehost.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
function MAX_remotehostProxyLookup()
{
$conf = $GLOBALS['_MAX']['CONF'];
if ($conf['logging']['proxyLookup']) {
$proxy = false;
if (!empty($_SERVER['HTTP_VIA'])) {
$proxy = true;
} elseif (!empty($_SERVER['REMOTE_HOST'])) {
$aProxyHosts = array(
'proxy',
'cache',
'inktomi'
);
foreach ($aProxyHosts as $proxyName) {
if (strpos($proxyName, $_SERVER['REMOTE_HOST']) !== false) {
$proxy = true;
break;
}
}
}
if ($proxy) {
// Try to find the "real" IP address the viewer has come from
$aHeaders = array(
'HTTP_FORWARDED',
'HTTP_FORWARDED_FOR',
'HTTP_X_FORWARDED',
'HTTP_X_FORWARDED_FOR',
'HTTP_CLIENT_IP'
);
foreach ($aHeaders as $header) {
if (!empty($_SERVER[$header])) {
$ip = $_SERVER[$header];
break;
}
}
if (!empty($ip)) {
// The "remote IP" may be a list, ensure that
$ip = explode(',', $ip);
$ip = trim($ip[count($ip) - 1]);
if (($ip != 'unknown') && (!MAX_remotehostPrivateAddress($ip))) {
// Set the "real" remote IP address, and unset
// (so that we don't accidently do this twice)
$_SERVER['REMOTE_ADDR'] = $ip;
$_SERVER['REMOTE_HOST'] = '';
$_SERVER['HTTP_VIA']    = '';
}
}
}
}
}
function MAX_remotehostReverseLookup()
{
if (empty($_SERVER['REMOTE_HOST'])) {
if ($GLOBALS['_MAX']['CONF']['logging']['reverseLookup']) {
$_SERVER['REMOTE_HOST'] = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
} else {
$_SERVER['REMOTE_HOST'] = $_SERVER['REMOTE_ADDR'];
}
}
}
function MAX_remotehostSetClientInfo()
{
if ($GLOBALS['_MAX']['CONF']['logging']['sniff'] && isset($_SERVER['HTTP_USER_AGENT'])) {
include MAX_PATH . '/lib/phpSniff/phpSniff.class.php';
$client = new phpSniff($_SERVER['HTTP_USER_AGENT']);
$GLOBALS['_MAX']['CLIENT'] = $client->_browser_info;
}
}
function MAX_remotehostSetGeoInfo()
{
if (!function_exists('parseDeliveryIniFile')) {
function parseDeliveryIniFile($configPath = null, $configFile = null, $sections = true)
{
if (!$configPath) {
$configPath = MAX_PATH . '/var';
}
if ($configFile) {
$configFile = '.' . $configFile;
}
$host = getHostName();
$configFileName = $configPath . '/' . $host . $configFile . '.conf.php';
$conf = @parse_ini_file($configFileName, true);
if (!empty($conf)) {
return $conf;
} elseif ($configFile === '.plugin') {
$pluginType = basename($configPath);
$defaultConfig = MAX_PATH . '/plugins/' . $pluginType . '/default.plugin.conf.php';
$conf = @parse_ini_file($defaultConfig, $sections);
if ($conf !== false) {
// check for false here - it's possible file doesn't exist
return $conf;
}
echo MAX_PRODUCT_NAME . " could not read the default configuration file for the {$pluginType} plugin";
exit(1);
}
// Check to ensure Max hasn't been installed
if (file_exists(MAX_PATH . '/var/INSTALLED')) {
echo MAX_PRODUCT_NAME . " has been installed, but no configuration file was found.\n";
exit(1);
}
// Max hasn't been installed, so delivery engine can't run
echo MAX_PRODUCT_NAME . " has not been installed yet -- please read the INSTALL.txt file.\n";
exit(1);
}
}
$pluginTypeConfig = parseDeliveryIniFile(MAX_PATH . '/var/plugins/config/geotargeting', 'plugin');
$type = (!empty($pluginTypeConfig['geotargeting']['type'])) ? $pluginTypeConfig['geotargeting']['type'] : null;
if (!is_null($type) && $type != 'none') {
$pluginConfig = parseDeliveryIniFile(MAX_PATH . '/var/plugins/config/geotargeting/' . $type, 'plugin');
$GLOBALS['_MAX']['CONF']['geotargeting'] = array_merge($pluginTypeConfig['geotargeting'], $pluginConfig['geotargeting']);
if (isset($GLOBALS['conf'])) {
$GLOBALS['conf']['geotargeting'] = $GLOBALS['_MAX']['CONF']['geotargeting'];
}
@include(MAX_PATH . '/plugins/geotargeting/' . $type . '/' . $type . '.delivery.php');
$functionName = 'MAX_Geo_'.$type.'_getInfo';
if (function_exists($functionName)) {
$GLOBALS['_MAX']['CLIENT_GEO'] = $functionName();
}
}
}
function MAX_remotehostPrivateAddress($ip)
{
require_once 'Net/IPv4.php';
$aPrivateNetworks = array(
'10.0.0.0/8',
'172.16.0.0/12',
'192.168.0.0/16',
'127.0.0.0/24'
);
foreach ($aPrivateNetworks as $privateNetwork) {
if (Net_IPv4::ipInNetwork($ip, $privateNetwork)) {
return true;
}
}
return false;
}
$file = '/lib/max/Delivery/log.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
$file = '/lib/max/Dal/Delivery.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
function MAX_Dal_Delivery_Include()
{
static $included;
if (isset($included)) {
return;
}
$included = true;
$conf = $GLOBALS['_MAX']['CONF'];
if (isset($conf['origin']['type']) && is_readable(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php')) {
require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['origin']['type']) . '.php');
} else {
require(MAX_PATH . '/lib/OA/Dal/Delivery/' . strtolower($conf['database']['type']) . '.php');
}
}
function MAX_Delivery_log_logAdRequest($viewerId, $adId, $creativeId, $zoneId)
{
if (_viewersHostOkayToLog()) {
$conf = $GLOBALS['_MAX']['CONF'];
list($geotargeting, $zoneInfo, $userAgentInfo, $maxHttps) = _prepareLogInfo();
$table = $conf['table']['prefix'] . $conf['table']['data_raw_ad_request'];
MAX_Dal_Delivery_Include();
OA_Dal_Delivery_logAction(
$table,
$viewerId,
$adId,
$creativeId,
$zoneId,
$geotargeting,
$zoneInfo,
$userAgentInfo,
$maxHttps
);
}
}
function MAX_Delivery_log_logAdImpression($viewerId, $adId, $creativeId, $zoneId)
{
if (_viewersHostOkayToLog()) {
$conf = $GLOBALS['_MAX']['CONF'];
list($geotargeting, $zoneInfo, $userAgentInfo, $maxHttps) = _prepareLogInfo();
$table = $conf['table']['prefix'] . $conf['table']['data_raw_ad_impression'];
MAX_Dal_Delivery_Include();
OA_Dal_Delivery_logAction(
$table,
$viewerId,
$adId,
$creativeId,
$zoneId,
$geotargeting,
$zoneInfo,
$userAgentInfo,
$maxHttps
);
}
}
function MAX_Delivery_log_logAdClick($viewerId, $adId, $creativeId, $zoneId)
{
if (_viewersHostOkayToLog()) {
$conf = $GLOBALS['_MAX']['CONF'];
list($geotargeting, $zoneInfo, $userAgentInfo, $maxHttps) = _prepareLogInfo();
$table = $conf['table']['prefix'] . $conf['table']['data_raw_ad_click'];
MAX_Dal_Delivery_Include();
OA_Dal_Delivery_logAction(
$table,
$viewerId,
$adId,
$creativeId,
$zoneId,
$geotargeting,
$zoneInfo,
$userAgentInfo,
$maxHttps
);
}
}
function MAX_Delivery_log_logTrackerImpression($viewerId, $trackerId)
{
if (_viewersHostOkayToLog()) {
$conf = $GLOBALS['_MAX']['CONF'];
if (empty($conf['rawDatabase']['host'])) {
$conf['rawDatabase']['host'] = 'singleDB';
}
if (isset($conf['rawDatabase']['serverRawIp'])) {
$serverRawIp = $conf['rawDatabase']['serverRawIp'];
} else {
$serverRawIp = $conf['rawDatabase']['host'];
}
list($geotargeting, $zoneInfo, $userAgentInfo, $maxHttps) = _prepareLogInfo();
$table = $conf['table']['prefix'] . $conf['table']['data_raw_tracker_impression'];
MAX_Dal_Delivery_Include();
$rawTrackerImpressionId = OA_Dal_Delivery_logTracker(
$table,
$viewerId,
$trackerId,
$serverRawIp,
$geotargeting,
$zoneInfo,
$userAgentInfo,
$maxHttps
);
return array('server_raw_tracker_impression_id' => $rawTrackerImpressionId, 'server_raw_ip' => $serverRawIp);
}
return false;
}
function MAX_Delivery_log_logVariableValues($variables, $trackerId, $serverRawTrackerImpressionId, $serverRawIp)
{
$conf = $GLOBALS['_MAX']['CONF'];
foreach ($variables as $variable) {
if (isset($_GET[$variable['name']])) {
$value = $_GET[$variable['name']];
// Do not save variable if empty or if the JS engine set it to "undefined"
if (!strlen($value) || $value == 'undefined') {
unset($variables[$variable['variable_id']]);
continue;
}
switch ($variable['type']) {
case 'int':
case 'numeric':
$value = preg_replace('/[^0-9.]/', '', $value);
$value = floatval($value); break;
case 'date':
if (!empty($value)) {
$value = date('Y-m-d H:i:s', strtotime($value));
} else {
$value = '';
}
break;
}
} else {
// Do not save anything if the variable isn't set
unset($variables[$variable['variable_id']]);
continue;
}
$variables[$variable['variable_id']]['value'] = $value;
}
if (count($variables)) {
MAX_Dal_Delivery_Include();
OA_Dal_Delivery_logVariableValues($variables, $serverRawTrackerImpressionId, $serverRawIp);
}
}
function _viewersHostOkayToLog()
{
$conf = $GLOBALS['_MAX']['CONF'];
if (!empty($conf['logging']['ignoreHosts'])) {
$hosts = str_replace(',', '|', $conf['logging']['ignoreHosts']);
$hosts = '#('.$hosts.')$#i';
$hosts = str_replace('.', '\.', $hosts);
$hosts = str_replace('*', '[^.]+', $hosts);
// Check if the viewer's IP address is in the ignore list
if (preg_match($hosts, $_SERVER['REMOTE_ADDR'])) {
return false;
}
// Check if the viewer's hostname is in the ignore list
if (preg_match($hosts, $_SERVER['REMOTE_HOST'])) {
return false;
}
}
return true;
}
function _prepareLogInfo()
{
$conf = $GLOBALS['_MAX']['CONF'];
$geotargeting = array();
if (isset($conf['geotargeting']['saveStats']) && $conf['geotargeting']['saveStats'] && !empty($GLOBALS['_MAX']['CLIENT_GEO'])) {
$geotargeting = $GLOBALS['_MAX']['CLIENT_GEO'];
}
$zoneInfo = array();
if (!empty($_GET['loc'])) {
$zoneInfo = parse_url($_GET['loc']);
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
$zoneInfo = parse_url($_SERVER['HTTP_REFERER']);
}
if (!empty($zoneInfo['scheme'])) {
$zoneInfo['scheme'] = ($zoneInfo['scheme'] == 'https') ? 1 : 0;
}
if (isset($GLOBALS['_MAX']['CHANNELS'])) {
$zoneInfo['channel_ids'] = $GLOBALS['_MAX']['CHANNELS'];
}
if ($conf['logging']['sniff'] && isset($GLOBALS['_MAX']['CLIENT'])) {
$userAgentInfo = array(
'os' => $GLOBALS['_MAX']['CLIENT']['os'],
'long_name' => $GLOBALS['_MAX']['CLIENT']['long_name'],
'browser'   => $GLOBALS['_MAX']['CLIENT']['browser'],
);
} else {
$userAgentInfo = array();
}
$maxHttps = 0;
if ($_SERVER['SERVER_PORT'] == $conf['openads']['sslPort']) {
$maxHttps = 1;
}
return array($geotargeting, $zoneInfo, $userAgentInfo, $maxHttps);
}
function MAX_Delivery_log_getArrGetVariable($name)
{
$varName = $GLOBALS['_MAX']['CONF']['var'][$name];
return isset($_GET[$varName]) ? explode($GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'], $_GET[$varName]) : array();
}
function MAX_Delivery_log_ensureIntegerSet(&$aArray, $index)
{
if (!is_array($aArray)) {
$aArray = array();
}
if (empty($aArray[$index])) {
$aArray[$index] = 0;
} else {
if (!is_integer($aArray[$index])) {
$aArray[$index] = intval($aArray[$index]);
}
}
}
function MAX_Delivery_log_setAdLimitations($index, $aAds, $aCaps)
{
_setLimitations('Ad', $index, $aAds, $aCaps);
}
function MAX_Delivery_log_setCampaignLimitations($index, $aCampaigns, $aCaps)
{
_setLimitations('Campaign', $index, $aCampaigns, $aCaps);
}
function MAX_Delivery_log_setZoneLimitations($index, $aZones, $aCaps)
{
_setLimitations('Zone', $index, $aZones, $aCaps);
}
function _setLimitations($type, $index, $aItems, $aCaps)
{
MAX_Delivery_log_ensureIntegerSet($aCaps['block'], $index);
MAX_Delivery_log_ensureIntegerSet($aCaps['capping'], $index);
MAX_Delivery_log_ensureIntegerSet($aCaps['session_capping'], $index);
MAX_Delivery_cookie_setCapping(
$type,
$aItems[$index],
$aCaps['block'][$index],
$aCaps['capping'][$index],
$aCaps['session_capping'][$index]
);
}
function MAX_commonGetDeliveryUrl($file = null)
{
$conf = $GLOBALS['_MAX']['CONF'];
if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == $conf['openads']['sslPort']) {
$url = MAX_commonConstructSecureDeliveryUrl($file);
} else {
$url = MAX_commonConstructDeliveryUrl($file);
}
return $url;
}
function MAX_commonConstructDeliveryUrl($file)
{
$conf = $GLOBALS['_MAX']['CONF'];
return 'http://' . $conf['webpath']['delivery'] . '/' . $file;
}
function MAX_commonConstructSecureDeliveryUrl($file)
{
$conf = $GLOBALS['_MAX']['CONF'];
if ($conf['openads']['sslPort'] != 443) {
$path = preg_replace('#/#', ':' . $conf['openads']['sslPort'] . '/', $conf['webpath']['deliverySSL']);
} else {
$path = $conf['webpath']['deliverySSL'];
}
return 'https://' . $path . '/' . $file;
}
function MAX_commonConstructPartialDeliveryUrl($file, $ssl = false)
{
$conf = $GLOBALS['_MAX']['CONF'];
if ($ssl) {
return '//' . $conf['webpath']['deliverySSL'] . '/' . $file;
} else {
return '//' . $conf['webpath']['delivery'] . '/' . $file;
}
}
function MAX_commonRemoveSpecialChars(&$var)
{
static $magicQuotes;
if (!isset($magicQuotes)) {
$magicQuotes = get_magic_quotes_gpc();
}
if (isset($var)) {
if (!is_array($var)) {
if ($magicQuotes) {
$var = stripslashes($var);
}
$var = strip_tags($var);
$var = str_replace(array("\n", "\r"), array('', ''), $var);
$var = trim($var);
} else {
array_walk($var, 'MAX_commonRemoveSpecialChars');
}
}
}
function MAX_commonSetNoCacheHeaders()
{
MAX_header('Pragma: no-cache');
MAX_header('Cache-Control: private, max-age=0, no-cache');
MAX_header('Date: '.gmdate('D, d M Y H:i:s', MAX_commonGetTimeNow()).' GMT');
}
function MAX_commonRegisterGlobalsArray($args = array())
{
static $magic_quotes_gpc;
if (!isset($magic_quotes_gpc)) {
$magic_quotes_gpc = ini_get('magic_quotes_gpc');
}
$found = false;
foreach($args as $key) {
if (isset($_GET[$key])) {
$value = $_GET[$key];
$found = true;
}
if (isset($_POST[$key])) {
$value = $_POST[$key];
$found = true;
}
if ($found) {
if (!$magic_quotes_gpc) {
if (!is_array($value)) {
$value = addslashes($value);
} else {
$value = MAX_commonSlashArray($value);
}
}
$GLOBALS[$key] = $value;
$found = false;
}
}
}
function MAX_commonDeriveSource($source)
{
return MAX_commonEncrypt(trim(urldecode($source)));
}
function MAX_commonEncrypt($string)
{
$convert = '';
if (isset($string) && substr($string,1,4) != 'obfs' && $GLOBALS['_MAX']['CONF']['delivery']['obfuscate']) {
$strLen = strlen($string);
for ($i=0; $i < $strLen; $i++) {
$dec = ord(substr($string,$i,1));
if (strlen($dec) == 2) {
$dec = 0 . $dec;
}
$dec = 324 - $dec;
$convert .= $dec;
}
$convert = '{obfs:' . $convert . '}';
return ($convert);
} else {
return $string;
}
}
function MAX_commonDecrypt($string)
{
$conf = $GLOBALS['_MAX']['CONF'];
$convert = '';
if (isset($string) && substr($string,1,4) == 'obfs' && $conf['delivery']['obfuscate']) {
$strLen = strlen($string);
for ($i=6; $i < $strLen-1; $i = $i+3) {
$dec = substr($string,$i,3);
$dec = 324 - $dec;
$dec = chr($dec);
$convert .= $dec;
}
return ($convert);
} else {
return($string);
}
}
function MAX_commonInitVariables()
{
MAX_commonRegisterGlobalsArray(array('context', 'source', 'target', 'withText', 'withtext', 'ct0', 'what', 'loc', 'referer', 'zoneid', 'campaignid', 'bannerid', 'clientid'));
global $context, $source, $target, $withText, $withtext, $ct0, $what, $loc, $referer, $zoneid, $campaignid, $bannerid, $clientid;
if (!isset($context)) 	$context = array();
if (!isset($source))	$source = '';
if (!isset($target)) 	$target = '_blank';
if (isset($withText) && !isset($withtext))  $withtext = $withText;
if (!isset($withtext)) 	$withtext = '';
if (!isset($ct0)) 	$ct0 = '';
if (!isset($what)) {
if (!empty($bannerid)) {
$what = 'bannerid:'.$bannerid;
} elseif (!empty($campaignid)) {
$what = 'campaignid:'.$campaignid;
} elseif (!empty($zoneid)) {
$what = 'zone:'.$zoneid;
} else {
$what = '';
}
} elseif (preg_match('/^.+:.+$/', $what)) {
list($whatName, $whatValue) = explode(':', $what);
if ($whatName == 'zone') {
$whatName = 'zoneid';
}
global $$whatName;
$$whatName = $whatValue;
}
if (!isset($clientid))  $clientid = '';
$source = MAX_commonDeriveSource($source);
if (!empty($loc)) {
$loc = stripslashes($loc);
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
$loc = $_SERVER['HTTP_REFERER'];
} else {
$loc = '';
}
if (!empty($referer)) {
$_SERVER['HTTP_REFERER'] = stripslashes($referer);
} else {
if (isset($_SERVER['HTTP_REFERER'])) unset($_SERVER['HTTP_REFERER']);
}
$GLOBALS['_MAX']['COOKIE']['LIMITATIONS']['arrCappingCookieNames'] = array(
$GLOBALS['_MAX']['CONF']['var']['blockAd'],
$GLOBALS['_MAX']['CONF']['var']['capAd'],
$GLOBALS['_MAX']['CONF']['var']['sessionCapAd'],
$GLOBALS['_MAX']['CONF']['var']['blockCampaign'],
$GLOBALS['_MAX']['CONF']['var']['capCampaign'],
$GLOBALS['_MAX']['CONF']['var']['sessionCapCampaign'],
$GLOBALS['_MAX']['CONF']['var']['blockZone'],
$GLOBALS['_MAX']['CONF']['var']['capZone'],
$GLOBALS['_MAX']['CONF']['var']['sessionCapZone']);
}
function MAX_commonDisplay1x1()
{
MAX_header('Content-Type: image/gif');
MAX_header('Content-Length: 43');
echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
}
function MAX_commonGetTimeNow()
{
static $now;
if (!isset($now)) {
$now = $GLOBALS['_MAX']['NOW'] = time();
}
return $now;
}
function MAX_setcookie($name, $value, $expire, $path, $domain)
{
setcookie($name, $value, $expire, $path, $domain);
}
function MAX_header($value)
{
header($value);
}
$file = '/lib/max/Delivery/cache.php';
$GLOBALS['_MAX']['FILES'][$file] = true;
define ('OA_DELIVERY_CACHE_FUNCTION_ERROR', 'Function call returned an error');
$GLOBALS['OA_Delivery_Cache'] = array(
'path'   => MAX_PATH.'/var/cache/',
'prefix' => 'deliverycache_',
'expiry' => $GLOBALS['_MAX']['CONF']['delivery']['cacheExpire']
);
function OA_Delivery_Cache_fetch($name, $isHash = false, $expiryTime = null)
{
$filename = OA_Delivery_Cache_buildFileName($name, $isHash);
$cache_complete = false;
$cache_contents = '';
$ok = @include($filename);
if ($ok && $cache_complete == true) {
if ($expiryTime === null) {
$expiryTime = $GLOBALS['OA_Delivery_Cache']['expiry'];
}
$now = MAX_commonGetTimeNow();
if (    (isset($cache_time) && $cache_time < $now - $expiryTime)
|| (isset($cache_expire) && $cache_expire > $now) )
{
OA_Delivery_Cache_store($name, $cache_contents, $isHash);
return false;
}
return $cache_contents;
}
return false;
}
function OA_Delivery_Cache_store($name, $cache, $isHash = false, $expireAt = null)
{
if ($cache === OA_DELIVERY_CACHE_FUNCTION_ERROR) {
// Don't store the result to enable permanent caching
return false;
}
if (!is_writable($GLOBALS['OA_Delivery_Cache']['path'])) {
return false;
}
$filename = OA_Delivery_Cache_buildFileName($name, $isHash);
$cache_literal  = "<"."?php\n\n";
$cache_literal .= "$"."cache_contents   = ".var_export($cache, true).";\n\n";
$cache_literal .= "$"."cache_name       = '".addcslashes($name, "'")."';\n";
$cache_literal .= "$"."cache_time       = ".MAX_commonGetTimeNow().";\n";
if ($expireAt !== null) {
$cache_literal .= "$"."cache_expire = ".$expireAt.";\n";
}
$cache_literal .= "$"."cache_complete = true;\n\n";
$cache_literal .= "?".">";
$tmp_filename = tempnam($GLOBALS['OA_Delivery_Cache']['path'], $GLOBALS['OA_Delivery_Cache']['prefix'].'tmp_');
if ($fp = @fopen($tmp_filename, 'wb')) {
@fwrite ($fp, $cache_literal, strlen($cache_literal));
@fclose ($fp);
if (!@rename($tmp_filename, $filename)) {
// On some systems rename() doesn't overwrite destination
@unlink($filename);
if (!@rename($tmp_filename, $filename)) {
@unlink($tmp_filename);
}
}
return true;
}
return false;
}
function OA_Delivery_Cache_store_return($name, $cache, $isHash = false, $expireAt = null)
{
if (OA_Delivery_Cache_store($name, $cache, $isHash, $expireAt)) {
return $cache;
}
return OA_Delivery_Cache_fetch($name, $isHash);
}
function OA_Delivery_Cache_delete($name = '')
{
if ($name != '') {
$filename = OA_Delivery_Cache_buildFileName($name);
if (file_exists($filename)) {
@unlink ($filename);
return true;
}
} else {
$cachedir = @opendir($GLOBALS['OA_Delivery_Cache']['path']);
while (false !== ($filename = @readdir($cachedir))) {
if (preg_match("#^{$GLOBALS['OA_Delivery_Cache']['prefix']}[0-9A-F]{32}.php$#i", $filename))
@unlink ($filename);
}
@closedir($cachedir);
return true;
}
return false;
}
function OA_Delivery_Cache_info()
{
$result = array();
$cachedir = @opendir($GLOBALS['OA_Delivery_Cache']['path']);
while (false !== ($filename = @readdir($cachedir))) {
if (preg_match("#^{$GLOBALS['OA_Delivery_Cache']['prefix']}[0-9A-F]{32}.php$#i", $filename)) {
$cache_complete = false;
$cache_contents = '';
$cache_name     = '';
$ok = @include($filename);
if ($ok && $cache_complete == true) {
$result[$cache_name] = strlen(serialize($cache_contents));
}
}
}
@closedir($cachedir);
return $result;
}
function OA_Delivery_Cache_buildFileName($name, $isHash = false)
{
if(!$isHash) {
$name = md5($name);
}
return $GLOBALS['OA_Delivery_Cache']['path'].$GLOBALS['OA_Delivery_Cache']['prefix'].$name.'.php';
}
function OA_Delivery_Cache_getName($functionName)
{
$args = func_get_args();
$args[0] = strtolower(str_replace('MAX_cacheGet', '', $args[0]));
return join('???', $args);
}
function MAX_cacheGetAd($ad_id, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $ad_id);
if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aRows = OA_Dal_Delivery_getAd($ad_id);
$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
}
return $aRows;
}
function MAX_cacheGetZoneLinkedAds($zoneId, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $zoneId);
if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aRows = OA_Dal_Delivery_getZoneLinkedAds($zoneId);
$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
}
return $aRows;
}
function MAX_cacheGetZoneInfo($zoneId, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $zoneId);
if (!$cached || ($aRows = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aRows = OA_Dal_Delivery_getZoneInfo($zoneId);
$aRows = OA_Delivery_Cache_store_return($sName, $aRows);
}
return $aRows;
}
function MAX_cacheGetLinkedAds($search, $campaignid, $laspart, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $search, $campaignid, $laspart);
if (!$cached || ($aAds = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aAds = OA_Dal_Delivery_getLinkedAds($search, $campaignid, $laspart);
$aAds = OA_Delivery_Cache_store_return($sName, $aAds);
}
return $aAds;
}
function MAX_cacheGetCreative($filename, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $filename);
if (!$cached || ($aCreative = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aCreative = OA_Dal_Delivery_getCreative($filename);
$aCreative['contents'] = addslashes(serialize($aCreative['contents']));
$aCreative = OA_Delivery_Cache_store_return($sName, $aCreative);
}
$aCreative['contents'] = unserialize(stripslashes($aCreative['contents']));
return $aCreative;
}
function MAX_cacheGetTracker($trackerid, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $trackerid);
if (!$cached || ($aTracker = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aTracker = OA_Dal_Delivery_getTracker($trackerid);
$aTracker = OA_Delivery_Cache_store_return($sName, $aTracker, $isHash = true);
}
return $aTracker;
}
function MAX_cacheGetTrackerVariables($trackerid, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $trackerid);
if (!$cached || ($aVariables = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$aVariables = OA_Dal_Delivery_getTrackerVariables($trackerid);
$aVariables = OA_Delivery_Cache_store_return($sName, $aVariables);
}
return $aVariables;
}
function MAX_cacheGetMaintenanceInfo($cached = true)
{
$cName  = OA_Delivery_Cache_getName(__FUNCTION__);
if (!$cached || ($output = OA_Delivery_Cache_fetch($cName, false, 3600)) === false) {
MAX_Dal_Delivery_Include();
$output = OA_Dal_Delivery_getMaintenanceInfo();
//        $interval = $GLOBALS['_MAX']['CONF']['maintenance']['operationInterval'];
$output = OA_Delivery_Cache_store_return($cName, $output);
}
return $output;
}
function MAX_cacheGetChannelLimitations($channelid, $cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__, $channelid);
if (!$cached || ($limitations = OA_Delivery_Cache_fetch($sName)) === false) {
MAX_Dal_Delivery_Include();
$limitations = OA_Dal_Delivery_getChannelLimitations($channelid);
$limitations = OA_Delivery_Cache_store_return($sName, $limitations);
}
return $limitations;
}
function MAX_cacheGetGoogleJavaScript($cached = true)
{
$sName  = OA_Delivery_Cache_getName(__FUNCTION__);
if (!$cached || ($output = OA_Delivery_Cache_fetch($sName)) === false) {
include MAX_PATH . '/lib/max/Delivery/google.php';
$output = MAX_googleGetJavaScript();
$output = OA_Delivery_Cache_store_return($sName, $output);
}
return $output;
}
// Set the viewer's remote information used in logging
MAX_remotehostProxyLookup();
MAX_remotehostReverseLookup();
MAX_remotehostSetClientInfo();
MAX_remotehostSetGeoInfo();
MAX_commonInitVariables();
MAX_cookieUnpackCapping();
function setupDeliveryConfigVariables()
{
if (!defined('MAX_PATH')) {
define('MAX_PATH', dirname(__FILE__).'/../..');
}
if ( !(isset($GLOBALS['_MAX']['CONF']))) {
$GLOBALS['_MAX']['CONF'] = parseDeliveryIniFile();
}
setupConfigVariables();
}
function setupIncludePath()
{
static $checkIfAlreadySet;
if (isset($checkIfAlreadySet)) {
return;
}
$checkIfAlreadySet = true;
$existingPearPath = ini_get('include_path');
$newPearPath = MAX_PATH . '/lib/pear';
if (!empty($existingPearPath)) {
$newPearPath .= PATH_SEPARATOR . $existingPearPath;
}
ini_set('include_path', $newPearPath);
}
$MAX_PLUGINS_AD_PLUGIN_NAME = 'MAX_type';
if(!isset($_GET[$MAX_PLUGINS_AD_PLUGIN_NAME])) {
echo $MAX_PLUGINS_AD_PLUGIN_NAME . ' is not specified';
exit(1);
}
$tagName = $_GET[$MAX_PLUGINS_AD_PLUGIN_NAME];
$tagFileName = MAX_PATH . '/plugins/invocationTags/'.$tagName.'/'.$tagName.'.delivery.php';
if(!file_exists($tagFileName)) {
echo 'Invocation plugin delivery file "' . $tagFileName . '" doesn\'t exists';
exit(1);
}
include $tagFileName;


?>
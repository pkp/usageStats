<?php

/**
 * @file plugins/generic/usageStats/GeoLocationTool.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class GeoLocationTool
 * @ingroup plugins_generic_usageStats
 *
 * @brief Geo location by ip wrapper class.
 */

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

class GeoLocationTool {

	var $_geoLocationTool;

	var $_regionName;

	var $_isDbFilePresent;

	/**
	 * Constructor.
	 * If we cannot find the database file, an empty object will be constructed.
	 * Use the method isPresent() to check if the database file is present before use.
	 */
	function __construct() {
		$geoLocationDbFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "GeoLite2-City.mmdb";
		if (file_exists($geoLocationDbFile)) {
			$isDbFilePresent = true;
			$this->_geoLocationTool = new Reader($geoLocationDbFile);
			include('lib' . DIRECTORY_SEPARATOR . 'geoIp' . DIRECTORY_SEPARATOR . 'geoipregionvars.php');
			$this->_regionName = $GEOIP_REGION_NAME;
		} else {
			$isDbFilePresent = false;
		}

		$this->_isDbFilePresent = $isDbFilePresent;
	}

	//
	// Public methods.
	//
	/**
	 * Identify if the geolocation database tool is available for use.
	 * @return boolean
	 */
	function isPresent() {
		return $this->_isDbFilePresent;
	}

	/**
	 * Return country code and city name for the passed
	 * ip address.
	 * @param $ip string
	 * @return array
	 */
	function getGeoLocation($ip) {
		// If no geolocation tool, the geo database file is missing.
		if (!$this->_geoLocationTool) return array(null, null, null);

		try {
			$record = $this->_geoLocationTool->city($ip);
		} catch (GeoIp2\Exception\AddressNotFoundException $e) {
			return array(null, null, null);
		}
		
		$regionIsoCode = $record->mostSpecificSubdivision->isoCode;
		if (strlen($regionIsoCode) > 2) $regionIsoCode = '';

		return array(
			$record->country->isoCode,
			utf8_encode($record->city->name),
			$regionIsoCode
		);
	}

	/**
	 * Get all country codes.
	 * @return mixed array or null
	 */
	function getAllCountryCodes() {
		if (!$this->_geoLocationTool) return null;

		$countryCodes = array(
			"","AP","EU","AD","AE","AF","AG","AI","AL","AM","CW",
			"AO","AQ","AR","AS","AT","AU","AW","AZ","BA","BB",
			"BD","BE","BF","BG","BH","BI","BJ","BM","BN","BO",
			"BR","BS","BT","BV","BW","BY","BZ","CA","CC","CD",
			"CF","CG","CH","CI","CK","CL","CM","CN","CO","CR",
			"CU","CV","CX","CY","CZ","DE","DJ","DK","DM","DO",
			"DZ","EC","EE","EG","EH","ER","ES","ET","FI","FJ",
			"FK","FM","FO","FR","SX","GA","GB","GD","GE","GF",
			"GH","GI","GL","GM","GN","GP","GQ","GR","GS","GT",
			"GU","GW","GY","HK","HM","HN","HR","HT","HU","ID",
			"IE","IL","IN","IO","IQ","IR","IS","IT","JM","JO",
			"JP","KE","KG","KH","KI","KM","KN","KP","KR","KW",
			"KY","KZ","LA","LB","LC","LI","LK","LR","LS","LT",
			"LU","LV","LY","MA","MC","MD","MG","MH","MK","ML",
			"MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV",
			"MW","MX","MY","MZ","NA","NC","NE","NF","NG","NI",
			"NL","NO","NP","NR","NU","NZ","OM","PA","PE","PF",
			"PG","PH","PK","PL","PM","PN","PR","PS","PT","PW",
			"PY","QA","RE","RO","RU","RW","SA","SB","SC","SD",
			"SE","SG","SH","SI","SJ","SK","SL","SM","SN","SO",
			"SR","ST","SV","SY","SZ","TC","TD","TF","TG","TH",
			"TJ","TK","TM","TN","TO","TL","TR","TT","TV","TW",
			"TZ","UA","UG","UM","US","UY","UZ","VA","VC","VE",
			"VG","VI","VN","VU","WF","WS","YE","YT","RS","ZA",
			"ZM","ME","ZW","A1","A2","O1","AX","GG","IM","JE",
			"BL","MF", "BQ"
		);

		// Overwrite the first empty record with the code to
		// unknow country.
		$countryCodes[0] = STATISTICS_UNKNOWN_COUNTRY_ID;
		return $countryCodes;
	}


	/**
	 * Get regions by country.
	 * @param $countryId int
	 * @return array
	 */
	function getRegions($countryId) {
		$regions = array();
		$database = $this->_regionName;
		if (isset($database[$countryId])) {
			$regions = $database[$countryId];
		}

		return $regions;
	}
}


<?php
/**
 * EOD Data SOAP Client
 * Prime Outsourcing
 * Date Started: February 16, 2012
 * Version: 0.1
 */

# Configuration - use a valid TOKEN
define("MAHIWAGANG_TOKEN", "019C205P0X1S");

# be ready for large data
set_time_limit(0);

require_once('nusoap/lib/nusoap.php');
$proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
$proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
$proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
$proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';

# Instantiate a SOAP client
$client = new nusoap_client('http://ws.eoddata.com/data.asmx?wsdl', 
		'wsdl',
		$proxyhost, 
		$proxyport, 
		$proxyusername, 
		$proxypassword);

if ( $client->getError() ) { # defensive programming
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	exit;
}

########################
## EODDATA CLIENT API ##
########################

/**
 * CountryList SOAP Call
 * @return Array of CountryList
 */
function eoddata_country_list(){
	global $client;	
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN);
	$result = $client->call('CountryList', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['CountryListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error 
	$list_raw = $result['CountryListResult']['COUNTRIES']['CountryBase'];	
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * DataClientLatestVersion SOAP Call
 * @return string|Version Number
 */
function eoddata_data_client_latest_version(){
	global $client;	
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN);
	$result = $client->call('DataClientLatestVersion', array('parameters' => $param), '', '', false, true);	
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;	
	
	# Check API Call's result
	$message = $result['DataClientLatestVersionResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	$version = $result['DataClientLatestVersionResult']['VERSION'];
	return $version;
}

/**
 * DataFormats Call 
 * TODO: Complex Array Data Structure
 */
function eoddata_data_formats(){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN);
	$result = $client->call('DataFormats', array('parameters' => $param), '', '', false, true);
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	echo "<pre>";print_r($result);
	# TODO: what shall you return?
}

/**
 * ExchangeGet SOAP Call
 * @param String $exchange
 * @return string|multitype:Single Exchange Data
 */
function eoddata_exchange_get($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('ExchangeGet', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['ExchangeGetResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['ExchangeGetResult']['EXCHANGE'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * ExchangeList SOAP Call
 * @return Array of ExchangeList
 */
function eoddata_exchange_list(){
	global $client;	
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN);
	$result = $client->call('ExchangeList', array('parameters' => $param), '', '', false, true);	
	
	if ($client->fault) return 'Fault';		// Check for a fault	
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['ExchangeListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['ExchangeListResult']['EXCHANGES']['EXCHANGE'];	
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * ExchangeMonths SOAP Call
 * @param String $exchange
 * @return Integer
 */
function eoddata_exchange_months($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('ExchangeMonths', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['ExchangeMonthsResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	$months = $result['ExchangeMonthsResult']['MONTHS'];
	return $months;
}

/**
 * FundamentalList SOAP Call
 * @param String $exchange
 * @return Array
 */
function eoddata_fundamental_list($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('FundamentalList', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['FundamentalListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['FundamentalListResult']['FUNDAMENTALS']['FUNDAMENTAL'];
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * Login SOAP Call
 * @param string $username
 * @param string $password
 */
function eoddata_login($username, $password){
	global $client;
	# SOAP Call
	$param = array('Username' => $username, 'Password' => $password);
	$result = $client->call('Login', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
// 	# Check API Call's result
// 	$message = $result['LoginResult']['!Message'];
// 	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['LoginResult'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * Login2 SOAP Call
 * @param string $username
 * @param string $password
 * @param string $version
 */
function eoddata_login2($username, $password, $version){
	global $client;
	# SOAP Call
	$param = array('Username' => $username, 'Password' => $password, 'Version' => $version);
	$result = $client->call('Login2', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
// 	# Check API Call's result
// 	$message = $result['Login2Result']['!Message'];
// 	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['Login2Result'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * Membership SOAP Call
 * @return string|Membership Type
 */
function eoddata_membership(){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN);
	$result = $client->call('Membership', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['MembershipResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	$membership = $result['MembershipResult']['MEMBERSHIP'];
	return $membership;
}

/**
 * QuoteGet SOAP Call
 * @param string $exchange
 * @param string $symbol
 */
function eoddata_quote_get($exchange, $symbol){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 'Symbol' => $symbol);
	$result = $client->call('QuoteGet', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteGetResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['QuoteGetResult']['QUOTE'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * QuoteList SOAP Call
 * @param string $exchange
 */
function eoddata_quote_list($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('QuoteList', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['QuoteListResult']['QUOTES']['QUOTE'];
	
	# prepare final list
	$final_list = prepare_final_list( $list_raw );
	return $final_list;
}

/**
 * QuoteList2 SOAP Call
 * @param string $exchange
 * @param string $symbols
 */
function eoddata_quote_list2($exchange, $symbols){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 'Symbols' => $symbols);
	$result = $client->call('QuoteList2', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteList2Result']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['QuoteList2Result']['QUOTES']['QUOTE'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * QuoteListByDate SOAP Call
 * @param string $exchange
 * @param integer $month
 * @param integer $day
 * @param integer $year
 */
function eoddata_quote_list_by_date($exchange, $month, $day, $year){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 
			'QuoteDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)));
	$result = $client->call('QuoteListByDate', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteListByDateResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['QuoteListByDateResult']['QUOTES']['QUOTE'];
	
	# prepare final list
	$final_list = prepare_final_list( $list_raw );
	return $final_list;
}

/**
 * QuoteListByDate2 SOAP Call
 * @param string $exchange
 * @param integer $month
 * @param integer $day
 * @param integer $year
 */
function eoddata_quote_list_by_date2($exchange, $month, $day, $year){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange,
			'QuoteDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)));
	$result = $client->call('QuoteListByDate2', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteListByDate2Result']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['QuoteListByDate2Result']['QUOTES2']['QUOTE2'];
	
	# prepare final list
	$final_list = prepare_final_list( $list_raw );
	return $final_list;
}

/**
 * QuoteListByDatePeriod SOAP Call
 * @param string $exchange
 * @param integer $month
 * @param integer $day
 * @param integer $year
 * @param integer $period
 * @return string
 */
function eoddata_quote_list_by_date_period($exchange, $month, $day, $year, $period){
	global $client;
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 
			'QuoteDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)),
			'Period' => $period);
	$result = $client->call('QuoteListByDatePeriod', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteListByDatePeriodResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	$data_raw = $result['QuoteListByDatePeriodResult']['QUOTES']['QUOTE'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * QuoteListByDatePeriod2 SOAP Call
 * @param string $exchange
 * @param integer $month
 * @param integer $day
 * @param integer $year
 * @param integer $period
 * @return string
 */
function eoddata_quote_list_by_date_period2($exchange, $month, $day, $year, $period){
	global $client;
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 
			'QuoteDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)),
			'Period' => $period);
	$result = $client->call('QuoteListByDatePeriod2', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['QuoteListByDatePeriod2Result']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	$data_raw = $result['QuoteListByDatePeriod2Result']['QUOTES2']['QUOTE2'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * SplitListByExchange SOAP Call
 * @param string $exchange
 */
function eoddata_split_list_by_exchange($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('SplitListByExchange', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SplitListByExchangeResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SplitListByExchangeResult']['SPLITS']['SPLIT'];
	
	# prepare final list
	$final_list = prepare_final_list( $list_raw );
	return $final_list;
}

/**
 * TODO: SplitListBySymbol SOAP Call
 * @param string $exchange
 * @param string $symbol
 */
function eoddata_split_list_by_symbol($exchange, $symbol){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 'Symbol' => $symbol);
	$result = $client->call('SplitListBySymbol', array('parameters' => $param), '', '', false, true);
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SplitListBySymbolResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	debug($result);exit;
	# reaching this line means no fault and zero error
// 	$list_raw = $result['SplitListBySymbolResult']['SYMBOLS']['SYMBOL'];
// 	
// 	# prepare final list
// 	$final_list = prepare_final_list( $list_raw );
// 	return $final_list;
}

/**
 * SymbolChangesByExchange SOAP Call
 * @param string $exchange
 * @return string|Ambigous <multitype:, unknown>
 */
function eoddata_symbol_changes_by_exchange($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('SymbolChangesByExchange', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolChangesByExchangeResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolChangesByExchangeResult']['SYMBOLCHANGES']['SYMBOLCHANGE'];
	
	# prepare final list
	$final_list = prepare_final_list( $list_raw );
	return $final_list;
}

/**
 * SymbolChart SOAP Call
 * @param string $exchange
 * @param string $symbol
 * @return string|URL
 */
function eoddata_symbol_chart($exchange, $symbol){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 'Symbol' => $symbol);
	$result = $client->call('SymbolChart', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolChartResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$chart = $result['SymbolChartResult']['CHART'];
	return $chart;
}

/**
 * SymbolGet SOAP Call
 * @param string $exchange
 * @param string $symbol
 * @return string|URL
 */
function eoddata_symbol_get($exchange, $symbol){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange, 'Symbol' => $symbol);
	$result = $client->call('SymbolGet', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolGetResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$data_raw = $result['SymbolGetResult']['SYMBOL'];
	
	# prepare final data
	$final_data = prepare_final_data( $data_raw );
	return $final_data;
}

/**
 * SymbolHistory SOAP Call
 * @param string $exchange
 * @param string $symbol
 * @param integer $month
 * @param integer $day
 * @param integer $year
 * @return string|Ambigous <multitype:, unknown>
 */
function eoddata_symbol_history($exchange, $symbol, $month, $day, $year){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange,
			'Symbol' => $symbol,
			'StartDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)));
	$result = $client->call('SymbolHistory', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolHistoryResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolHistoryResult']['QUOTES']['QUOTE'];
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * SymbolHistoryPeriod SOAP Call
 * @param string $exchange
 * @param string $symbol
 * @param integer $month
 * @param integer $day
 * @param integer $year
 * @param mixed $period
 * @return string|Ambigous <multitype:, unknown>
 */
function eoddata_symbol_history_period($exchange, $symbol, $month, $day, $year, $period){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange,
			'Symbol' => $symbol,
			'StartDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)),
			'Period' => $period);
	$result = $client->call('SymbolHistoryPeriod', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolHistoryPeriodResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolHistoryPeriodResult']['QUOTES']['QUOTE'];

	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * TODO": fix BUG
 * @param unknown_type $exchange
 * @param unknown_type $symbol
 * @param unknown_type $start_month
 * @param unknown_type $start_day
 * @param unknown_type $start_year
 * @param unknown_type $end_month
 * @param unknown_type $end_day
 * @param unknown_type $end_year
 * @param unknown_type $period
 * @return string|Ambigous <multitype:, unknown>
 */
function eoddata_symbol_history_period_by_date_range($exchange, 
		$symbol, 
		$start_month, 
		$start_day, 
		$start_year,
		$end_month, 
		$end_day, 
		$end_year, 
		$period){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange,
			'Symbol' => $symbol,
			'StartDate' => date("Ymd", mktime(0, 0, 0, $start_month, $start_day, $start_year)),
			'EndDate' => date("Ymd", mktime(0, 0, 0, $end_month, $end_day, $end_year)),
			'Period' => $period);
	$result = $client->call('SymbolHistoryPeriodByDateRange', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolHistoryPeriodByDateRangeResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolHistoryPeriodByDateRangeResult']['QUOTES']['QUOTE'];
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * SymbolList SOAP Call
 * @param unknown_type $exchange
 */
function eoddata_symbol_list($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('SymbolList', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
		
	# Check API Call's result
	$message = $result['SymbolListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolListResult']['SYMBOLS']['SYMBOL'];
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * SymbolList2 SOAP Call
 * @param unknown_type $exchange
 * @return string|Ambigous <Ambigous, multitype:, unknown>
 */
function eoddata_symbol_list2($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('SymbolList2', array('parameters' => $param), '', '', false, true);
	
	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['SymbolList2Result']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;
	
	# reaching this line means no fault and zero error
	$list_raw = $result['SymbolList2Result']['SYMBOLS2']['SYMBOL2'];
	
	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * TechnicalList SOAP Call
 * @param unknown_type $exchange
 * @return string|Ambigous <Ambigous, multitype:, unknown>
 */
function eoddata_technical_list($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('TechnicalList', array('parameters' => $param), '', '', false, true);

	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['TechnicalListResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;

	# reaching this line means no fault and zero error
	$list_raw = $result['TechnicalListResult']['TECHNICALS']['TECHNICAL'];

	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * Top10Gains SOAP CALL
 * @param unknown_type $exchange
 */
function eoddata_top_10_gains($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('Top10Gains', array('parameters' => $param), '', '', false, true);

	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	# Check API Call's result
	$message = $result['Top10GainsResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;

	# reaching this line means no fault and zero error
	$list_raw = $result['Top10GainsResult']['QUOTES']['QUOTE'];

	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

/**
 * Top10Losses SOAP Call
 * @param unknown_type $exchange
 */
function eoddata_top_10_losses($exchange){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange);
	$result = $client->call('Top10Losses', array('parameters' => $param), '', '', false, true);

	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;

	# Check API Call's result
	$message = $result['Top10LossesResult']['!Message'];
	if($message !== 'Success') return 'Message: ' . $message;

	# reaching this line means no fault and zero error
	$list_raw = $result['Top10LossesResult']['QUOTES']['QUOTE'];

	# prepare final list
	$final_list = prepare_final_list($list_raw);
	return $final_list;
}

# UpdateDataFormat 


# ValidateAccess 
/**
 * ValidateAccess SOAP Call
 * @param string $exchange
 * @param string $symbol
 * @param integer $month
 * @param integer $day
 * @param integer $year
 * @param mixed $period
 * @return boolean
 */
function eoddata_validate_access($exchange, $symbol, $month, $day, $year, $period){
	global $client;
	# SOAP Call
	$param = array('Token' => MAHIWAGANG_TOKEN, 'Exchange' => $exchange,
			'Symbol' => $symbol,
			'QuoteDate' => date("Ymd", mktime(0, 0, 0, $month, $day, $year)),
			'Period' => $period);
	$result = $client->call('ValidateAccess', array('parameters' => $param), '', '', false, true);

	if ($client->fault) return 'Fault';		// Check for a fault
	$err = $client->getError();				// Check for errors
	if($err) return 'Error: ' . $err;
	
	$message = $result['ValidateAccessResult']['!Message'];
	return ($message !== "Success");
}

######################
## HELPER FUNCTIONS ##
######################

/**
 * Prepare final list
 * @return Ambigous <multitype:, unknown>
 */
function prepare_final_list($list_raw){
	# fetch the headers first
	$headers_raw = array_keys( $list_raw[0] );
	# clean the header names
	$headers = remove_exclamation_infront_headers( $headers_raw );
	$final_list = array();
	$count = 0;
	foreach($list_raw as $item){
		foreach($headers as $header){
			$final_list[$count][$header] = $item["!" . $header];
		}
		$count++;
	}
	return $final_list;
}

/**
 * Prepare final data
 * @param Array $data_raw
 */
function prepare_final_data($data_raw){
	# fetch the headers first
	$headers_raw = array_keys( $data_raw );
	# clean the header names
	$headers = remove_exclamation_infront_headers( $headers_raw );
	$final_data = array();
	foreach($headers as $header){
		$final_data[$header] = $data_raw["!" . $header];
	}
	return $final_data;
}

/**
 * This function cleans the header names by removing the Exclamation Points infornt
 * @param Array $headers
 */
function remove_exclamation_infront_headers($headers){
	for($i = 0; $i < count($headers); $i++){		
		$header_name = $headers[$i];
		# if the first character of the string is '!', remove it
		if(substr($header_name, 0,1) === '!'){
			$headers[$i] = substr($header_name, 1);
		}
	}
	return $headers;
}

/**
 * Saves the 2DArray data to a specified CSV file
 * @param 2DArray $data_array
 * @param String $filename
 */
function convert_array_to_csv($data_array, $filename){
	$headers = array();
	if(isset($data_array[0])){
		$headers = array_keys($data_array[0]);
		$content = implode( ",", $headers ) . "\r\n";
		for($i = 0; $i < count($data_array); $i++){
			$line = implode( ",", $data_array[$i] ) . "\r\n";
			$content .= $line;
		}
	} else {
		$headers = array_keys($data_array);
		$content = implode( ",", $headers ) . "\r\n";
		$line = implode( ",", $data_array ) . "\r\n";
		$content .= $line;
	}
	
	$handle = fopen($filename, 'w');
	fwrite($handle, $content);
	fclose($handle);
}

/**
 * dump $res
 * @param array/object $res
 */
function debug($res){
	echo "<pre>";
	print_r($res); 
	echo "</pre>";
}

############
## TESTERS ##
############
// echo"<pre>";print_r($result);
// convert_array_to_csv(eoddata_fundamental_list('AMEX'), 'AMEX-FLIST.csv')
// echo eoddata_fundamental_list('AMEX');
// debug( eoddata_login('eoddatahappyday','eoddatahappyday') );
// echo eoddata_data_client_latest_version();
// $version = eoddata_data_client_latest_version();
// debug( eoddata_login2('eoddatahappyday','eoddatahappyday', $version) );
// debug( eoddata_membership());     
// debug( eoddata_quote_list2('NASDAQ', 'MSFT') );
debug(eoddata_validate_access("NASDAQ", "MSFT", 1, 1, 2011, "m") );
?>
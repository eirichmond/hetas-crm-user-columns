<?php
/**
 * Plugin Name:     Hetas Crm User Columns
 * Plugin URI:      https://hetas.co.uk
 * Description:     Adds useful user information relating to CRM data
 * Author:          Elliott Richmond
 * Author URI:      https://squareone.software
 * Text Domain:     hetas-crm-user-columns
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Hetas_Crm_User_Columns
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('VERSION', '0.1.0');

function enqueue_scripts() {
	// global $pagenow;
	// if($pagenow == 'users.php') {
		wp_enqueue_script( 'hetas-crm-user-columns', plugin_dir_url( __FILE__ ) . 'js/hetas-crm-user-columns.js', array( 'jquery' ), VERSION, true );
		wp_localize_script( 'hetas-crm-user-columns', 'hetascrm', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nextNonce' => wp_create_nonce( 'hetas-nonce' ) ) );
	//}
}
add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

function map_statecodes($statecode) {
	$statecodes = array(
		'0' => 'Active',
		'1' => 'Inactive'
	);
	return $statecodes[$statecode];
}

function map_statuscodes($statuscode) {
	$statuscodes = array(
		'1' => 'Applicant',
		'806070002' => 'Live',
		'806070006' => 'Live 3 month PRA',
		'806070011' => 'Awaiting PRA',
		'806070003' => 'Lapsed',
		'806070007' => 'Suspended',
		'806070008' => 'Payment Overdue',
		'806070009' => 'DD Overdue',
		'806070010' => 'DD Stopped',
		'2' => 'Expired',
		'806070000' => 'Cancelled',
		'806070004' => 'Suspended'
	);
	return $statuscodes[$statuscode];
}

function map_scheme_type($scheme_type) {
	$scheme_types = array(
		'0' => 'HETAS CPS England And Wales',
		'1' => 'HETAS CPS Scotland',
		'11' => 'HETAS CPS Isle of Man',
		'12' => 'HETAS CPS Channel Islands',
		'13' => 'HETAS CPS Isles of Scilly',
		'30' => 'HETAS CPS (Northern Ireland)',
		'2' => 'HETAS Registered Installer Scheme Ireland',
		'3' => 'HETAS Approved Servicing',
		'4' => 'HETAS Approved Retailer',
		'32' => 'HETAS Approved Retailer (Republic of Ireland)',
		'5' => 'HETAS Approved Chimney Sweep (RA)',
		'42' => 'HETAS Approved Chimney Sweep DE (E & W)',
		'43' => 'HETAS Approved Chimney Sweep DE (S & NI)',
		'44' => 'HETAS Approved Chimney Sweep DE - DR',
		'45' => 'HETAS Approved Chimney Sweep DE - ST',
		'46' => 'HETAS Approved Chimney Sweep DE - NVQ',
		'33' => 'HETAS Approved Chimney Sweep (Republic of Ireland)',
		'41' => 'Chimney Sweeps Sweep Safe Ltd',
		'22' => 'HETAS Approved Manufacturers',
		'23' => 'HETAS Approved MCS Product',
		'6' => 'Woodsure',
		'20' => 'MCS - 1 Technology',
		'21' => 'MCS - 2 Technology',
		'7' => 'Ready To Burn',
		'40' => 'Woodsure & Ready To Burn',
		'100000000' => 'HETAS Domestic Biomass Maintenance Scheme',
		'100000001' => 'HETAS Commercial Biomass Maintenance Scheme',
		'8' => 'ENplus® Trader',
		'50' => 'ENplus® Producer',
		'51' => 'ENplus® Service Provider',
		'9' => 'BSL',
		'10' => 'Grown In Britain'
	);
	return $scheme_types[$scheme_type];
}

/**
* Add an ajax search.
*/
function get_scheme_data() {
	// Handle request then generate response using WP_Ajax_Response

	$nonce = $_POST['nextNonce'];
	if ( ! wp_verify_nonce( $nonce, 'hetas-nonce' ) ) {
		die ( 'Busted!' );
	}
   
	$call = new Dynamics_crm('crm','1.1.0');
	$scheme = $call->get_scheme_by_id($_POST['scheme_id']);
	$scheme_data = array();
	foreach($scheme->value as $hscheme) {
		$scheme_data['van_name'] = $hscheme->van_name;
		$scheme_data['state'] = map_statecodes($hscheme->statecode);
		$scheme_data['status'] = map_statuscodes($hscheme->statuscode);
		$scheme_data['van_webcreditenabled'] = $hscheme->van_webcreditenabled;
		$scheme_data['van_webcreditbalance'] = $hscheme->van_webcreditbalance;
		$scheme_data['van_schemetype'] = map_scheme_type($hscheme->van_schemetype);
	}

	wp_send_json( $scheme_data );
	// Don't forget to stop execution afterward.
	wp_die();
}

add_action( 'wp_ajax_nopriv_get_scheme_data', 'get_scheme_data' );
add_action( 'wp_ajax_get_scheme_data', 'get_scheme_data' );

/**
* Add an ajax search.
*/
function get_business_data() {
	// Handle request then generate response using WP_Ajax_Response

	$nonce = $_POST['nextNonce'];
	if ( ! wp_verify_nonce( $nonce, 'hetas-nonce' ) ) {
		die ( 'Busted!' );
	}
   
	$call = new Dynamics_crm('crm','1.1.0');
	$business = $call->get_business_by_accountid_refined($_POST['business_id']);
	$business_data = array();
	foreach($business->value as $hbusiness) {
		$business_data['name'] = $hbusiness->name;
		$business_data['van_hetasid'] = $hbusiness->van_hetasid;
		$business_data['state'] = map_statecodes($hbusiness->statuscode);
		$business_data['address1_composite'] = $hbusiness->address1_composite;
		$business_data['address1_line1'] = $hbusiness->address1_line1;
		$business_data['address1_line2'] = $hbusiness->address1_line2;
		$business_data['address1_line3'] = $hbusiness->address1_line3;
		$business_data['address1_city'] = $hbusiness->address1_city;
		$business_data['address1_stateorprovince'] = $hbusiness->address1_stateorprovince;
		$business_data['address1_postalcode'] = $hbusiness->address1_postalcode;
		$business_data['address1_country'] = $hbusiness->address1_country;
		$business_data['telephone1'] = $hbusiness->telephone1;
	}

	wp_send_json( $business_data );
	// Don't forget to stop execution afterward.
	wp_die();
}

add_action( 'wp_ajax_nopriv_get_business_data', 'get_business_data' );
add_action( 'wp_ajax_get_business_data', 'get_business_data' );


// add a new column to users list
function new_modify_user_table( $column ) {
    $column['scheme'] = 'Scheme';
    $column['business'] = 'Business';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

// populate new column with data
function new_modify_user_table_row( $val, $column_name, $user_id ) {
	$scheme_id = get_user_meta( $user_id, '_van_schemeid_value', true );
	$businessid_id = get_user_meta( $user_id, '_van_businessid_value', true );
    switch ($column_name) {
        case 'scheme' :
            return '<div id="scheme-info-user-'.$user_id.'"><span class="spinner"></span><a class="button-secondary get-scheme" href="#" title="Get Scheme Data" data-scheme_id="'.$scheme_id.'">Get Scheme Data</a></div>';
			
		case 'business' :
            return '<div id="business-info-user-'.$user_id.'"><span class="spinner"></span><a class="button-secondary get-business" href="#" title="Get Business Data" data-business_id="'.$businessid_id.'">Get Business Data</a></div>';
		default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );
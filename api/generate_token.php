<?php
// Get our helper functions
require_once("inc/functions.php");

// Set variables for our request
$api_key = "4708875fe1878f8e7d57f835b3e4a4d1";
$shared_secret = "9ac6ba84f121b1bb791472368ac3f2bb";
$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

	// Set variables for our request
	$query = array(
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $params['code'] // Grab the access key from the URL
	);

	// Generate access token URL
	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	// Configure curl client and execute request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$shopify_result = curl_exec($ch);
	curl_close($ch);

	// Store the access token
	$shopify_result = json_decode($shopify_result, true);
	$access_token = $shopify_result['access_token'];
	$store_url = $params['shop'];


    $shop_api_url = "https://". $store_url . "/admin/api/2023-01/shop.json";
    $ch = curl_init($shop_api_url);
    curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
    'X-Shopify-Access-Token: ' . $access_token,
    )
    ));

    // Send the request
    $shop_detail_response = json_decode(curl_exec($ch), true);
    $shop_detail_response = $shop_detail_response["shop"];

    $create_user_post_url = "https://apps.returnx.io/api/1.1/wf/add-merchant";
    // // create a password by obfuscating access_token using sha256
    $password = hash('sha256', $access_token);
    $website_url_shortened = substr($shop_detail_response['myshopify_domain'], 0, -14);
    // // Make post request to create_user_post_url with email and password as parameters
    $data = array(
        'email' =>  $shop_detail_response['email'],
        'support_email' => $shop_detail_response['customer_email'],
        'merchant_currency' => $shop_detail_response['currency'],
        'merchant_name' => $shop_detail_response['name'],
        'token' => $access_token,
        'user_id' => $website_url_shortened,
        'token_digest' => $password
    );

    $ch = curl_init($create_user_post_url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 3c6da2551638a0b1ce21d7757a235430',
            'Content-Type: application/x-www-form-urlencoded',
        ),
        CURLOPT_POSTFIELDS => http_build_query($data)
    ));

    
    // Send the request
    $response = curl_exec($ch);

    // Check for errors
    if($response === FALSE){
    };
    $response = json_decode($response, true);
    if(isset($response["statusCode"]) && $response["statusCode"] === 400){
        // User creation failed
        // If user creation failed because of already existing user, then redirect to login

        if ($response["reason"] == "USED_EMAIL") {
            // Base URL for redirection
            $redirectUrl = "https://apps.returnx.io?email=" . $shop_detail_response['email'] . "&uid=" . urlencode($password);

            // Check if 'rxref' parameter is set and append it if it exists
            if (isset($_GET['rxref'])) {
                $rxref = $_GET['rxref'];
                $redirectUrl .= "&rxref=" . urlencode($rxref);
            }

            // Redirect
            header("Location: " . $redirectUrl);
            die();
        } else {
            echo('An error occurred creating user');
            print_r($response);

            die();
        }
    }

    $redirect_url = "https://apps.returnx.io?email=";
    // add code as GET parameter to redirect url
    $redirect_url = $redirect_url . $shop_detail_response['email'] . "&uid=" . $password;
    // Redirect user
    if (isset($_GET['rxref'])) {
        $rxref = $_GET['rxref'];
        $redirect_url .= "&rxref=" . urlencode($rxref);
    }
    header("Location: $redirect_url");
    die();



} else {
	// Someone is trying to be shady!
	die('This request is NOT from Shopify!');
}

<?php

// Set variables for our request
$shop = $_GET['shop'];
$api_key = "4708875fe1878f8e7d57f835b3e4a4d1";
$scopes = "write_orders, read_all_orders, read_orders, write_order_edits, read_order_edits, write_merchant_managed_fulfillment_orders, read_merchant_managed_fulfillment_orders, write_assigned_fulfillment_orders, read_assigned_fulfillment_orders, read_customers, read_products, write_shipping, read_shipping, read_product_listings, read_analytics, read_discounts, read_files, write_fulfillments, read_fulfillments, read_inventory, read_locations, read_payment_terms, read_locales, read_markets, read_shopify_payments_accounts, write_third_party_fulfillment_orders, read_third_party_fulfillment_orders, write_inventory, write_returns, read_returns, read_gift_cards, write_gift_cards, read_price_rules, read_reports, read_content, write_content, read_themes, write_themes, read_script_tags, write_script_tags, write_price_rules, write_draft_orders, read_draft_orders";

// Base redirect URI
$redirect_uri = "https://installs.returnx.io/generate_token.php";

// Check if 'rxref' parameter is set and append it to the redirect_uri
if (isset($_GET['rxref'])) {
    $rxref = $_GET['rxref'];
    $redirect_uri .= "?rxref=" . urlencode($rxref);
}

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();

?>
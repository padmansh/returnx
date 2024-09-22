<?php 

// Get 'shop' parameter from URL
$shop = $_GET['shop'];

// Remove '.myshopify.com' (14 chars)
$cutUrl = substr($shop, 0, -14);

// Base URL for redirection
$redirectUrl = "https://returnx.vercel.app/api/install.php?shop=" . urlencode($cutUrl);

// Check if 'rxref' parameter is set and append it if it exists
if (isset($_GET['rxref'])) {
    $rxref = $_GET['rxref'];
    $redirectUrl .= "&rxref=" . urlencode($rxref);
}

// Redirect to install.php with the appropriate parameters
header("Location: " . $redirectUrl);
?>
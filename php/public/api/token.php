<?php
// Start or resume the session
session_start();
header('Content-type: application/json');
// Function to generate a random alphanumerical string of length 10
function generateToken($length = 32) {
    // Characters to be used in the token
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    // Generate the token
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

// Check if the session variable 'session_transport_token' is already set
if (isset($_SESSION['session_transport_token'])) {
    // Return the existing token
    echo '{"key":"' . $_SESSION['session_transport_token'] . '"}';
    $_SESSION['session_transport_token'] = $_SESSION['session_transport_token'];
} else {
    // Generate a new token
    $newToken = generateToken();
    
    // Store the token in the session
    $_SESSION['session_transport_token'] = $newToken;
    
    // Return the new token
    echo '{"key":"' . $newToken . '"}';
}
?>
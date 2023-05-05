<?php
function create_secure_hash($source_string, $salt)
{
    // Combine the source string and salt
    $combined_string = $salt . $source_string . $salt;

    // Create the secure hash using the SHA-256 algorithm
    $secure_hash = hash("sha256", $combined_string);

    return $secure_hash;
}

function create_simple_hash($source_string)
{
    // Create the simple hash using the SHA-256 algorithm
    $secure_hash = hash("sha256", $source_string);

    return $secure_hash;
}

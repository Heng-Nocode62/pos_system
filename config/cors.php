<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://your-frontend.onrender.com', // Your deployed frontend
        'http://localhost:5173',               // Local development
    ],

    'allowed_origins_patterns' => [],

    // Make sure 'Authorization' is permitted in headers
    'allowed_headers' => ['*'], 

    // Explicitly expose Authorization if your API returns updated tokens in headers
    'exposed_headers' => ['Authorization'],

    'max_age' => 86400, // Cache preflight response for 24 hours to speed up requests

    // Set to true ONLY if storing JWT in HTTP-Only cookies. 
    // Set to false if sending Bearer token via Authorization header.
    'supports_credentials' => false, 
];

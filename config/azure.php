<?php

return [
    'document_intelligence' => [
        'endpoint' => env('AZURE_DOCUMENT_ENDPOINT'),
        'api_key' => env('AZURE_DOCUMENT_API_KEY'),
    ],
    'computer_vision' => [
        'endpoint' => env('AZURE_VISION_ENDPOINT', env('AZURE_DOCUMENT_ENDPOINT')), // Stesso endpoint
        'api_key' => env('AZURE_VISION_API_KEY', env('AZURE_DOCUMENT_API_KEY')), // Stessa chiave
    ],
];

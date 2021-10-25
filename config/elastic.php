<?php

$hosts = [
    '127.0.0.1:9200',   // IP + Port
    '127.0.0.1',    // Just IP
    'mydomain.server.com:9201', // Domain + Port
    'mydomain2.server.com',     // Just Domain
    'https://localhost',        // SSL to localhost
    'https://127.0.0.1:9200'  // SSL to IP + Port
];

return ['hosts' => $hosts];

?>
<?php

/**
 * Example of parsing a valid key
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

$generator = new KeyGenerator('xyz_sandbox');
$apiKey    = $generator->parse('xyz_sandbox_PudLoQjP_N227Oh5hz48h4FQM_e07f9ca3');
if ($apiKey === null) {
    echo "Invalid key!\n";
} else {
    echo "Identifier : " . $apiKey->getIdentifier() . "\n";
    echo "Secret     : " . $apiKey->getSecret() . "\n";
}

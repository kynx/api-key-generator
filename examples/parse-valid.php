<?php

/**
 * Example of parsing a valid key
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

$generator = new KeyGenerator('xyz_sandbox');
$apiKey    = $generator->parse('xyz_sandbox_miWh6l3ftyzi9TRmpZeJ4nU3LpBF5T37FguT1p4y_dab13e9d');
if ($apiKey === null) {
    echo "Invalid key!\n";
} else {
    echo "Identifier : " . $apiKey->getIdentifier() . "\n";
    echo "Secret     : " . $apiKey->getSecret() . "\n";
}

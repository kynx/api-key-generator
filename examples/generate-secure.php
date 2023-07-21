<?php

/**
 * £xample of generating key with extra entropy
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

$identifierLength = 8;
$secretLength     = 32;

$generator = new KeyGenerator('xyz_sandbox', $identifierLength, $secretLength);
$apiKey    = $generator->generate();
echo $apiKey->getKey() . "\n";

<?php

/**
 * Example of generating key with defaults
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

$generator = new KeyGenerator('xyz_sandbox');
$apiKey    = $generator->generate();
echo $apiKey->getKey() . "\n";

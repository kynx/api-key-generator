<?php

/**
 * Example of generating a key that uses extra characters
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;
use Kynx\ApiKey\RandomString;

require '../vendor/autoload.php';

$randomString = new RandomString(RandomString::DEFAULT_CHARACTERS . '!@$%^&*()./');

$generator = new KeyGenerator('xyz_sandbox', 8, 32, $randomString);
$apiKey    = $generator->generate();
echo $apiKey->getKey() . "\n";

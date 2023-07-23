<?php

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;
use Kynx\ApiKey\KeyGeneratorChain;

require '../vendor/autoload.php';

$newPrefix           = 'abc_sandbox';
$newIdentifierLength = 10;
$newSecretLength     = 48;

$oldPrefix           = 'xyz_sandbox';
$oldIdentifierLength = 8;
$oldSecretLength     = 32;

$primary  = new KeyGenerator($newPrefix, $newIdentifierLength, $newSecretLength);
$fallback = new KeyGenerator($oldPrefix, $oldIdentifierLength, $oldSecretLength);
$chain    = new KeyGeneratorChain($primary, $fallback);

$oldKey = $chain->parse('xyz_sandbox_miWh6l3ftyzi9TRmpZeJ4nU3LpBF5T37FguT1p4y_dab13e9d');
if ($oldKey === null) {
    echo "Invalid key!\n";
} else {
    echo "Identifier : " . $oldKey->getIdentifier() . "\n";
    echo "Secret     : " . $oldKey->getSecret() . "\n";
}

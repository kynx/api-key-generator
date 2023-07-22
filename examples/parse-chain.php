<?php

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;
use Kynx\ApiKey\KeyGeneratorChain;

require '../vendor/autoload.php';

$newPrefix           = 'abc_sandbox';
$newIdentifierLength = 10;
$newSecretLength     = 32;

$oldPrefix           = 'xyz_sandbox';
$oldIdentifierLength = 8;
$oldSecretLength     = 16;

$primary  = new KeyGenerator($newPrefix, $newIdentifierLength, $newSecretLength);
$fallback = new KeyGenerator($oldPrefix, $oldIdentifierLength, $oldSecretLength);
$chain    = new KeyGeneratorChain($primary, $fallback);

$oldKey = $chain->parse('xyz_sandbox_PudLoQjP_N227Oh5hz48h4FQM_e07f9ca3');
if ($oldKey === null) {
    echo "Invalid key!\n";
} else {
    echo "Identifier : " . $oldKey->getIdentifier() . "\n";
    echo "Secret     : " . $oldKey->getSecret() . "\n";
}

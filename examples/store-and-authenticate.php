<?php

/**
 * Example of securely storing key in a database and then authenticating key
 *
 * Table structure (MySQL):
 *
 * CREATE TABLE apikeys (
 *     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     identifier VARCHAR(8) NOT NULL,
 *     hash VARCHAR(255) NOT NULL,
 *     UNIQUE INDEX apikeys_identity_ux (identifier)
 * )
 */

declare(strict_types=1);

use Kynx\ApiKey\ApiKey;
use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

$pdo = new PDO('mysql:host=127.0.0.1;dbname=api', 'apiuser', 'apipass');

$generator = new KeyGenerator('xyz_sandbox');
$insert    = $pdo->prepare('INSERT INTO apikeys (identifier, hash) VALUES (:identifier, :hash)');

// store api identifier and hash of secret, retrying when duplicate key error occurs

$retries = 0;
while (true) {
    $apiKey = $generator->generate();
    $hash   = password_hash($apiKey->getSecret(), PASSWORD_DEFAULT);
    try {
        $insert->execute(['identifier' => $apiKey->getIdentifier(), 'hash' => $hash]);
        break;
    } catch (PDOException $e) {
        // check for unique constraint violation - mysql-specific
        if ($e->errorInfo[1] !== 1062 || $retries >= 9) {
            throw $e;
        }
    }
    $retries++;
}

assert($apiKey instanceof ApiKey);

$key = $apiKey->getKey();

// verify key is well-formed
$parsed = $generator->parse($key);
if ($parsed === null) {
    echo "Invalid key!\n";
    exit;
}

// validate apikey against stored hash

$select = $pdo->prepare('SELECT id, hash FROM apikeys WHERE identifier = :identifier');
$select->execute(['identifier' => $parsed->getIdentifier()]);
$row = $select->fetch();

if (password_verify($parsed->getSecret(), $row['hash'])) {
    echo "Authenticated ID " . $row['id'] . "\n";
} else {
    echo "Not authenticated\n";
}

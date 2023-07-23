<?php

/**
 * Test number of unique constraint collisions generating identities of different lengths
 */

declare(strict_types=1);

use Kynx\ApiKey\KeyGenerator;

require '../vendor/autoload.php';

function testCollisions(PDO $pdo, int $length, int $groupSize, int $numIdentities): void
{
    $pdo->exec('TRUNCATE TABLE apikeys');
    $generator = new KeyGenerator('test', $length, 24);
    $insert    = $pdo->prepare('INSERT INTO apikeys (identifier, hash) VALUES (:identifier, :hash)');
    $padSize   = strlen((string) $numIdentities);

    $collisions = 0;
    $group      = 0;
    $prev       = 0;
    $start      = microtime(true);

    for ($i = 0; $i < $numIdentities; $i++) {
        $group = (int) ceil($i / $groupSize);
        if ($group > $prev) {
            if ($prev > 0) {
                outputLine($i - 1, $collisions, $padSize, $start);
            }

            $collisions = 0;
            $prev       = $group;
            $start      = microtime(true);
        }

        $retries = 0;
        while (true) {
            $apiKey = $generator->generate();
            $hash   = 0;
//            $hash   = password_hash($apiKey->getSecret(), PASSWORD_DEFAULT);
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

        $collisions += $retries;
    }

    outputLine($i, $collisions, $padSize, $start);
}

function outputLine(int $group, int $collisions, int $padSize, float $start): void
{
    $percent = $collisions > 0 ? $group / $collisions * 100 : 0;
    echo sprintf(
        "    %" . $padSize . "d : %" . $padSize . "d %3d%% %01.2fs",
        $group,
        $collisions,
        $percent,
        microtime(true) - $start
    ) . "\n";
}

$pdo = new PDO('mysql:host=127.0.0.1;dbname=api', 'apiuser', 'apipass');

echo "Length = 8\n";
$collisions = testCollisions($pdo, 8, 1000, 1000000);
echo "\n";

# kynx/api-key-generator

[![Build Status](https://github.com/kynx/api-key-generator/workflows/Continuous%20Integration/badge.svg)](https://github.com/kynx/api-key-generator/actions?query=workflow%3A"Continuous+Integration")

Generate and parse well-formed API keys.


## Introduction

API keys are a common way to authenticate users accessing an API. While there isn't a standard saying how they should be
constructed, there are some best practices.

An API key should:

* Be secure (OK, that's a little obvious...)
* Not leak any details about the user identity it is tied to
* Be easy to store securely and validate against a persistence layer
* Be traceable in case some idiot checks one into VCS - for instance, via [GitHub's Secret Scanning]
* Provide a way to quickly reject obviously malformed keys without hitting the persistence layer
* Contain human-readable information so end users / support can easily see if they are using the right key
* Be easy to copy-and-paste from a UI into the consuming application

This library helps with this, providing an `ApiKeyGenerator` for generating and parsing keys, and an `ApiKey` object 
for working with the keys themselves. It does not provide help with storing and verifying the keys - tha part is up to 
you.

## Installation

```
composer require kynx/api-key-generator
```

## Usage

### Generating a key:

```php
use Kynx\ApiKey\KeyGenerator;

require 'vendor/autoload.php';

$generator = new KeyGenerator('xyz_sandbox');
$apiKey    = $generator->generate();
echo $apiKey->getKey() . "\n";
```

This will output something like: 

```
xyz_sandbox_PudLoQjP_N227Oh5hz48h4FQM_e07f9ca3
```

See the [examples](./examples) directory for code showing how to modify the key strength, get an obfusicated version of
the key, etc.

### Parsing a key:

```php
use Kynx\ApiKey\KeyGenerator;

require 'vendor/autoload.php';

$generator = new KeyGenerator('xyz_sandbox');
$apiKey    = $generator->parse('xyz_sandbox_PudLoQjP_N227Oh5hz48h4FQM_e07f9ca3');
if ($apiKey === null) {
    echo "Invalid key!\n";
} else {
    echo "Identifier : " . $apiKey->getIdentifier() . "\n";
    echo "Secret     : " . $apiKey->getSecret() . "\n";
}
```

Since the key is well-formed, this will output:

```
Identifier : PudLoQjP
Secret     : N227Oh5hz48h4FQM
```

Now make a random change to the key. The `parse()` method will return `null` and you will see the`Invalid key!`
message.


## API Key Structure

You will notice in the examples above that the generated key is composed of four parts separated by underscores. They 
are:

```
<prefix>_<identifier>_<secret>_<checksum>
```

### Prefix

The `prefix` is always the first argument passed to the constructor. It is there to make your key easy to recognise, 
both to end users and to secret scanners using a regex for find leaked keys in the wild. In the examples we've used a 
company identifier (`xyz`) plus a string indicating whether it's for use in our `production` or `sandbox` environments.
If you've ever integrated with Stripe you will be familiar with this pattern.

### Identifier

The `identifier` is a random string that can be used to look up the secret in a database. Store this un-hashed and put 
a unique constraint on it. The generated identifiers are _not_ guaranteed to be unique, so when inserting into the 
database your should be prepared to catch the constraint violation, generate a new key and re-try.

### Secret

The `secret` part provides the security. This _must_ be hashed (using PHP's [password_hash()]) before storing.

### Checksum

Finally the `checksum` is a `crc32b` hash of the rest of the key. It is there to quickly filter out garbage hitting your
API, without needing to bother your persistence layer. Just because it matches _does not_ mean the user is 
authenticated! You still need to check the identifier and secret against what you stored when the key was generated.

### Defaults

By default the `identifier` is 8 characters long. You can reduce the risk of collisions by increasing this. By default
the `secret` is 16 characters long. You can improve the security of your API by increasing this.  

By default the generated part of the key is composed of the characters `[a-zA-Z0-9_]` and you should ensure your 
`prefix` is too: this makes it easy to copy from your key management console. Try double-clicking on 
`xyz-sandbox_Pu!Lo&jP_N22/Oh5hz48h4.QM_e07f9ca3` to see what happens when other characters are present. If you want more
entropy, make your secret longer.


# Further reading

https://zuplo.com/blog/2022/12/01/api-key-authentication
https://blog.mergify.com/api-keys-best-practice/


[GitHub's Secret Scanning]: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
[password_hash()]: https://www.php.net/password_verify
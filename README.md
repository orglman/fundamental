# Orgelman Fundamental Scripts (OFS)
[![Build Status](https://travis-ci.org/orglman/fundamental.svg)](https://travis-ci.org/orglman/fundamental)
[![composer.lock available](https://poser.pugx.org/orglman/fundamental/composerlock)](https://packagist.org/packages/orglman/fundamental)

[![Latest Stable Version](https://poser.pugx.org/orglman/fundamental/v/stable.svg)](https://packagist.org/packages/orglman/fundamental)
[![Latest Unstable Version](https://poser.pugx.org/orglman/fundamental/v/unstable.svg)](https://packagist.org/packages/orglman/fundamental)
[![Latest Stable Version](https://img.shields.io/github/tag/orglman/fundamental.svg)](https://packagist.org/packages/orglman/fundamental)

[![star this repo](http://githubbadges.com/star.svg?user=orglman&repo=fundamental&style=flat)](https://github.com/orglman/fundamental)
[![fork this repo](http://githubbadges.com/fork.svg?user=orglman&repo=fundamental&style=flat)](https://github.com/orglman/fundamental/fork)
[![Pending Pull-Requests](http://githubbadges.herokuapp.com/orglman/fundamental/pulls.svg?style=flat)](https://github.com/orglman/fundamental/pulls)
[![Open Issues](http://githubbadges.herokuapp.com/orglman/fundamental/issues.svg?style=flat)](https://github.com/orglman/fundamental/issues)

[![Total Downloads](https://poser.pugx.org/orglman/fundamental/downloads)](https://packagist.org/packages/orglman/fundamental)

[![License](https://poser.pugx.org/orglman/fundamental/license.svg)](https://packagist.org/packages/orglman/fundamental)

A reposotory of fundamental features that are used in Orgelman's project.

## Install
OFS is available on [Packagist](https://packagist.org/packages/orglman/fundamental) (using semantic versioning), and installation via Composer is the recommended way to install OFS. Just add this line to your composer.json file:
```
"orglman/fundamental": "@dev"
```
or run
```
composer require phpmailer/phpmailer
```
Note that the vendor folder and the vendor/autoload.php script are generated by Composer; they are not part of OFS.

## Code Examples

### Forbid direct access
```
if((isset($_SERVER["SCRIPT_FILENAME"]))&&(basename(__FILE__)==basename($_SERVER["SCRIPT_FILENAME"]))){header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized',true,401);header('Status: '.'401 Unauthorized');header('Retry-After: 86400');die('<h1>401 Unauthorized</h1>');}
if(get_included_files()[0]==__FILE__){header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized', true, 401);header('Status: '.'401 Unauthorized');header('Retry-After: 86400');die('<h1>401 Unauthorized</h1>');}
```
### Getting script elapsed time 
```
$functions        = new orgelman\functions\Functions($root = null, $start = microtime(true));
echo $functions->timeElapsed();
```
### Hide emails
```
$functions        = new orgelman\functions\Functions();
echo $functions->obfuscate_email('test@example.com');
echo $functions->botTrap('test@example.com');
```

### Get user agent, IP and client info 
```
$functions        = new orgelman\functions\Functions();
print_r($functions->get_client());
echo $functions->get_client_ua();
echo $functions->get_client_ip();
```

### Encrypting and decrypting strings
```
$str              = 'message';
$encrypt          = new orgelman\security\encrypt($compress = 'true','sha256');
$encrypted        = $encrypt->encrypt($str, $key, $method = '')['encrypted'];

$decrypted        = $encrypt->decrypt($encrypted, $key, $method = '')['decrypted'];
```

### Test passwords
```
$hash             = new orgelman\security\hash($compress = 'true');
$password         = 'password';

$hash->setPasswordLenghtMin($num);
$hash->setPasswordLenghtMax($num);
$hash->setPasswordNumber($num);
$hash->setPasswordLetter($num);
$hash->setPasswordCapital($num);
$hash->setPasswordSymbol($num);

$test = $hash->test($password);
if($test!=true) {
   echo '<ul>';
   foreach(test as $error) {
      echo '<li>'.$error.'</li>';
   }
   echo '</ul>';
}
```
### Hashing and validating passwords
```
$password         = 'password';
$hash             = new orgelman\security\hash($compress = 'true');
$hash->setPasswordLenghtMin($num);
$hash->setPasswordLenghtMax($num);
$hash->setPasswordNumber($num);
$hash->setPasswordLetter($num);
$hash->setPasswordCapital($num);
$hash->setPasswordSymbol($num);

$hashedPass       = $hash->generate($password);
if(!is_string($hashedPass)) {
   echo '<ul>';
   foreach(hashedPass as $error) {
      echo '<li>'.$error.'</li>';
   }
   echo '</ul>';
} else {
  if($hash->valid(password, $hashedPass)) {
    echo 'Yay!';
  } else {
    echo 'Nay!';
  }
}
```
## Tests
There is a PHPUnit test script in the [test](https://github.com/orglman/fundamental/tree/master/test/) folder.

Build status: [![Build Status](https://travis-ci.org/orglman/fundamental.svg)](https://travis-ci.org/orglman/fundamental)

If this isn't passing, is there something you can do to help?

## Authors

* **Tobias Jonson** - *Developer* - [orglman](https://github.com/orglman)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

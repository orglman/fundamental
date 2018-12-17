# Orgelman Fundamental Scripts (OFS)
[![Build Status](https://travis-ci.org/orglman/fundamental.svg)](https://travis-ci.org/orglman/fundamental)

[![Latest Stable Version](https://poser.pugx.org/orglman/fundamental/v/stable.svg)](https://packagist.org/packages/orglman/fundamental) 
[![Latest Unstable Version](https://poser.pugx.org/orglman/fundamental/v/unstable.svg)](https://packagist.org/packages/orglman/fundamental) 

[![star this repo](http://githubbadges.com/star.svg?user=orglman&repo=fundamental&style=flat)](https://github.com/orglman/fundamental)
[![fork this repo](http://githubbadges.com/fork.svg?user=orglman&repo=fundamental&style=flat)](https://github.com/orglman/fundamental/fork)
[![Pending Pull-Requests](http://githubbadges.herokuapp.com/orglman/fundamental/pulls.svg?style=flat)](https://github.com/orglman/fundamental/pulls)
[![Open Issues](http://githubbadges.herokuapp.com/orglman/fundamental/issues.svg?style=flat)](https://github.com/orglman/fundamental/issues)

[![Total Downloads](https://poser.pugx.org/orglman/fundamental/downloads)](https://packagist.org/packages/orglman/fundamental) 

[![License](https://poser.pugx.org/orglman/fundamental/license.svg)](https://packagist.org/packages/orglman/fundamental)

![forthebadge](https://forthebadge.com/images/badges/fuck-it-ship-it.svg)

A reposotory of good-to-have features that are used in Orgelman's project.

## Code Examples

### Forbid direct access
```
if(get_included_files()[0]==__FILE__){header("HTTP/1.1 403 Forbidden");die('<h1 style="font-family:arial;">Error 403: Forbidden</h1>');} 
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
$encrypt          = new orgelman\security\encrypt('sha256');
$encrypted        = $encrypt->encrypt($str, $key, $method = '')['encrypted'];

$decrypted        = $encrypt->decrypt($encrypted, $key, $method = '')['decrypted'];
```
### Hashing and validating passwords
```
$password         = 'password';
$hash             = new orgelman\security\hash();
$hashedPass       = $hash->generate($password);

if($hash->valid(password, $hashedPass)) {
  echo 'Yay!';
} else {
  echo 'Nay!';
}
```

## Authors

* **Tobias Jonson** - *Developer* - [orglman](https://github.com/orglman)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

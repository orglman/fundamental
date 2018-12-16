# Orgelman Fundamental Scripts (OFS)
[![Build Status](https://travis-ci.org/orglman/fundamental.svg)](https://travis-ci.org/orglman/fundamental)
[![Latest Stable Version](https://poser.pugx.org/orglman/fundamental/v/stable.svg)](https://packagist.org/packages/orglman/fundamental) [![Total Downloads](https://poser.pugx.org/orglman/fundamental/downloads)](https://packagist.org/packages/orglman/fundamental) [![Latest Unstable Version](https://poser.pugx.org/orglman/fundamental/v/unstable.svg)](https://packagist.org/packages/orglman/fundamental) [![License](https://poser.pugx.org/orglman/fundamental/license.svg)](https://packagist.org/packages/orglman/fundamental)

## Code Examples


### Encrypting and decrypting strings
```
$str        = 'message';
$encrypt    = new orgelman\security\encrypt('sha256');
$encrypted  = $encrypt->encrypt($str, $key, $method = '')['encrypted'];

$decrypted  = $encrypt->decrypt($encrypted, $key, $method = '');
```
### Hashing and validating passwords
```
$password   = 'password';
$hash       = new orgelman\security\hash();
$hashedPass = $hash->generate($password);

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

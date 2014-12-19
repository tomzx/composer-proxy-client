Composer Proxy Client
=========

[![Build Status](https://travis-ci.org/tomzx/composer-proxy-client.svg)](https://travis-ci.org/tomzx/composer-proxy-client)
[![Total Downloads](https://poser.pugx.org/tomzx/composer-proxy-client/downloads.svg)](https://packagist.org/packages/tomzx/composer-proxy-client)
[![Latest Stable Version](https://poser.pugx.org/tomzx/composer-proxy-client/v/stable.svg)](https://packagist.org/packages/tomzx/composer-proxy-client)
[![Latest Unstable Version](https://poser.pugx.org/tomzx/composer-proxy-client/v/unstable.svg)](https://packagist.org/packages/tomzx/composer-proxy-client)
[![License](https://poser.pugx.org/tomzx/composer-proxy-client/license.svg)](https://packagist.org/packages/tomzx/composer-proxy-client)

`Composer Proxy Client` is a plugin for composer that will send to [Composer Proxy](https://github.com/tomzx/composer-proxy) the list of require and require-dev of your composer.json. `Composer Proxy` will then add these dependencies to its cache, thus making it possible to use it in the same way you'd use satis to cache repository, but in a dynamic manner (instead of specifying by hand your dependencies).

Getting started
---------------

In your project composer.json, add the following

```
json
{
	"require": {
		"tomzx/composer-proxy-client": "0.1.*@dev"
	},
	...
	"scripts": {
		"pre-install-cmd": [
			"ComposerProxy\\Client\\Client::prepare"
		],
		"pre-update-cmd": [
			"ComposerProxy\\Client\\Client::prepare"
		]
	}
	"extra": {
		"composer-proxy": {
			"url": "url-to-composer-proxy"
		}
	}
}
```

**Note** Specifying the `pre-install-cmd` script is at your discretion.

This takes care of informing composer that you want to use `Composer Proxy Client` AND where your `Composer Proxy` server resides.

Note
----

This is a work in progress. `Composer Proxy Client` and `Composer Proxy` are not ready to be used in production.


License
-------

The code is licensed under the [MIT license](http://choosealicense.com/licenses/mit/). See license.txt.
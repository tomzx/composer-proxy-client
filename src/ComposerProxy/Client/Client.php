<?php

namespace ComposerProxy\Client;

use Arry\Arry;
use Composer\Script\Event;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Pool;

class Client
{
	public static function prepare(Event $event)
	{
		$composer = $event->getComposer();
		$io = $event->getIO();

		// TODO: Remove this hack when composer supports loading autoload.files for plugins
		$vendorDir = $composer->getConfig()->get('vendor-dir');
		$composerDir = $vendorDir . '/composer';

		$includeFiles = require $composerDir . '/autoload_files.php';
		foreach ($includeFiles as $file) {
			require $file;
		}
		// End of hack

		/** @var \Composer\Package\Package $package */
		$package = $composer->getPackage();

		$dependencies = [];

		$requires = $package->getRequires();
		/** @var \Composer\Package\Link $require */
		foreach ($requires as $require) {
			$dependencies[$require->getTarget()] = $require->getPrettyConstraint();
		}

		$devRequires = $package->getDevRequires();
		/** @var \Composer\Package\Link $require */
		foreach ($devRequires as $devRequire) {
			$dependencies[$devRequire->getTarget()] = $devRequire->getPrettyConstraint();
		}

		$extra = $package->getExtra();

		$proxyUrls = (array)Arry::get($extra, 'composer-proxy.url');
		if ( ! $proxyUrls) {
			$io->write('<warning>No composer proxy url defined in composer.json. You might not use your proxy when fetching packages.</warning>');
			return;
		}

		$io->write('<info>Sending data over to the defined proxies in order to cache your packages. Please wait, this may take a while...</info>');

		$client = new GuzzleClient();

		$requests = [];
		foreach ($proxyUrls as $proxyUrl) {
			$requests[] = $client->createRequest('POST', $proxyUrl.'/packages', [
				'body' => $dependencies,
			]);
		}

		$pool = new Pool($client, $requests);
		$res = $pool->wait();

		$io->write('<info>Proxies caching completed!</info>');
	}
}
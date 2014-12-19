<?php

namespace ComposerProxy\Client;

use Arry\Arry;
use Composer\Script\Event;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
	public static function prepare(Event $event)
	{
		$composer = $event->getComposer();
		$io = $event->getIO();
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

		$proxyUrl = Arry::get($extra, 'composer-proxy.url');
		if ( ! $proxyUrl) {
			$io->write('<warning>No composer proxy url defined in composer.json. You might not use your proxy when fetching packages.</warning>');
			return;
		}

		$client = new GuzzleClient([
			'base_url' => $proxyUrl,
		]);
		$client->post('packages', $dependencies);
	}
}
<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Console\Console;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\Console\Application;

final class PackageManagerExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('packageRegistrator'))
			->setFactory(PackageRegistrator::class)
			->setAutowired(PackageRegistrator::class);
	}


	public function afterCompile(ClassType $class): void
	{
		if (PHP_SAPI === 'cli') {
			$class->getMethod('initialize')->addBody(
				'// Package manager.' . "\n"
				. '(function () {' . "\n"
				. "\t" . 'if (isset($_SERVER[\'NETTE_TESTER_RUNNER\']) === true) { return; }' . "\n"
				. "\t" . 'new ' . Console::class . '($this->getByType(?), $this->getByType(?));' . "\n"
				. '})();' . "\n",
				[Application::class, \Nette\Application\Application::class]
			);
		}
	}
}

<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

$file = __DIR__.'/vendor/autoload.php';

if (!file_exists($file)) {
	throw new RuntimeException('Install dependencies to run test suite.');
}

$autoload = require_once $file;

$autoload->add('Acme', __DIR__ . '/tests/SupportFiles/src');

$autoload->register();

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $autoload->add('', __DIR__.'/vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

AnnotationRegistry::registerLoader(array($autoload, 'loadClass'));

return $autoload;
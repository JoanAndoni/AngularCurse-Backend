<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0a01ae49e32dd0219a23dab9e94a9b9
{
    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Slim' => 
            array (
                0 => __DIR__ . '/..' . '/slim/slim',
            ),
        ),
    );

    public static $classMap = array (
        'PiramideUploader' => __DIR__ . '/../..' . '/piramide-uploader/PiramideUploader.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitc0a01ae49e32dd0219a23dab9e94a9b9::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitc0a01ae49e32dd0219a23dab9e94a9b9::$classMap;

        }, null, ClassLoader::class);
    }
}

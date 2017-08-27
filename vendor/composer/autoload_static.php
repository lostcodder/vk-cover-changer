<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf5b4e3926479a2dfd26845622a1ee399
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Buzz\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Buzz\\' => 
        array (
            0 => __DIR__ . '/..' . '/kriswallsmith/buzz/lib/Buzz',
        ),
    );

    public static $prefixesPsr0 = array (
        'N' => 
        array (
            'NekoWeb' => 
            array (
                0 => __DIR__ . '/..' . '/druidvav/antigate-client/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf5b4e3926479a2dfd26845622a1ee399::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf5b4e3926479a2dfd26845622a1ee399::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitf5b4e3926479a2dfd26845622a1ee399::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}

<?php



namespace Composer\Autoload;

class ComposerStaticInitcf1a3d40be2db3d20e77f5829d3856fd
{
    public static $files = array (
        'ee249067d88400e71c7ddbe6dbf8f63d' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/version.php',
        '632577a169802e8825cf41367ab3130d' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v6/core/class-kernel-lite.php',
        '1adb0d09bdfb1b3428f647ec34b0e5ea' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v6/helper/base-helper.php',
        '75918ec4267601268984e7c4c38aea11' => __DIR__ . '/../..' . '/vitepos_lite/helper/global-helper.php',
        '1e30ec1bad4eed8f5d0e5240bdf9cb82' => __DIR__ . '/../..' . '/vitepos_lite/libs/class-vitepos-loader.php',
    );

    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'VitePos_Lite\\' => 13,
        ),
        'A' => 
        array (
            'Appsbd_Lite\\V6\\' => 15,
            'Appsbd_Lite\\V5\\' => 15,
            'Appsbd_Lite\\V4\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'VitePos_Lite\\' => 
        array (
            0 => __DIR__ . '/../..' . '/vitepos_lite',
        ),
        'Appsbd_Lite\\V6\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v6',
        ),
        'Appsbd_Lite\\V5\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v5',
        ),
        'Appsbd_Lite\\V4\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v4',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf1a3d40be2db3d20e77f5829d3856fd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf1a3d40be2db3d20e77f5829d3856fd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcf1a3d40be2db3d20e77f5829d3856fd::$classMap;

        }, null, ClassLoader::class);
    }
}

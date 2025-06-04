<?php



namespace Composer\Autoload;

class ComposerStaticInitcf1a3d40be2db3d20e77f5829d3856fd
{
    public static $files = array (
        'c33f23be1f768473540e11bdf37dab3a' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v5/core/class-kernel-lite.php',
        'fa9e236262162c72a39dd210fcd21a04' => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite/v5/helper/base-helper.php',
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
            'Appsbd_Lite\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'VitePos_Lite\\' => 
        array (
            0 => __DIR__ . '/../..' . '/vitepos_lite',
        ),
        'Appsbd_Lite\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsbd-wp/appsbd-lite/appsbd_lite',
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

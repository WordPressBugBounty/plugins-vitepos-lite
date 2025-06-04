<?php
if ( file_exists( getcwd() . DIRECTORY_SEPARATOR . 'composer.lock' ) ) {
	exit;
}
ob_start();
?>
	/**
	* Plugin Name: {{name}}
	* Plugin URI: {{plugin_uri}}
	* Description: it's a plugin for WooCommerce Mini Cart.
	* Version: 1.0.0
	* Author: appsbd
	* Author URI: http://www.appsbd.com
	* Text Domain: minicart
	* Tested up to: 6.3
	* wc require:3.2.0
	* License: GPLv2 or later
	* License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*
	* @package {{name_space}}
	*/

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once 'vendor/autoload.php';

	/*
	use {{name_space}}\Core\{{basename}}_lite;


	if ( true === \{{name_space}}\Libs\{{basename}}_Loader::is_ready_to_load( __FILE__ ) ) {

	// __ron_start__
	{{basename}}_lite::set_development_mode( true );

	// __ron_end__

	$o{{basename}} = new {{basename}}_lite( __FILE__ );
	$o{{basename}}->start_plugin();
	}
	*/
<?php
$main_file     = ob_get_clean();
function rmdir_recursive( $dir ) {
	foreach ( scandir( $dir ) as $file ) {
		if ( '.' === $file || '..' === $file ) {
			continue;
		}
		if ( is_dir( "$dir/$file" ) ) {
			rmdir_recursive( "$dir/$file" );
		} else {
			unlink( "$dir/$file" );
		}
	}
	rmdir( $dir );
}


$is_pre = ! empty( $argv[1] ) && strtolower( trim( $argv[1] ) ) == 'pre';
if ( $is_pre ) {
	if ( is_dir( __DIR__ . DIRECTORY_SEPARATOR . '.git_bk' ) ) {
		rename( __DIR__ . DIRECTORY_SEPARATOR . '.git_bk', __DIR__ . DIRECTORY_SEPARATOR . '.git' );
	}
} else {
	if ( ! file_exists( getcwd() . DIRECTORY_SEPARATOR . basename( getcwd() ) . '.php' ) ) {
		$composer = json_decode( file_get_contents( getcwd() . DIRECTORY_SEPARATOR . 'composer.json' ) );
		$old_data = array(
			'plugin_name' => 'Sample Plugin',
			'basename'    => 'Simple',
			'name_space'  => 'Simple_Lite',
			'plugin_uri'  => 'https://appsbd.com',
		);
		if ( ! empty( $composer->plugin_data ) ) {
			$plugindata = (array) $composer->plugin_data;
			foreach ( $plugindata as $key => $plugindatum ) {
				$old_data[ $key ] = $plugindatum;
			}
		}

		foreach ( $old_data as $okey => $oval ) {
			$main_file = str_replace( '{{' . $okey . '}}', $oval, $main_file );
		}
		$base_path = strtolower( $old_data['name_space'] );
		$dirs      = array(
			'languages',
			'dev_files',
			$base_path . DIRECTORY_SEPARATOR . 'core',
			$base_path . DIRECTORY_SEPARATOR . 'models',
			$base_path . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'database',
			$base_path . DIRECTORY_SEPARATOR . 'libs',

			$base_path . DIRECTORY_SEPARATOR . 'helper',
			$base_path . DIRECTORY_SEPARATOR . 'modules',
		);
		foreach ( $dirs as $dir ) {
			if ( ! is_dir( getcwd() . DIRECTORY_SEPARATOR . $dir ) ) {
				mkdir( getcwd() . DIRECTORY_SEPARATOR . $dir, 0755, true );
			}
		}

		$files = array(
			'core/class-basename-lite.php.txt',
			'core/class-basename-module-lite.php.txt',
			'core/class-reward-lite-model.php.txt',
			'libs/class-basename-loader.php.txt',
			'libs/class-client-language.php.txt',

		);

		
		foreach ( $files as $file ) {
			$from_file = __DIR__ . DIRECTORY_SEPARATOR . 'apbd-plugin-docs' . DIRECTORY_SEPARATOR . $file;
			if ( file_exists( $from_file ) ) {
				$to_file = getcwd() . DIRECTORY_SEPARATOR . $base_path . DIRECTORY_SEPARATOR . str_replace(
					array(
						'basename',
						'.php.txt',
					),
					array( strtolower( $old_data['basename'] ), '.php' ),
					$file
				);
				if ( ! file_exists( $to_file ) ) {
					$fpath = dirname( $to_file );
					if ( ! is_dir( $fpath ) ) {
						mkdir( $fpath, 0755, true );
					}
					$file_data = file_get_contents( $from_file );
					foreach ( $old_data as $okey => $oval ) {
						$file_data = str_replace( '{{' . $okey . '}}', $oval, $file_data );
					}
					if ( file_put_contents( $to_file, $file_data ) ) {
						echo 'File created:' . $to_file . "\n";
					}
				}
			} else {
				echo 'Base file not exists :' . $from_file . "\n";
			}
		}

		if ( ! file_exists( getcwd() . DIRECTORY_SEPARATOR . basename( getcwd() ) . '.php' ) ) {
			if ( file_put_contents(
				getcwd() . DIRECTORY_SEPARATOR . basename( getcwd() ) . '.php',
				"<?php\n" . $main_file
			) ) {
				echo 'Plugin File created :' . basename( getcwd() ) . ".php \n";
			}
		}
	}
	


	if ( is_dir( __DIR__ . DIRECTORY_SEPARATOR . 'apbd-plugin-docs' ) ) {
		rmdir_recursive( __DIR__ . DIRECTORY_SEPARATOR . 'apbd-plugin-docs' );
	}
	if ( is_dir( __DIR__ . DIRECTORY_SEPARATOR . '.git' ) ) {
		
	}
	if ( file_exists( __DIR__ . DIRECTORY_SEPARATOR . 'bitbucket-pipelines.yml' ) ) {
		unlink( __DIR__ . DIRECTORY_SEPARATOR . 'bitbucket-pipelines.yml' );
	}
}

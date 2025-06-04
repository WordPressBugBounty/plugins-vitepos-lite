<?php
/**
 * It is template of pos client
 *
 * @var \VitePos_Lite\Modules\POS_Settings $this
 *
 * @package vitepos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="theme-color" content="<?php echo esc_html( $this->get_pos_color_code() ); ?>">
	<link rel="icon" href="<?php echo esc_url( $this->get_favicon() ); ?>">
	<link rel="manifest" href="<?php echo esc_url( $this->get_manifest_link() ); ?>">
	<title>pos</title>
	<?php
	/**
	 * Its for pos client header
	 *
	 * @since 1.0
	 */
	do_action( 'vitepos-client-header' );
	?>
</head>
<body><noscript><strong> <?php echo esc_html( $this->kernel_object->__( "We're sorry but pos doesn't work properly without JavaScript enabled. Please enable it to continue." ) ); ?></strong></noscript>
<div id="app">
	<div class="pre-loader">
		<?php echo esc_html( $this->kernel_object->__( 'Please wait ..' ) ); ?>
	</div>
</div>
<?php
/**
 * Its for pos client header
 *
 * @since 1.0
 */
do_action( 'vitepos-client-footer' );
?>
</body>
</html>

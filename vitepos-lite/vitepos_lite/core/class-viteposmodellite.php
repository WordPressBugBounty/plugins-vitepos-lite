<?php
/**
 * Vitepos Model
 *
 * @package VitePos\Core
 */

namespace VitePos_Lite\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Appsbd_Lite\V1\Core\BaseModel;

/**
 * Class ViteposModel
 *
 * @package VitePos\Core
 */
class ViteposModelLite extends BaseModel {

	/**
	 * The __ is generated by appsbd
	 *
	 * @param any  $string Its string param.
	 * @param null $parameter Its parameter param.
	 * @param null $_ Its _ param.
	 *
	 * @return mixed
	 */
	public function __( $string, $parameter = null, $_ = null ) {
		$args = func_get_args();
		return call_user_func_array( array( VitePosLite::get_instance(), '__' ), $args );
	}
}

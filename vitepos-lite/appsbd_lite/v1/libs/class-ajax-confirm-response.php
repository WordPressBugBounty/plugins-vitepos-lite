<?php
/**
 * Its pos appsbd-ajax-confirm-response model
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Appsbd\V1\libs
 */

namespace Appsbd_Lite\V1\libs;

use Appsbd_Lite\V1\Core\Kernel_Lite;

if ( ! class_exists( __NAMESPACE__ . '\Ajax_Confirm_Response' ) ) {
	/**
	 * Class appsbd_ajax_confirm_response
	 *
	 * @package Appsbd\V1\libs
	 */
	class Ajax_Confirm_Response {
		/**
		 * Its property status
		 *
		 * @var bool
		 */
		public $status = false;
		/**
		 * Its property msg
		 *
		 * @var string
		 */
		public $msg = '';
		/**
		 * Its property data
		 *
		 * @var null
		 */
		public $data = null;
		/**
		 * Its property icon
		 *
		 * @var string
		 */
		public $icon = '';
		/**
		 * Its property is_sticky
		 *
		 * @var bool
		 */
		public $is_sticky = false;
		/**
		 * Its property title
		 *
		 * @var null
		 */
		public $title = null;
		/**
		 * Its property https_status
		 *
		 * @var null
		 */
		private $https_status = null;

		/**
		 * The set response is generated by appsbd
		 *
		 * @param bool   $status Its status parameter.
		 * @param null   $data Its data parameter.
		 * @param string $icon Its icon parameter.
		 * @param null   $title Its title parameter.
		 * @param false  $is_sticky Its sticky parameter.
		 */
		public function set_response( $status, $data = null, $icon = '', $title = null, $is_sticky = false ) {
			if ( empty( $icon ) ) {
				$icon = $status ? ' fa fa-check-circle-o ' : ' fa fa-times-circle-o ';
			}
			$this->status    = $status;
			$this->msg       = \Appsbd_Lite\V1\Core\Kernel_Lite::get_msg_for_api();
			$this->data      = $data;
			$this->icon      = $icon;
			$this->is_sticky = $is_sticky;
			$this->title     = $title;
		}

		/**
		 * The display with response is generated by appsbd
		 *
		 * @param mixed $status Its status param.
		 * @param null  $data Its data param.
		 * @param null  $status_code Its the status code.
		 */
		public function display_with_response( $status, $data = null, $status_code = null ) {
			$this->set_response( $status, $data );
			$this->set_http_status_code( $status_code );
			$this->display();
		}

		/**
		 * The set http status code is generated by appsbd
		 *
		 * @param mixed $status_code Its status code.
		 */
		public function set_http_status_code( $status_code ) {
			$this->https_status = $status_code;
		}
		/**
		 * The add error is generated by appsbd
		 *
		 * @param mixed $msg Its msg param.
		 */
		public function add_error( $msg ) {
			Kernel_Lite::add_error( $msg );
		}

		/**
		 * The add info is generated by appsbd
		 *
		 * @param mixed $msg Its msg param.
		 */
		public function add_info( $msg ) {
			Kernel_Lite::add_info( $msg );
		}

		/**
		 * The add debug is generated by appsbd
		 *
		 * @param mixed $msg Its msg param.
		 */
		public function add_debug( $msg ) {
			Kernel_Lite::add_debug( $msg );
		}

		/**
		 * The add warning is generated by appsbd
		 *
		 * @param mixed $msg Its msg param.
		 */
		public function add_warning( $msg ) {
			Kernel_Lite::add_warning( $msg );
		}
		/**
		 * The display is generated by appsbd
		 */
		public function display() {
			wp_send_json( $this, $this->https_status );
		}
	}
}

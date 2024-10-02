<?php
/**
 * Its used for Base module.
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Appsbd\V1\Core
 */

namespace Appsbd_Lite\V1\Core;

use Appsbd_Lite\V1\libs\Ajax_Data_Response;
use Appsbd_Lite\V1\libs\AppInput;
use Appsbd_Lite\V1\libs\Ajax_Confirm_Response;

if ( ! class_exists( __NAMESPACE__ . '\BaseModule' ) ) {


	/**
	 * Class BaseModule
	 *
	 * @package Appsbd\V1\Core
	 */
	abstract class BaseModule {

		/**
		 * Its property module_name
		 *
		 * @var string
		 */
		public $module_name = '';
		/**
		 * Its property menu_title
		 *
		 * @var string
		 */
		public $menu_title = '';
		/**
		 * Its property menu_icon
		 *
		 * @var string
		 */
		public $menu_icon = '';
		/**
		 * Its property plugin_base_name
		 *
		 * @var any
		 */
		public $plugin_base_name;
		/**
		 * Its property plugin_file
		 *
		 * @var any
		 */
		public $plugin_file;
		/**
		 * Its property options
		 *
		 * @var array
		 */
		protected $options;
		/**
		 * Its Kernel Object
		 *
		 * @var Kernel_Lite
		 */
		public $kernel_object;
		/**
		 * Its property form_class
		 *
		 * @var string
		 */
		protected $form_class = '';
		/**
		 * Its property is_multipart_form
		 *
		 * @var bool
		 */
		protected $is_multipart_form = false;
		/**
		 * Its property form_data_attr
		 *
		 * @var array
		 */
		protected $form_data_attr = array();
		/**
		 * Its property dont_add_default_form
		 *
		 * @var bool
		 */
		protected $dont_add_default_form = false;
		/**
		 * Its property _view_data
		 *
		 * @var string[]
		 */
		protected $_view_data = array(
			'_title'        => 'Unknown',
			'_subTitle'     => '',
			'_relaod_event' => '',
		);
		/**
		 * Its property __on_tab_active_js_method
		 *
		 * @var string
		 */
		protected $__on_tab_active_js_method = '';
		/**
		 * Its contains self instance
		 *
		 * @var self []
		 */
		private static $_self = array();
		/**
		 * Its property view_path
		 *
		 * @var string
		 */
		protected $view_path = '';
		/**
		 * Its property is_disabled_menu
		 *
		 * @var bool
		 */
		protected $is_disabled_menu = false;
		/**
		 * Its property is_last_menu
		 *
		 * @var bool
		 */
		protected $is_last_menu = false;
		/**
		 * Its property is_hidden_module
		 *
		 * @var bool
		 */
		protected $is_hidden_module = false;
		/**
		 * Its notice type error
		 */
		const NOTICE_TYPE_ERROR = 'E';
		/**
		 * Its notice type info
		 */
		const NOTICE_TYPE_INFO = 'I';
		/**
		 * Its notice type appsbd
		 */
		const NOTICE_TYPE_APPSBD = 'A';
		/**
		 * Its notice type none
		 */
		const NOTICE_TYPE_NONE = 'N';

		/**
		 * BaseModule constructor.
		 *
		 * @param any    $plugin_base_name Its plugin_base_name param.
		 * @param Kernel $kernel_object Its kernel_object param.
		 */
		public function __construct( $plugin_base_name, &$kernel_object ) {
			$this->kernel_object    = $kernel_object;
			$this->plugin_base_name = $plugin_base_name;
			$this->plugin_file      = $this->kernel_object->plugin_file;
			$this->set_option();
			self::$_self[ get_class( $this ) ] = $this;
			$this->initialize();
		}



		/**
		 * The initialize is generated by appsbd
		 */
		abstract public function initialize();

		/**
		 * The on init is generated by appsbd
		 */
		public function on_init() {
			if ( $this->check_user_access() ) {
				$this->add_ajax_action( 'option', array( $this, 'ajax_request_callback' ) );
				$this->add_ajax_action( 'get-option', array( $this, 'get_admin_options' ) );
				$this->add_ajax_action( 'data', array( $this, 'data' ) );
				$this->add_ajax_action( 'confirm', array( $this, 'confirm' ) );
			}
		}
		/**
		 * The check user access is generated by appsbd
		 *
		 * @return bool
		 */
		public function check_user_access() {
			return current_user_can( 'activate_plugins' );
		}
		/**
		 * The AddAdminNoticeWithBg is generated by appsbd
		 *
		 * @param any    $message Its message param.
		 * @param any    $type Its type param.
		 * @param false  $is_dismissible Its is_dismissible param.
		 * @param string $extra_class Its extra_class param.
		 */
		public function add_admin_notice_with_bg( $message, $type, $is_dismissible = false, $extra_class = '' ) {
			$extra_class .= ' apbd-with-bg';
			$this->add_admin_notice( $message, $type, $is_dismissible, $extra_class );
		}


		/**
		 * The Add Admin Notice is generated by appsbd
		 *
		 * @param any    $message Its message param.
		 * @param any    $type Its type param.
		 * @param false  $is_dismissible Its is_dismissible param.
		 * @param string $extra_class Its extra_class param.
		 */
		public function add_admin_notice( $message, $type, $is_dismissible = false, $extra_class = '' ) {
			if ( self::NOTICE_TYPE_ERROR == $type ) {
				$class = 'notice apbd-notice notice-error';
			} elseif ( self::NOTICE_TYPE_APPSBD == $type ) {
				$class = 'notice apbd-notice notice-appsbd';
			} elseif ( self::NOTICE_TYPE_NONE == $type ) {
				$class = '';
			} else {
				$class = 'notice apbd-notice notice-success';
			}
			if ( $is_dismissible ) {
				$class .= ' is-dismissible';
			}
			$class .= ' ' . $extra_class;
			$msg    = sprintf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
			$this->kernel_object->add_admin_notice( $msg );
		}
		/**
		 * The getHookActionStr is generated by appsbd
		 *
		 * @param any $str Its str param.
		 *
		 * @return mixed
		 */
		public function get_hook_action_str( $str ) {
			return $this->kernel_object->get_hook_action_str( $str );
		}


		/**
		 * The getCustomizerControlId is generated by appsbd
		 *
		 * @param any $name Its name param.
		 *
		 * @return string
		 */
		public function get_customizer_control_id( $name ) {
			return $this->get_module_id() . '_cs_' . $name;
		}


		/**
		 * The getCustomizerControlIdToRealId is generated by appsbd
		 *
		 * @param any $customizer_id Its customizer_id param.
		 *
		 * @return false|string
		 */
		public function get_customizer_control_id_to_real_id( $customizer_id ) {
			return substr( $customizer_id, strlen( $this->get_module_id() . '_cs_' ) );
		}


		/**
		 * The addTopMenu is generated by appsbd
		 *
		 * @param string   $title Its title param.
		 * @param string   $icon Its icon param.
		 * @param callable $func Its func param.
		 * @param string   $css_class Its css_class param.
		 * @param bool     $is_tab Its is_tab param.
		 * @param array    $attr Its attr param.
		 */
		public function add_top_menu( $title, $icon, $func, $css_class = '', $is_tab = true, $attr = array() ) {
			$this->kernel_object->add_top_menu( $title, $icon, $func, $css_class, $is_tab, $attr );
		}


		/**
		 * The GetModuleInstance is generated by appsbd
		 *
		 * @return static
		 */
		public static function get_module_instance() {
			return self::$_self[ static::class ];
		}


		/**
		 * The GetModuleOption is generated by appsbd
		 *
		 * @param string $key Its key param.
		 * @param string $default Its default param.
		 *
		 * @return mixed|string
		 */
		public static function get_module_option( $key = '', $default = '' ) {
			if ( ! empty( self::$_self[ static::class ] ) ) {
				return self::$_self[ static::class ]->get_option( $key, $default );
			} else {
				return $default;
			}
		}


		/**
		 * The GetModuleActionUrl is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 *
		 * @return string
		 */
		public static function get_module_action_url( $action_string = '', $params = array() ) {
			if ( ! empty( self::$_self[ static::class ] ) ) {
				return self::$_self[ static::class ]->get_action_url( $action_string, $params );
			} else {
				return 'model not initialize';
			}
		}

		/**
		 * The AddError is generated by appsbd
		 *
		 * @param any  $message Its message param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 */
		public function add_error( $message, $parameter = null, $_ = null ) {
			if ( ! is_string( $message ) || empty( $message ) ) {
				return;
			}
			$args    = func_get_args();
			$message = call_user_func_array( array( $this, '__' ), $args );
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_error( $message );
		}


		/**
		 * The AddInfo is generated by appsbd
		 *
		 * @param any  $message Its message param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 */
		public function add_info( $message, $parameter = null, $_ = null ) {
			if ( ! is_string( $message ) || empty( $message ) ) {
				return;
			}
			$args    = func_get_args();
			$message = call_user_func_array( array( $this, '__' ), $args );
			\Appsbd_Lite\V1\Core\Kernel_Lite::add_info( $message );
		}


		/**
		 * The AddDebug is generated by appsbd
		 *
		 * @param any $obj Its obj param.
		 */
		public function add_debug( $obj ) {
			 \Appsbd_Lite\V1\Core\Kernel_Lite::add_debug( $obj );
		}


		/**
		 * The AddWarning is generated by appsbd
		 *
		 * @param any  $message Its message param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 */
		public function add_warning( $message, $parameter = null, $_ = null ) {
			 $args   = func_get_args();
			$message = call_user_func_array( array( $this, '___' ), $args );
			appsbd_add_warning_v1_lite( $message );
		}


		/**
		 * The setViewPath is generated by appsbd
		 *
		 * @param any $view_path Its view_path param.
		 */
		public function set_view_path( $view_path ) {
			$this->view_path = $view_path;
		}


		/**
		 * The isDisabledMenu is generated by appsbd
		 *
		 * @return bool
		 */
		public function is_disabled_menu() {
			return $this->is_disabled_menu;
		}


		/**
		 * The set disabled menu is generated by appsbd
		 */
		public function set_disabled_menu() {
			 $this->is_disabled_menu = true;
		}


		/**
		 * The isLastMenu is generated by appsbd
		 *
		 * @return bool
		 */
		public function is_last_menu() {
			return $this->is_last_menu;
		}


		/**
		 * The setIsLastMenu is generated by appsbd
		 *
		 * @param any $is_last_menu Its is_last_menu param.
		 */
		public function set_is_last_menu( $is_last_menu ) {
			$this->is_last_menu = $is_last_menu;
		}


		/**
		 * The isHiddenModule is generated by appsbd
		 *
		 * @return bool
		 */
		public function is_hidden_module() {
			return $this->is_hidden_module;
		}


		/**
		 * The setIsHiddenModule is generated by appsbd
		 *
		 * @param any $is_hidden_module Its is_hidden_module param.
		 */
		public function set_is_hidden_module( $is_hidden_module ) {
			$this->is_hidden_module = $is_hidden_module;
		}


		/**
		 * The APPSBDLoadDatabaseModel is generated by appsbd
		 *
		 * @param any $model_name Its model_name param.
		 */
		protected function appsbd_load_database_model( $model_name ) {
			appsbd_load_database_model( $this->kernel_object->plugin_file, $model_name, $model_name );
		}

		/**
		 * The on table create is generated by appsbd
		 */
		public function on_table_create() {
		}


		/**
		 * The OnPluginVersionUpdated is generated by appsbd
		 *
		 * @param any $current_version Its current_version param.
		 * @param any $previous_version Its previous_version param.
		 */
		public function on_plugin_version_updated( $current_version, $previous_version ) {
		}


		/**
		 * The AddFilter is generated by appsbd
		 *
		 * @param any $filter Its filter param.
		 * @param any $filter_function_name Its filter_function_name param.
		 */
		public function add_filter( $filter, $filter_function_name ) {
			add_filter( $filter, $filter_function_name );
		}


		/**
		 * The AddAction is generated by appsbd
		 *
		 * @param any $action Its action param.
		 * @param any $action_function_name Its action_function_name param.
		 * @param int $priority Its priority param.
		 * @param int $accepted_args Its accepted_args param.
		 */
		public function add_action( $action, $action_function_name, $priority = 10, $accepted_args = 1 ) {
			add_action( $action, $action_function_name, $priority, $accepted_args );
		}


		/**
		 * The AddAppAction is generated by appsbd
		 *
		 * @param any $action Its action param.
		 * @param any $action_function_name Its action_function_name param.
		 * @param int $priority Its priority param.
		 * @param int $accepted_args Its accepted_args param.
		 */
		public function add_app_action( $action, $action_function_name, $priority = 10, $accepted_args = 1 ) {
			$action = $this->get_hook_action_str( $action );
			add_action( $action, $action_function_name, $priority, $accepted_args );
		}


		/**
		 * The AddIntoOption is generated by appsbd
		 *
		 * @param any $key Its key param.
		 * @param any $value Its value param.
		 */
		public function add_into_option( $key, $value ) {
			$this->options[ $key ] = $value;
		}

		/**
		 * The Add Client Style is generated by appsbd
		 *
		 * @param any   $style_id Its style_id param.
		 * @param any   $style_file_name Its style_file_name param.
		 * @param false $is_from_root Its is_from_root param.
		 * @param array $deps Its deps param.
		 */
		final public function add_client_style( $style_id, $style_file_name, $is_from_root = false, $deps = array() ) {
			$this->kernel_object->add_style( $style_id, $style_file_name, $is_from_root, $deps );
		}


		/**
		 * The Add Client Script is generated by appsbd
		 *
		 * @param any   $script_id Its script_id param.
		 * @param any   $script_file_name Its script_file_name param.
		 * @param false $is_from_root Its is_from_root param.
		 * @param array $deps Its deps param.
		 */
		final public function add_client_script( $script_id, $script_file_name, $is_from_root = false, $deps = array() ) {
			$this->kernel_object->add_script( $script_id, $script_file_name, $is_from_root, $deps );
		}


		/**
		 * The AddAdminStyle is generated by appsbd
		 *
		 * @param any   $style_id Its style_id param.
		 * @param any   $style_file_name Its style_file_name param.
		 * @param false $is_from_root Its is_from_root param.
		 * @param array $deps Its deps param.
		 */
		final public function add_admin_style( $style_id, $style_file_name, $is_from_root = false, $deps = array() ) {
			$this->kernel_object->add_style( $style_id, $style_file_name, $is_from_root, $deps );
		}


		/**
		 * The AddAdminScript is generated by appsbd
		 *
		 * @param any   $script_id Its script_id param.
		 * @param any   $script_file_name Its script_file_name param.
		 * @param false $is_from_root Its is_from_root param.
		 * @param array $deps Its deps param.
		 */
		final public function add_admin_script( $script_id, $script_file_name, $is_from_root = false, $deps = array() ) {
			 $this->kernel_object->add_script( $script_id, $script_file_name, $is_from_root, $deps );
		}


		/**
		 * The AddGlobalJSVar is generated by appsbd
		 *
		 * @param any $key Its key param.
		 * @param any $value Its value param.
		 */
		final public function add_global_js_var( $key, $value ) {
			 $value = $this->__( $value );
			$this->kernel_object->add_app_global_var( $key, $value );
		}


		/**
		 * The AddAjaxAction is generated by appsbd
		 *
		 * @param any      $action_name Its action_name param.
		 * @param callable $function_to_add Its function_to_add param.
		 */
		public function add_ajax_action( $action_name, $function_to_add ) {
			$action_name = $this->get_action_name( $action_name );
			add_action( 'wp_ajax_' . $action_name, $function_to_add );
		}


		/**
		 * The AddAjaxNoPrivAction is generated by appsbd
		 *
		 * @param any $action_name Its action_name param.
		 * @param any $function_to_add Its function_to_add param.
		 */
		public function add_ajax_no_priv_action( $action_name, $function_to_add ) {
			 $action_name = $this->get_action_name( $action_name );
			add_action( 'wp_ajax_nopriv_' . $action_name, $function_to_add );
		}


		/**
		 * The AddAjaxBothAction is generated by appsbd
		 *
		 * @param any $action_name Its action_name param.
		 * @param any $function_to_add Its function_to_add param.
		 */
		public function add_ajax_both_action( $action_name, $function_to_add ) {
			$this->add_ajax_action( $action_name, $function_to_add );
			$this->add_ajax_no_priv_action( $action_name, $function_to_add );
		}


		/**
		 * The GetActionUrl is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 *
		 * @return string
		 */
		public function get_action_url( $action_string = '', $params = array() ) {
			$action_name = $this->get_action_name( $action_string );
			$param_str   = count( $params ) > 0 ? '&' . http_build_query( $params ) : '';

			return admin_url( 'admin-ajax.php' ) . '?action=' . $action_name . $param_str;
		}


		/**
		 * The GetActionUrlWithBackButton is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 * @param null   $back_action_string Its back_action_string param.
		 * @param array  $back_params Its back_params param.
		 * @param string $button_name Its button_name param.
		 *
		 * @return string
		 */
		public function get_action_url_with_back_button(
			$action_string = '',
			$params = array(),
			$back_action_string = null,
			$back_params = array(),
			$button_name = 'back'
		) {
			$button_name = $this->__( $button_name );
			$main_url    = $this->get_action_url( $action_string, $params );
			if ( is_null( $back_action_string ) ) {
				$button_url = appsbd_current_url();
			} else {
				$button_url = $this->get_action_url( $back_action_string, $back_params );
			}

			if ( strpos( $main_url, '?' ) !== false ) {
				return $main_url . '&cbtn=' . urlencode( $button_url ) . '&cbtnn=' . urlencode( $button_name );
			} else {
				return $main_url . '?cbtn=' . $button_url . '&cbtnn=' . $button_name;
			}
		}


		/**
		 * The RedirectActionUrlWithBackButton is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 * @param null   $back_action_string Its back_action_string param.
		 * @param array  $back_params Its back_params param.
		 * @param string $button_name Its button_name param.
		 */
		public function redirect_action_url_with_back_button(
			$action_string = '',
			$params = array(),
			$back_action_string = null,
			$back_params = array(),
			$button_name = 'back'
		) {
			$url = $this->get_action_url_with_back_button(
				$action_string,
				$params,
				$back_action_string,
				$back_params,
				$button_name
			);
			$this->redirect_url( $url );
		}


		/**
		 * The RedirectActionUrl is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 */
		public function redirect_action_url( $action_string = '', $params = array() ) {
			$url = $this->get_action_url( $action_string, $params );
			$this->redirect_url( $url );
		}


		/**
		 * The RedirectUrl is generated by appsbd
		 *
		 * @param any $url Its url param.
		 */
		public function redirect_url( $url ) {
			header( "Location: $url" );
			die;
		}


		/**
		 * The GetAPIUrl is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 * @param array  $params Its params param.
		 *
		 * @return string
		 */
		public function get_api_url( $action_string = '', $params = array() ) {
			return get_bloginfo( 'url' ) . '/wp-json/' . $action_string;
		}


		/**
		 * The GetActionName is generated by appsbd
		 *
		 * @param string $action_string Its action_string param.
		 *
		 * @return string
		 */
		public function get_action_name( $action_string = '' ) {
			if ( ! empty( $action_string ) ) {
				$action_string = '-' . $action_string;
			}

			return $this->get_module_action_prefix() . $action_string;
		}


		/**
		 * The OptionFormHeader is generated by appsbd
		 *
		 * @return string
		 */
		public function option_form_header() {
			return '';
		}


		/**
		 * The getModuleOptionName is generated by appsbd
		 *
		 * @return string
		 */
		public function get_module_option_name() {
			$module_name = strtolower( $this->get_module_id() );
			return $this->plugin_base_name . '_o_' . $module_name;
		}

		/**
		 * The set option is generated by appsbd
		 */
		public function set_option() {
			$option_name   = $this->get_module_option_name();
			$this->options = get_option( $option_name, array() );
		}


		/**
		 * The get_option is generated by appsbd
		 *
		 * @param string $key Its key param.
		 * @param string $default Its default param.
		 *
		 * @return mixed|string
		 */
		public function get_option( $key = '', $default = '' ) {
			if ( empty( $key ) ) {
				return $this->options;
			} elseif ( ! empty( $this->options[ $key ] ) ) {
					return $this->options[ $key ];
			} else {
				return $default;
			}
		}


		/**
		 * The AddOption is generated by appsbd
		 *
		 * @param any $key Its key param.
		 * @param any $value Its value param.
		 *
		 * @return bool
		 */
		public function add_option( $key, $value ) {
			$this->options[ $key ] = $value;

			return $this->update_option();
		}


		/**
		 * The UpdateOption is generated by appsbd
		 *
		 * @return bool
		 */
		public function update_option() {
			$option_name = $this->get_module_option_name();
			return update_option( $option_name, $this->options ) || add_option( $option_name, $this->options );
		}


		/**
		 * The GetModuleId is generated by appsbd
		 *
		 * @return false|string
		 */
		public function get_module_id() {
			$class = get_class( $this );
			$class = str_replace( '\\', '/', $class );
			return basename( $class );
		}


		/**
		 * The _e is generated by appsbd
		 *
		 * @param any  $string Its string param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 */
		public function esc_attr_e( $string, $parameter = null, $_ = null ) {
			$args = func_get_args();
			echo esc_attr( call_user_func_array( array( $this->kernel_object, '__' ), $args ) );
		}


		/**
		 * The _ee is generated by appsbd
		 *
		 * @param any  $string Its string param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 */
		public function _ee( $string, $parameter = null, $_ = null ) {
			 $args = func_get_args();
			foreach ( $args as &$arg ) {
				if ( is_string( $arg ) ) {
					$arg = $this->kernel_object->__( $arg );
				}
			}
			$this->esc_attr_e( $string, $args );
		}


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

			return call_user_func_array( array( $this->kernel_object, '__' ), $args );
		}
		/**
		 * The __ is generated by appsbd
		 *
		 * @param any  $string Its string param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 *
		 * @return mixed
		 */
		public function esc_e( $string, $parameter = null, $_ = null ) {
			$args = func_get_args();
			echo wp_kses_post( call_user_func_array( array( $this->kernel_object, '__' ), $args ) );
		}


		/**
		 * The ___ is generated by appsbd
		 *
		 * @param any  $string Its string param.
		 * @param null $parameter Its parameter param.
		 * @param null $_ Its _ param.
		 *
		 * @return mixed
		 */
		public function ___( $string, $parameter = null, $_ = null ) {
			 $args = func_get_args();
			foreach ( $args as &$arg ) {
				if ( is_string( $arg ) ) {
					$arg = $this->kernel_object->__( $arg );
				}
			}

			return call_user_func_array( array( $this->kernel_object, '__' ), $args );
		}


		/**
		 * The GetMainFormId is generated by appsbd
		 *
		 * @return string
		 */
		public function get_module_action_prefix() {
			return $this->kernel_object->get_action_prefix() . '-m-' . str_replace( '_', '-', strtolower( $this->get_module_id() ) );
		}


		/**
		 * The __toString is generated by appsbd
		 *
		 * @return false|string
		 */
		public function __toString() {
			return get_class( $this );
		}

		/**
		 * The data is generated by appsbd
		 */
		public function data() {
			$data = new Ajax_Data_Response();
			wp_send_json( $data );
		}

		/**
		 * The confirm is generated by appsbd
		 */
		public function confirm() {
			$data = new Ajax_Confirm_Response();
			wp_send_json( $data );
		}

		/**
		 * The admin script data is generated by appsbd
		 */
		public function admin_script_data() {
		}


		/**
		 * The get admin settings is generated by appsbd
		 */
		public function get_admin_options() {
			$response = new Ajax_Confirm_Response();
			$response->display_with_response( true, $this->options );
		}
		/**
		 * The update request callback is generated by appsbd
		 */
		public function update_request_option() {
			$before_save = $this->options;
			$app_posts   = AppInput::get_posted_data();
			if ( ! empty( $app_posts['action'] ) ) {
				unset( $app_posts['action'] );
			}
			foreach ( $app_posts as $key => $post ) {
				$this->options[ $key ] = $post;
			}
			$is_updated = false;
			if ( $before_save === $this->options ) {
				$this->add_error( 'No change for update' );
			} elseif ( $this->update_option() ) {
					$is_updated = true;
					$this->add_info( 'Saved Successfully' );
			} else {
				$this->add_error( 'No change for update' );
			}
			return $is_updated;
		}
		/**
		 * The ajax request callback is generated by appsbd
		 */
		public function ajax_request_callback() {
			$response = new Ajax_Confirm_Response();
			$response->display_with_response( $this->update_request_option(), $this->options );
		}

		/**
		 * The get plugin url is generated by appsbd
		 *
		 * @param mixed $file_path Its the file for form plugin directory.
		 *
		 * @return string
		 */
		public function get_plugin_url( $file_path ) {
			return $this->kernel_object->get_plugin_url( $file_path );
		}

		/**
		 * The get plugin esc url is generated by appsbd
		 *
		 * @param mixed $file_path Its the file for form plugin directory.
		 */
		public function get_plugin_esc_url( $file_path ) {
			echo esc_url( $this->get_plugin_url( $file_path ) );
		}
		/**
		 * The IsActive is generated by appsbd
		 *
		 * @return bool
		 */
		public function is_active() {
			return true;
		}


		/**
		 * The is_page_check is generated by appsbd
		 *
		 * @param any $page Its page param.
		 *
		 * @return false
		 */
		public function is_page_check( $page ) {
			 return false;
		}

		/**
		 * The on active is generated by appsbd
		 */
		public function on_active() {
		}

		/**
		 * The on deactive is generated by appsbd
		 */
		public function on_deactivate() {
		}

		/**
		 * The on admin scripts is generated by appsbd
		 */
		public function on_admin_scripts_lite() {
		}

		/**
		 * The on admin styles is generated by appsbd
		 */
		public function on_admin_styles_lite() {
		}

		/**
		 * The on client script is generated by appsbd
		 */
		public function on_client_scripts_lite() {
		}

		/**
		 * The on client style is generated by appsbd
		 */
		public function on_client_styles_lite() {
		}


		/**
		 * The LinksActions is generated by appsbd
		 *
		 * @param any $links Its links param.
		 */
		public function links_actions( &$links ) {
		}


		/**
		 * The PluginRowMeta is generated by appsbd
		 *
		 * @param any $links Its links param.
		 */
		public function plugin_row_meta( &$links ) {
		}

		/**
		 * The admin sub menu is generated by appsbd
		 */
		public function admin_sub_menu_lite() {
		}

		/**
		 * The on admin global styles is generated by appsbd
		 */
		public function on_admin_global_styles_lite() {
		}

		/**
		 * The on admin aain option styles is generated by appsbd
		 */
		public function on_admin_main_option_styles_lite() {
		}

		/**
		 * The on admin global scripts is generated by appsbd
		 */
		public function on_admin_global_scripts_lite() {
		}

		/**
		 * The on admin main option scripts is generated by appsbd
		 */
		public function on_admin_main_option_scripts_lite() {
		}
	}
}

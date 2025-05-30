<?php
/**
 * Insights SDK File
 * SDK Version 1.0.0
 *
 * @package dci
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Vtp_Insights_SDK' ) ) {

	/**
	 * Insights SDK Class
	 */
	class Vtp_Insights_SDK {

		/**
		 * Its property version
		 *
		 * @var mixed|string
		 */
		public $version;

		/**
		 * Its property dci_name
		 *
		 * @var string
		 */
		public $dci_name;
		/**
		 * Its property dci_allow_name
		 *
		 * @var string
		 */
		public $dci_allow_name;
		/**
		 * Its property dci_insights_date_name
		 *
		 * @var string
		 */
		public $dci_insights_date_name;
		/**
		 * Its property dci_insights_count_name
		 *
		 * @var mixed
		 */
		public $dci_insights_count_name;
		/**
		 * Its property nonce
		 *
		 * @var false|string
		 */
		public $nonce;
		/**
		 * Its property params
		 *
		 * @var mixed
		 */
		public $params;
		/**
		 * Its property text_domain
		 *
		 * @var mixed|string
		 */
		public $text_domain;
		/**
		 * Its property notice_exits
		 *
		 * @var array
		 */
		public static $notice_exits = array();

		/**
		 * Its constructor
		 *
		 * @param array $params Its param.
		 */
		public function __construct( $params ) {
			$security_key = md5( $params['plugin_name'] );
			if ( ! empty( $params['key_prefix'] ) ) {
				$this->dci_name = 'dci_' . $params['key_prefix'];
			} else {
				$this->dci_name = 'dci_' . str_replace(
					'-',
					'_',
					sanitize_title( $params['plugin_name'] ) . '_' . $security_key
				);
			}
			$this->params      = $params;
			$this->text_domain = isset( $params['text_domain'] ) ? $params['text_domain'] : 'dci';
			$this->version     = isset( $params['sdk_version'] ) ? $params['sdk_version'] : '1.0.0';

			add_action( 'wp_ajax_vtp_dci_sdk_insights', array( $this, 'dci_sdk_insights' ) );
			add_action( 'wp_ajax_vtp_dci_sdk_dismiss_notice', array( $this, 'dci_sdk_dismiss_notice' ) );
			add_action( 'wp_ajax_vtp_dci_sdk_insights_deactivate_feedback', array( $this, 'insights_deactivate_feedback' ) );

			$this->dci_allow_name         = '_dci_allow_status_' . $this->dci_name;
			$this->dci_insights_date_name = '_dci_status_date_' . md5( $params['plugin_name'] );
			$dci_insights_count_name      = '_dci_attempt_count_' . $this->dci_name;
			$dci_status_db                = get_option( $this->dci_allow_name, false );

			$this->nonce = wp_create_nonce( $this->dci_allow_name );

			/**
			 * Deactivate Feedback
			 * Visible only in plugins.php
			 */
			$this->deactivation_feedback( $params );
			if ( ! empty( $this->params['data_skip'] ) ) {
				return;
			}
			/**
			 * Modal Trigger if not init
			 * Show Notice Modal
			 */
			if ( ! $dci_status_db ) {
				$delay_check = $this->show_notice_delay_init( $params );
				if ( $delay_check ) {
					return;
				}

				$this->notice_modal( $params );
				return;
			}

			/**
			 * If Disallow
			 */
			if ( 'disallow' == $dci_status_db ) {
				return;
			}

			/**
			 * Skip & Date Not Expired
			 * Show Notice Modal
			 */
			if ( 'skip' == $dci_status_db && true == $this->check_date() ) {
				$this->notice_modal( $params );
				return;
			}

			/**
			 * Allowed & Date not Expired
			 * No need send data to server
			 * Else Send Data to Server
			 */
			if ( ! $this->check_date() ) {
				return;
			}

			/**
			 * Count attempt every time
			 */
			$dci_attempt = get_option( $dci_insights_count_name, 0 );

			if ( ! $dci_attempt ) {
				update_option( $dci_insights_count_name, 1 );
			}
			update_option( $dci_insights_count_name, $dci_attempt + 1 );

			/**
			 * Next schedule date for attempt
			 */
			update_option( $this->dci_insights_date_name, gmdate( 'Y-m-d', strtotime( '+1 month' ) ) );

			/**
			 * Prepare data
			 */
			$this->data_prepare( $params );
		}

		/**
		 * The show notice delay init is generated by appsbd
		 *
		 * @param mixed $params It is params param.
		 *
		 * @return bool
		 */
		public function show_notice_delay_init( $params ) {
			if ( ! isset( $params['delay_time'] ) ) {
				return false;
			}
			$installed = get_option( $this->dci_name . '_installed', false );

			if ( ! $installed ) {
				update_option( $this->dci_name . '_installed', time() );
			}

			$time = isset( $params['delay_time']['time'] ) ? $params['delay_time']['time'] : 60 * MINUTE_IN_SECONDS;

			$installed = get_option( $this->dci_name . '_installed', false );

			if ( $installed && ( time() - $installed ) < $time ) {
				return true;
			}

			return false;
		}

		/**
		 * Insights Deactivate Feedback
		 */
		public function insights_deactivate_feedback() {

			$api_endpoint = isset( $this->params['api_endpoint'] ) ? $this->params['api_endpoint'] : '';
			$public_key   = $this->post_value( 'public_key' );
			$product_id   = $this->post_value( 'product_id' );

			$feedback = $this->post_value( 'feedback', '' );
			$nonce    = $this->post_value( 'nonce', '' );
			$version  = $this->post_value( 'version', '' );

			if ( ! wp_verify_nonce( $nonce, 'dci_sdk' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Nonce verification failed',
					)
				);
				wp_die();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Denied, you don\'t have right permission',
					)
				);
				wp_die();
			}

			$feedback = json_decode( stripslashes( $feedback ), true );
			if ( is_array( $feedback ) ) {
				$feedback = array_merge( array( 'version' => $version ), $feedback );
			}
			$data = array(
				'api_endpoint' => $api_endpoint,
				'public_key'   => $public_key,
				'product_id'   => $product_id,
				'feedback'     => $feedback,
				'version'      => $version,
			);
			$this->data_prepare( $data );
			wp_send_json(
				array(
					'status'  => 'success',
					'title'   => 'Success',
					'message' => 'Success.',
				)
			);
			wp_die();
		}

		/**
		 * The notice modal is generated by appsbd
		 *
		 * @param mixed $params It is params param.
		 */
		public function notice_modal( $params ) {
			if ( in_array( $this->dci_name, self::$notice_exits ) ) {
				return;
			}
			self::$notice_exits[]    = $this->dci_name;
			$dci_data                = array();
			$dci_data['name']        = $this->dci_name;
			$dci_data['date_name']   = $this->dci_insights_date_name;
			$dci_data['allow_name']  = $this->dci_allow_name;
			$dci_data['nonce']       = wp_create_nonce( 'dci_sdk' );
			$dci_data['slug']        = $params['slug'];
			$dci_data['text_domain'] = $this->text_domain;

			add_action( 'admin_enqueue_scripts', array( $this, 'dci_enqueue_scripts' ) );

			/**
			 * If not current page
			 * Show Notice Modal & Return
			 */
			if ( $params['current_page'] !== $params['menu_slug'] ) {
				if ( ! get_transient( 'dismissed_notice_' . $this->dci_name ) ) {
					add_action( 'admin_notices', array( $this, 'display_global_notice' ) );
				}
				return;
			}

			/**
			 * If Dismissed but always show welcome is true then show notice on welcome page
			 */
			if ( ! isset( $params['always_show_welcome'] ) && get_transient( 'dismissed_notice_' . $this->dci_name ) ) {
				return;
			}

			add_action( 'admin_notices', array( $this, 'display_global_notice' ) );

			if ( isset( $params['popup_notice'] ) && true === $params['popup_notice'] ) {

				include_once __DIR__ . '/notice.php';

				add_action(
					'in_admin_header',
					function () use ( $dci_data ) {
						if ( function_exists( 'dci_popup_notice' ) ) {
							dci_popup_notice( $dci_data );
						}
					},
					99999
				);
			}
		}

		/**
		 * The deactivation feedback is generated by appsbd
		 *
		 * @param mixed $params It is params param.
		 */
		public function deactivation_feedback( $params ) {
			$dci_data                         = array();
			$dci_data['nonce']                = wp_create_nonce( 'dci_sdk' );
			$dci_data['slug']                 = $params['slug'];
			$dci_data['text_domain']          = $this->text_domain;
			$dci_data['api_endpoint']         = $params['api_endpoint'];
			$dci_data['public_key']           = $params['public_key'];
			$dci_data['product_id']           = $params['product_id'];
			$dci_data['core_file']            = isset( $params['core_file'] ) ? $params['core_file'] : false;
			$dci_data['plugin_deactivate_id'] = isset( $params['plugin_deactivate_id'] ) ? $params['plugin_deactivate_id'] : false;
			$dci_data['version']              = isset( $params['version'] ) ? $params['version'] : false;

			add_action( 'admin_enqueue_scripts', array( $this, 'dci_enqueue_scripts' ) );

			if ( isset( $params['deactivate_feedback'] ) && true === $params['deactivate_feedback'] ) {

				include_once __DIR__ . '/deactivate-feedback.php';

				$current_screen = get_admin_page_parent();

				if ( isset( $current_screen ) && ! empty( $current_screen ) && 'plugins.php' === $current_screen ) {
					add_action(
						'in_admin_header',
						function () use ( $dci_data ) {
							if ( function_exists( 'vtp_dci_deactivate_feedback' ) ) {
								vtp_dci_deactivate_feedback( $dci_data );
							}
						},
						99999
					);
				}
			}
		}

		/**
		 * The check date is generated by appsbd
		 *
		 * @return bool
		 */
		public function check_date() {
			$current_date    = strtotime( gmdate( 'Y-m-d' ) );
			$dci_status_date = strtotime( get_option( $this->dci_insights_date_name, false ) );

			if ( ! $dci_status_date ) {
				return true;
			}

			if ( $dci_status_date && $current_date >= $dci_status_date ) {
				return true;
			}
			return false;
		}

		/**
		 * The modal trigger is generated by appsbd
		 *
		 * @return bool
		 */
		public function modal_trigger() {

			if ( ! wp_verify_nonce( $this->dci_allow_name, $this->nonce ) ) {
				echo 'Nonce Verification Failed';
				return false;
			}

			$sanitized_status = sanitize_text_field( $this->get_value( 'dci_allow_status' ) );

			if ( 'skip' == $sanitized_status ) {
				update_option( $this->dci_allow_name, 'skip' );
				/**
				 * Next schedule date for attempt
				 */
				update_option( $this->dci_insights_date_name, gmdate( 'Y-m-d', strtotime( '+1 month' ) ) );
				return false;
			} elseif ( 'yes' == $sanitized_status ) {
				update_option( $this->dci_allow_name, 'yes' );
				return true;
			}

			return false;
		}

		/**
		 * Reset Options Settings
		 *
		 * @return void
		 */
		public function reset_settings() {
			delete_option( $this->dci_allow_name );
			delete_option( $this->dci_insights_date_name );
		}

		/**
		 * The data prepare is generated by appsbd
		 *
		 * @param mixed $params It is params param.
		 */
		public function data_prepare( $params ) {
			$server_url  = isset( $params['api_endpoint'] ) ? $params['api_endpoint'] : false;
			$public_key  = isset( $params['public_key'] ) ? $params['public_key'] : false;
			$custom_data = isset( $params['custom_data'] ) ? $params['custom_data'] : false;
			$product_id  = isset( $params['product_id'] ) ? $params['product_id'] : false;

			if ( ! $server_url || ! $public_key ) {
				return;
			}

			/**
			 * ==================================
			 *
			 * Start Own Custom Important Data
			 *
			 * ==================================
			 */

			/**
			 * ==================================
			 *
			 * End Own Custom Important Data
			 *
			 * ==================================
			 */

			$data               = array();
			$data['public_key'] = $public_key;
			$data['product_id'] = $product_id;
			if ( ! empty( $this->params['custom_data'] ) ) {
				$data['custom_data'] = $this->params['custom_data'];
			}

			if ( isset( $params['feedback'] ) && ! empty( $params['feedback'] ) ) {
				$data['feedback'] = $params['feedback'];
			}

			$non_sensitive_data = $this->dci_non_sensitve_data();
			$data               = array_merge( $data, $non_sensitive_data );

			$this->dci_send_data_to_server( $server_url, $data );
		}

		/**
		 * Get the list of active and inactive plugins
		 *
		 * @return array
		 */
		private function get_all_plugins() {

			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$plugins             = get_plugins();
			$active_plugins_keys = get_option( 'active_plugins', array() );
			$active_plugins      = array();

			foreach ( $plugins as $k => $v ) {

				$formatted         = array();
				$formatted['name'] = wp_strip_all_tags( $v['Name'] );

				if ( isset( $v['Version'] ) ) {
					$formatted['version'] = wp_strip_all_tags( $v['Version'] );
				}

				if ( isset( $v['Author'] ) ) {
					$formatted['author'] = wp_strip_all_tags( $v['Author'] );
				}

				if ( isset( $v['Network'] ) ) {
					$formatted['network'] = wp_strip_all_tags( $v['Network'] );
				}

				if ( isset( $v['PluginURI'] ) ) {
					$formatted['plugin_uri'] = wp_strip_all_tags( $v['PluginURI'] );
				}

				if ( in_array( $k, $active_plugins_keys, true ) ) {

					unset( $plugins[ $k ] );
					$active_plugins[ $k ] = $formatted;
				} else {
					$plugins[ $k ] = $formatted;
				}
			}

			$plugins_data = array();
			foreach ( $active_plugins as $slug => $_plugin ) {
				$slug = strstr( $slug, '/', true );

				if ( ! $slug ) {
					continue;
				}

				$plugins_data[ $slug ] = array(
					'name'    => isset( $_plugin['name'] ) ? $_plugin['name'] : '',
					'version' => isset( $_plugin['version'] ) ? $_plugin['version'] : '',
				);
			}

			return array(
				'active_plugins'   => $active_plugins,
				'inactive_plugins' => $plugins,
				'plugins_data'     => $plugins_data,
			);
		}

		/**
		 * Non sensitive data
		 *
		 * @return array
		 */
		public function dci_non_sensitve_data() {
			$current_user = wp_get_current_user();
			$all_plugins  = $this->get_all_plugins();

			$users = get_users(
				array(
					'role'    => 'administrator',
					'orderby' => 'ID',
					'order'   => 'ASC',
					'number'  => 1,
					'paged'   => 1,
				)
			);

			$admin_user = ( is_array( $users ) && ! empty( $users ) ) ? $users[0] : false;
			$first_name = $current_user->first_name;
			$last_name  = $current_user->last_name;

			if ( empty( $first_name ) && empty( $last_name ) ) {
				$first_name = $current_user->display_name;
				$last_name  = null;
			}

			if ( $admin_user ) {
				$first_name = $admin_user->first_name ? $admin_user->first_name : $admin_user->display_name;
				$last_name  = $admin_user->last_name;
			}

			$theme = wp_get_theme( get_stylesheet() );

			$data = array(
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'email'        => get_option( 'admin_email' ),
				'user_role'    => $current_user->roles[0],
				'website_url'  => site_url(),
				'website_data' => array(
					'sdk_version'            => $this->version,
					'website_name'           => get_bloginfo( 'name' ),
					'wp_version'             => get_bloginfo( 'version' ),
					'php_version'            => phpversion(),
					'locale'                 => get_locale(),
					'wp_multisite'           => is_multisite() ? 'Yes' : 'No',
					'wp_memory_limit'        => defined( WP_MEMORY_LIMIT ) ? WP_MEMORY_LIMIT : false,
					'memory_limit'           => ini_get( 'memory_limit' ),

					'inactive_plugins'       => $all_plugins['inactive_plugins'],
					'active_plugins'         => $all_plugins['active_plugins'],
					'active_plugins_count'   => count( $all_plugins['active_plugins'] ),
					'inactive_plugins_count' => count( $all_plugins['inactive_plugins'] ),

					'theme_name'             => $theme->get( 'Name' ),
					'theme_version'          => $theme->get( 'Version' ),
					'theme_uri'              => $theme->get( 'ThemeURI' ),
					'theme_author'           => $theme->get( 'Author' ),
				),
			);

			return $data;
		}

		/**
		 * The dci send data to server is generated by appsbd
		 *
		 * @param mixed $server_url It is server_url param.
		 * @param mixed $data It is data param.
		 */
		public function dci_send_data_to_server( $server_url, $data = null ) {

			$args = array(
				'method'  => 'POST',
				'timeout' => 60,
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-API-KEY'    => $data['public_key'],
				),
				'body'    => wp_json_encode( $data ),
			);

			$response = wp_remote_request( $server_url, $args );

			if ( is_wp_error( $response ) ) {
				$this->reset_settings();
			} else {
				$response_data = wp_remote_retrieve_body( $response );
				$response_data = json_decode( $response_data, true );
				if ( isset( $response_data['data']['status'] ) && 401 == $response_data['data']['status'] ) {
					update_option( $this->dci_insights_date_name, gmdate( 'Y-m-d', strtotime( '+3 days' ) ) );
				}
			}
		}

		/**
		 * The post value is generated by appsbd
		 *
		 * @param mixed $key It is key param.
		 * @param mixed $default It is default param.
		 *
		 * @return mixed|string
		 */
		public function post_value( $key, $default = '' ) {
			if ( check_ajax_referer( 'vitepos' ) || vitepos_is_rest() ) {
				return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
			}
		}

		/**
		 * The get value is generated by appsbd
		 *
		 * @param mixed $key It is key param.
		 * @param mixed $default It is default param.
		 *
		 * @return mixed|string
		 */
		public function get_value( $key, $default = '' ) {
			return \Appsbd_Lite\V1\libs\AppInput::get_value( $key, $default );
		}
		/**
		 * Ajax callback
		 */
		public function dci_sdk_insights() {
			$sanitized_status = $this->post_value( 'button_val' );
			$nonce            = $this->post_value( 'nonce' );
			$date_name        = $this->post_value( 'date_name' );
			$allow_name       = $this->post_value( 'allow_name' );

			if ( ! wp_verify_nonce( $nonce, 'dci_sdk' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Nonce verification failed2',
					)
				);
				wp_die();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Denied, you don\'t have right permission',
					)
				);
				wp_die();
			}

			if ( 'disallow' == $sanitized_status ) {
				update_option( $allow_name, 'disallow' );
			}

			if ( 'skip' == $sanitized_status ) {
				update_option( $allow_name, 'skip' );
				/**
				 * Next schedule date for attempt
				 */
				update_option( $date_name, gmdate( 'Y-m-d', strtotime( '+1 month' ) ) );
			} elseif ( 'yes' == $sanitized_status ) {
				update_option( $allow_name, 'yes' );
			}

			wp_send_json(
				array(
					'status'  => 'success',
					'title'   => 'Success',
					'message' => 'Success.',
				)
			);
			wp_die();
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function dci_enqueue_scripts() {
		}

		/**
		 * Display Global Notice
		 *
		 * @return void
		 */
		public function display_global_notice() {
			$menu_slug = isset( $this->params['menu_slug'] ) ? $this->params['menu_slug'] : 'javascript:void(0);';

			$admin_url = add_query_arg(
				array(
					'page' => $menu_slug,
				),
				admin_url( 'admin.php' )
			);

			$plugin_title = isset( $this->params['plugin_title'] ) ? $this->params['plugin_title'] : '';
			$plugin_msg   = isset( $this->params['plugin_msg'] ) ? $this->params['plugin_msg'] : '';
			$plugin_icon  = isset( $this->params['plugin_icon'] ) ? $this->params['plugin_icon'] : '';

			?>
			<div
					class="apbd-dci-global-notice apbd-dci-notice-data notice notice-success is-dismissible <?php echo esc_attr( substr( $this->dci_name, 0, -33 ) ); ?>">
				<div class="apbd-dci-global-header bdt-apbd-dci-notice-global-header">

					<div class="apbd-dci-notice-content">
						<h3>
							<i class="vps vps-vite-pos-full"></i>
						</h3>
						<div class="custom-msg"><?php echo wp_kses_post( $plugin_msg ); ?></div>
						<input type="hidden" name="dci_name" value="<?php echo esc_html( $this->dci_name ); ?>">
						<input type="hidden" name="dci_date_name" value="<?php echo esc_html( $this->dci_insights_date_name ); ?>">
						<input type="hidden" name="dci_allow_name" value="<?php echo esc_html( $this->dci_allow_name ); ?>">
						<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'dci_sdk' ) ); ?>">

						<div class="apbd-dci-notice-button-wrap">
							<button name="dci_allow_status" value="yes" class="apbd-dci-button-allow">
								<?php esc_html_e( 'Yes, I\'d Love To Contribute', 'vitepos-lite' ); ?>
							</button>
							<button name="dci_allow_status" value="skip" class="apbd-dci-button-skip">
								<?php esc_html_e( 'Skip For Now', 'vitepos-lite' ); ?>
							</button>
							<button name="dci_allow_status" value="disallow" class="apbd-dci-button-disallow apbd-dci-button-danger">
								<?php esc_html_e( 'No Thanks', 'vitepos-lite' ); ?>
							</button>
						</div>
					</div>
				</div>

			</div>

			<?php
		}

		/**
		 * The   display global notice is generated by appsbd
		 */
		public function _display_global_notice() {
			$menu_slug = isset( $this->params['menu_slug'] ) ? $this->params['menu_slug'] : 'javascript:void(0);';

			$admin_url = add_query_arg(
				array(
					'page' => $menu_slug,
				),
				admin_url( 'admin.php' )
			);

			$plugin_title = isset( $this->params['plugin_title'] ) ? $this->params['plugin_title'] : '';
			$plugin_msg   = isset( $this->params['plugin_msg'] ) ? $this->params['plugin_msg'] : '';
			$plugin_icon  = isset( $this->params['plugin_icon'] ) ? $this->params['plugin_icon'] : '';

			?>
			<div class="apbd-dci-global-notice apbd-dci-notice-data notice notice-success is-dismissible">
				<div class="apbd-dci-global-header">
					<?php if ( ! empty( $plugin_icon ) ) : ?>
						<div>
							<img src="<?php echo esc_url( $plugin_icon ); ?>" alt="icon">
						</div>
					<?php endif; ?>
					<h3>
						<?php echo wp_kses_post( $plugin_title ); ?>
					</h3>
				</div>
				<?php echo wp_kses_post( $plugin_msg ); ?>
				<p>
					<?php esc_html_e( 'What we', 'vitepos-lite' ); ?> <a
							href="<?php echo esc_url( $admin_url ); ?>"><?php esc_html_e( 'collect', 'vitepos-lite' ); ?></a>?
				</p>
				<input type="hidden" name="dci_name" value="<?php echo esc_html( $this->dci_name ); ?>">
				<input type="hidden" name="dci_date_name" value="<?php echo esc_html( $this->dci_insights_date_name ); ?>">
				<input type="hidden" name="dci_allow_name" value="<?php echo esc_html( $this->dci_allow_name ); ?>">
				<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'dci_sdk' ) ); ?>">
				<p>
					<button name="dci_allow_status" value="yes" class="button button-primary apbd-dci-button-allow">
						<?php esc_html_e( 'Allow', 'vitepos-lite' ); ?>
					</button>
					<button name="dci_allow_status" value="skip" class="button apbd-dci-button-skip button-secondary">
						<?php esc_html_e( 'I\'ll Skip For Now', 'vitepos-lite' ); ?>
					</button>
					<button name="dci_allow_status" value="disallow" class="button apbd-dci-button-disallow apbd-dci-button-danger">
						<?php esc_html_e( 'Don\'t Allow', 'vitepos-lite' ); ?>
					</button>
				</p>
			</div>
			<?php
		}

		/**
		 * Dismiss Notice
		 *
		 * @return void
		 */
		public function dci_sdk_dismiss_notice() {
			$nonce    = $this->post_value( 'nonce' );
			$dci_name = $this->post_value( 'dci_name' );

			if ( ! wp_verify_nonce( $nonce, 'dci_sdk' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Nonce verification failed',
					)
				);
				wp_die();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json(
					array(
						'status'  => 'error',
						'title'   => 'Error',
						'message' => 'Denied, you don\'t have right permission',
					)
				);
				wp_die();
			}

			set_transient( 'dismissed_notice_' . $dci_name, true, 7 * DAY_IN_SECONDS );

			wp_send_json(
				array(
					'status'  => 'success',
					'title'   => 'Success',
					'message' => 'Success.',
				)
			);
			wp_die();
		}
	}
}


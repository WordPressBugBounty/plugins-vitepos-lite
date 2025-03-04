<?php
/**
 * Its api for user
 *
 * @since: 12/07/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos_Lite\Api\V1
 */

namespace VitePos_Lite\Api\V1;

use Appsbd_Lite\V1\libs\API_Data_Response;
use VitePos_Lite\Libs\API_Base;
use VitePos_Lite\Libs\POS_Customer;
use VitePos_Lite\Models\Database\Mapbd_Pos_Cash_Drawer;
use VitePos_Lite\Models\Database\Mapbd_Pos_Role;
use VitePos_Lite\Models\Database\Mapbd_Pos_Warehouse;
use VitePos_Lite\Modules\POS_Settings;

/**
 * Class pos_user_api
 *
 * @package VitePos_Lite\Api\V1
 */
class Pos_User_Api extends API_Base {

	/**
	 * The set api base is generated by appsbd
	 *
	 * @return mixed|string
	 */
	public function set_api_base() {
		return 'user';
	}

	/**
	 * The routes is generated by appsbd
	 *
	 * @return mixed|void
	 */
	public function routes() {
		$this->register_rest_route( 'POST', 'login', array( $this, 'user_login' ) );
		$this->register_rest_route( 'GET', 'logout', array( $this, 'user_logout' ) );
		$this->register_rest_route( 'POST', 'list', array( $this, 'user_list' ) );
		$this->register_rest_route( 'POST', 'change-pass', array( $this, 'change_pass' ) );
		$this->register_rest_route( 'POST', 'change-pass-force', array( $this, 'change_pass_force' ) );
		$this->register_rest_route( 'POST', 'delete-user', array( $this, 'delete_user' ) );
		$this->register_rest_route( 'GET', 'close-cash-drawer', array( $this, 'close_cash_drawer' ) );
		$this->register_rest_route( 'GET', 'cash-drawer-list', array( $this, 'cash_drawer_list' ) );
		$this->register_rest_route( 'GET', 'roles', array( $this, 'roles' ) );
		$this->register_rest_route( 'POST', 'create', array( $this, 'create_user' ) );
		$this->register_rest_route( 'POST', 'outlet-panel', array( $this, 'outlet_panel' ) );
		$this->register_rest_route( 'GET', 'details/(?P<id>\d+)', array( $this, 'user_details' ) );
		$this->register_rest_route( 'GET', 'current-user', array( $this, 'api_current_user' ) );
		$this->register_rest_route( 'GET', 'get-logged-user', array( $this, 'get_logged_user' ) );
	}

	/**
	 * The set route permission is generated by appsbd
	 *
	 * @param \VitePos_Lite\Libs\any $route Its string.
	 *
	 * @return bool
	 */
	public function set_route_permission( $route ) {
		if ( 'login' == $route ) {
			return true;
		} elseif ( 'logout' == $route ) {
			return true;
		} elseif ( 'get-logged-user' == $route ) {
			return true;
		} elseif ( 'create' == $route ) {
			return current_user_can( 'user-add' ) || current_user_can( 'user-edit' );
		} elseif ( 'delete-user' == $route ) {
			return current_user_can( 'user-delete' );
		} else {
			return POS_Settings::is_pos_user();
		}
		return parent::set_route_permission( $route );
	}

	/**
	 * The user login is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function user_login() {
		if ( ! POS_Settings::check_captcha( $this->payload ) ) {
			$this->response->set_response( false, '' );
			return $this->response->get_response();
		}
		$credentials               = array();
		$credentials['user_login'] = sanitize_text_field( $this->payload['username'] );

		$credentials['user_password'] = $this->payload['password'];
		if ( ! empty( $credentials['user_login'] ) ) {
			if ( is_email( $credentials['user_login'] ) ) {
				$user = get_user_by( 'email', $credentials['user_login'] );
				if ( ! empty( $user->user_login ) ) {
					$credentials['user_login'] = $user->user_login;
				}
			} else {
				$user = get_user_by( 'login', $credentials['user_login'] );
			}

			if ( ! empty( $user ) ) {
				if ( POS_Settings::is_pos_user( $user ) ) {
					$user = wp_signon( $credentials, false );
					if ( is_wp_error( $user ) ) {
						$this->add_error( $user->get_error_message() );
						$this->response->set_response( false, '', $credentials );
						return $this->response->get_response();
					} else {
						wp_set_current_user( $user->ID );
						wp_set_auth_cookie( $user->ID, true );
						$response_data = $this->get_logged_user_response( $user );
						$this->response->set_response( true, 'Logged in successfully', $response_data );
						return $this->response->get_response();
					}
				} else {
					$this->add_error( 'You do not have permission to access this link' );
					$this->response->set_response( false );
					return $this->response->get_response();
				}
			} else {
				$this->add_error( 'Invalid login information' );
				$this->response->set_response( false );
				return $this->response->get_response();
			}
		} else {
			$this->add_error( 'Username is required' );
			$this->response->set_response( false );
			return $this->response->get_response();
		}
	}

	/**
	 * The user login is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function get_logged_user() {
		$logged_id = $this->get_current_user_id();
		if ( ! empty( $logged_id ) && is_user_logged_in() ) {
			$user = get_user_by( 'id', $logged_id );
			if ( POS_Settings::is_pos_user( $user ) ) {
				$response_data = $this->get_logged_user_response( $user );
				$this->response->set_response( true, 'Logged in successfully', $response_data );

				return $this->response->get_response();
			} else {
				wp_logout();
				$this->add_error( "You don't have permission to access this link" );
				$this->response->set_response( false );
				return $this->response->get_response();
			}
		} else {
			$this->add_error( 'No logged in user' );
			$this->response->set_response( false, '', null );
			return $this->response->get_response();
		}
	}

	/**
	 * The get logged user response is generated by appsbd
	 *
	 * @param \WP_User $user Its the user object.
	 *
	 * @return mixed|void
	 */
	private function get_logged_user_response( $user ) {
		$response_data                = new \stdClass();
		$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
		$response_data->username      = $user->user_login;
		$response_data->name          = $user->first_name . ' ' . $user->last_name;
		$response_data->logged_in     = is_user_logged_in();
		if ( empty( trim( $response_data->name ) ) ) {
			$response_data->name = $user->display_name;
		}
		$response_data->img          = get_avatar_url( $user->ID );
		$response_data->caps         = Mapbd_Pos_Role::set_capabilities_by_role( $user->caps, $user );
		$response_data->outlets      = Mapbd_Pos_Warehouse::get_outlet_details( $user );
		$response_data->is_temp_pass = get_user_meta( $user->ID, 'force_pw_change', true );

		/**
		 * Its for logged user
		 *
		 * @since 1.0
		 */
		$response_data = apply_filters( 'apbd-vitepos/filter/logged-user', $response_data, $user );
		/**
		 * Its for logged user
		 *
		 * @since 1.0
		 */
		$response_data = apply_filters( 'apbd-auth/filter/logged-user', $response_data, $user );
		return $response_data;
	}

	/**
	 * The user logout is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function user_logout() {
		wp_logout();
		if ( is_user_logged_in() ) {
			$this->response->set_response( false, 'Logout failed' );
			return $this->response;
		} else {
			$this->response->set_response( true, 'Logout successful' );
			return $this->response;
		}
	}

	/**
	 * The delete user is generated by appsbd.
	 *
	 *  @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function delete_user() {
		if ( ! empty( $this->payload ) ) {
			$id = intval( $this->payload['id'] );
			require_once ABSPATH . 'wp-admin/includes/user.php';
			$user = get_user_by( 'ID', $id );
			if ( ! empty( $user ) ) {
				if ( wp_delete_user( $user->ID ) ) {
					$this->add_info( 'Successfully deleted' );
					$this->response->set_response( true, '' );
					return $this->response;
				}
			}
		}
		$this->add_error( 'Delete failed' );
		$this->response->set_response( false, '' );
		return $this->response;
	}

	/**
	 * The change pass is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function change_pass() {
		if ( ! empty( $this->payload['newPass'] ) && ! empty( $this->payload['currentPass'] ) ) {

			$id = $this->get_current_user_id();
			if ( $id ) {
				$user_data = get_user_by( 'ID', $id );
				if ( ! empty( $user_data->ID ) ) {
					if ( wp_check_password( $this->payload['currentPass'], $user_data->user_pass, $user_data->ID ) ) {
						if ( $this->payload['currentPass'] != $this->payload['newPass'] ) {
							wp_set_password( $this->payload['newPass'], $user_data->ID );
							$credentials                  = array();
							$credentials['user_login']    = $user_data->user_login;
							$credentials['user_password'] = $this->payload['newPass'];
							$user                         = wp_signon( $credentials, false );
							$response_data                = new \stdClass();
							if ( ! is_wp_error( $user ) ) {
								$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
							} else {
								$response_data->logout = true;
							}
							$this->add_info( 'Password changed successfully.' );
							$this->response->set_response( true, '', $response_data );

							return $this->response;
						} else {
							$this->add_info( 'Password changed successfully.' );
							$this->response->set_response( true, '', null );

							return $this->response;
						}
					} else {
						$this->response->set_response( false, 'Old password not matched,try again.' );

						return $this->response;
					}
				} else {
					$this->response->set_response( false, 'Invalid request.' );

					return $this->response;
				}
			} else {
				$this->add_error( 'Please login again,no user found logged in.' );
				$this->response->set_response( false );

				return $this->response;
			}
		}
	}
	/**
	 * The change pass force is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function change_pass_force() {
		if ( ! empty( $this->payload['newPass'] ) ) {
			if ( $this->payload['user_id'] ) {
				if ( current_user_can( 'change-any-user-pass' ) ) {
					$user_data = get_user_by( 'ID', $this->payload['user_id'] );
					if ( ! empty( $user_data->ID ) ) {
						wp_set_password( $this->payload['newPass'], $user_data->ID );
						if ( metadata_exists( 'user', $user_data->ID, 'force_pw_change' ) ) {
							update_user_meta( $user_data->ID, 'force_pw_change', 'Y' );
						} else {
							add_user_meta( $user_data->ID, 'force_pw_change', 'Y' );
						}
						/**
						 * Its send user temporary
						 *
						 * @since 1.0
						 */
						do_action( 'apbd-vtpos/action/send-temp-password-email', $user_data, $this->payload['newPass'] );

						$this->add_info( 'Password changed successfully.' );
						$this->response->set_response( true );

						return $this->response;
					} else {
						$this->response->set_response( false, 'Invalid request no user found' );

						return $this->response->get_response();
					}
				} else {
					$this->response->set_response( false, 'You do not have permission to do this' );
					return $this->response->get_response();
				}
			} elseif ( $this->get_current_user_id() ) {
					$user_data = get_user_by( 'ID', $this->get_current_user_id() );
					wp_set_password( $this->payload['newPass'], $user_data->ID );
				if ( metadata_exists( 'user', $user_data->ID, 'force_pw_change' ) ) {
					update_user_meta( $user_data->ID, 'force_pw_change', 'N' );
				} else {
					add_user_meta( $user_data->ID, 'force_pw_change', 'N' );
				}
					$credentials                  = array();
					$credentials['user_login']    = $user_data->user_login;
					$credentials['user_password'] = $this->payload['newPass'];
					$user                         = wp_signon( $credentials, false );
					$response_data                = new \stdClass();
				if ( ! is_wp_error( $user ) ) {
					$response_data->wp_rest_nonce = wp_create_nonce( 'wp_rest' );
					$response_data->is_temp_pass  = get_user_meta( $user->ID, 'force_pw_change', true );
				} else {
					$response_data->logout = true;
				}
					$this->add_info( 'Password changed successfully.' );
					$this->response->set_response( true, '', $response_data );

					return $this->response;
			} else {
				$this->add_error( 'Invalid Request' );
				$this->response->set_response( false );
			}
		} else {
			$this->add_error( 'Password can not be set as empty' );
			$this->response->set_response( false );
			return $this->response;
		}
	}

	/**
	 * The roles is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function roles() {
		$response_roles = Mapbd_pos_role::get_role_list();
		$response_roles = array_filter(
			$response_roles,
			static function ( $element ) {
				return 'administrator' !== $element->slug;
			}
		);
		$this->response->set_response( true, '', $response_roles );
		return $this->response;
	}

	/**
	 * The get user object is generated by appsbd
	 *
	 * @param any $user Its string.
	 *
	 * @return \stdClass
	 */
	public function get_user_object( $user ) {
		$users_obj             = new \stdClass();
		$users_obj->id         = $user->ID;
		$users_obj->first_name = $user->first_name;
		$users_obj->last_name  = $user->last_name;
		$users_obj->username   = $user->user_nicename;
		$users_obj->email      = $user->user_email;
		$users_obj->city       = get_user_meta( $user->ID, 'billing_city', true );

		$users_obj->contact_no  = get_user_meta( $user->ID, 'billing_phone', true );
		$users_obj->street      = get_user_meta( $user->ID, 'billing_address_1', true );
		$users_obj->country     = get_user_meta( $user->ID, 'billing_country', true );
		$users_obj->postcode    = get_user_meta( $user->ID, 'billing_postcode', true );
		$users_obj->designation = get_user_meta( $user->ID, 'designation', true );
		$users_obj->outlet_id   = get_user_meta( $user->ID, 'outlet_id', true );
		$users_obj->role        = array_shift( $user->roles );
		if ( '' == $users_obj->outlet_id ) {
			$users_obj->outlet_id = array();
		}
		return $users_obj;
	}

	/**
	 * The user list is generated by appsbd
	 *
	 * @return API_Data_Response
	 */
	public function user_list() {
		$page          = $this->get_payload( 'page', 1 );
		$limit         = $this->get_payload( 'limit', 20 );
		$response_user = array();
		$response_data = new API_Data_Response();
		$src_props     = $this->get_payload( 'src_by', array() );
		$sort_by_props = $this->get_payload( 'sort_by', array() );

		$args = array(
			'role__not_in' => array( 'customer', 'subscriber', 'administrator' ),
			'count_total'  => true,
			'offset'       => $limit,
			'paged'        => $page,
		);
		if ( ! POS_Settings::is_admin_user() && ! current_user_can( 'any-outlet-user-create' ) ) {
			$outlets = get_user_meta( $this->get_current_user_id(), 'outlet_id', true );
			if ( is_array( $outlets ) ) {
				$args['meta_query'][] = array(
					'key'     => 'outlet_id',

					'value'   => '"(' . implode( '|', $outlets ) . ')"',
					'compare' => 'REGEXP',
				);
			} else {
				$this->add_error( "You don't have permission to view user of this outlet" );
				$response_data->set_total_records( 0 );
				$this->response->set_response( false, '', $response_data );
				return $this->response->get_response();
			}
		}
		POS_Customer::set_search_param( $src_props, $args );
		POS_Customer::set_sort_param( $sort_by_props, $args );
		$user_search = new \WP_User_Query( $args );
		$total_user  = $user_search->get_total();
		$users       = $user_search->get_results();
		foreach ( $users as $user ) {
			$response_user[] = $this->get_user_object( $user );
		}
		$response_data->limit = $this->payload['limit'];
		$response_data->page  = $this->payload['page'];
		if ( $response_data->set_total_records( $total_user ) ) {
			$response_data->rowdata = $response_user;
		}
		return $response_data;
	}

	/**
	 * The getUserObjectById is generated by appsbd
	 *
	 * @param any $id Its Integer.
	 *
	 * @return \stdClass|stdClass|null
	 */
	private function get_user_object_by_id( $id ) {
		 $user = get_user_by( 'id', $id );
		if ( ! empty( $user ) ) {
			return $this->get_user_object( $user );
		}
		return null;
	}

	/**
	 * The current user is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function api_current_user() {
		$user_id = $this->get_current_user_id() ? $this->get_current_user_id() : 1;
		if ( ! empty( $user_id ) ) {
			$id            = intval( $user_id );
			$user_obj      = $this->get_user_object_by_id( $id );
			$user_obj->img = get_avatar_url( $user_id );
			$this->set_response( true, 'data found', $user_obj );
			return $this->response;
		}
		$this->set_response( false, 'data not found or invalid param' );
		return $this->response;
	}

	/**
	 * The user details is generated by appsbd
	 *
	 * @param any $data Its string.
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function user_details( $data ) {
		if ( ! empty( $data['id'] ) ) {
			$id       = intval( $data['id'] );
			$user_obj = $this->get_user_object_by_id( $id );
			$this->set_response( true, 'data found', $user_obj );
			return $this->response;
		}
		$this->set_response( false, 'data not found or invalid param' );
		return $this->response;
	}

	/**
	 * The outlet panel is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function outlet_panel() {
		$outlet_place                 = new \stdClass();
		$outlet_place->outlet         = $this->payload['outlet'];
		$outlet_place->counter        = $this->payload['counter'];
		$outlet_place->is_new         = $this->get_payload( 'is_new', false );
		$existing_drawer              = Mapbd_Pos_Cash_Drawer::get_by_counter( $outlet_place->outlet, $outlet_place->counter, $this->get_current_user_id() );
		$outlet_place->cd_balance     = ! empty( $existing_drawer->closing_balance ) ? $existing_drawer->closing_balance : 0;
		$outlet_place->cash_drawer_id = ! empty( $existing_drawer->id ) ? $existing_drawer->id : 0;
		$outlet_place->is_submitted   = $this->payload['is_submitted'];
		if ( ! empty( $this->payload['is_new'] ) ) {

			$outlet_place->cd_balance = $this->payload['cd_balance'];
			$cash_drawar              = Mapbd_Pos_Cash_Drawer::create_by_counter( $outlet_place->cd_balance, $outlet_place->outlet, $outlet_place->counter, $this->get_current_user_id() );
			if ( ! empty( $cash_drawar->id ) ) {
				$outlet_place->cash_drawer_id = ! empty( $cash_drawar->id ) ? $cash_drawar->id : 0;
			}
		}
		$this->set_response( true, 'Data have not found or invalid param', $outlet_place );
		return $this->response->get_response();
	}

	/**
	 * The close cash drawer is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function close_cash_drawer() {
		$outlet  = $this->get_outlet_id();
		$counter = $this->get_counter_id();
		if ( empty( $outlet ) || empty( $counter ) ) {
			$this->add_info( 'Request outlet or counter empty' );
			$this->set_response( false );
			return $this->response->get_response();
		}
		$existing_drawer = Mapbd_Pos_Cash_Drawer::get_by_counter( $outlet, $counter, $this->get_current_user_id() );
		if ( ! empty( $existing_drawer ) && $existing_drawer->set_close_drawer() ) {
			$this->add_info( 'Successfully closed' );
			$this->set_response( true );
			return $this->response->get_response();
		}
		$this->add_error( 'Drawer close failed' );
		$this->set_response( false );
		return $this->response->get_response();
	}
	/**
	 * The close cash drawer is generated by appsbd
	 *
	 * @return \Appsbd\V1\libs\API_Response
	 */
	public function cash_drawer_list() {

		$drawer_list = Mapbd_Pos_Cash_Drawer::get_cash_drawer_list( $this->get_current_user_id() );
		if ( ! empty( $drawer_list ) ) {
			$this->add_info( 'Successfully closed' );
			$this->set_response( true, '', $drawer_list );
			return $this->response->get_response();
		}
		$this->add_error( 'Drawer list not found' );
		$this->set_response( false );
		return $this->response->get_response();
	}

	/**
	 * The create user is generated by appsbd
	 *
	 * @return \Appsbd_Lite\V1\libs\API_Response
	 */
	public function create_user() {
		if ( ! empty( $this->payload ) ) {
			$old_cus = get_user_by( 'ID', $this->payload['id'] );
			if ( ! empty( $old_cus ) ) {
				if ( ! current_user_can( 'user-edit' ) ) {
					$this->add_error( 'You do not have permission to do this' );
					$this->response->set_response( false, '' );
					return $this->response->get_response();
				}
				$user_obj = new POS_Customer();
				$user_obj->set_from_array( $this->payload );
				$outlet_id           = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = serialize( $outlet_id );
				if ( $user_obj->is_valid_form( false ) ) {
					if ( $user_obj->update_user() ) {
						$r_user_obj = $this->get_user_object_by_id( $this->payload['id'] );
						$this->response->set_response( true, 'Successfully updated' );
					} else {
						$this->add_error( 'Not updated' );
						$this->response->set_response( false, '' );
					}
				} else {
					$this->add_error( 'Form is not valid' );
					$this->response->set_response( false );
				}
				return $this->response->get_response();
			} else {
				if ( ! current_user_can( 'user-add' ) ) {
					$this->add_error( 'You do not have permission to do this' );
					$this->response->set_response( false, '' );
					return $this->response->get_response();
				}
				$user_obj = new POS_Customer();
				$user_obj->set_from_array( $this->payload );
				$outlet_id           = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = $this->get_payload( 'outlet_id' );
				$user_obj->outlet_id = serialize( $outlet_id );
				$user_obj->added_by( $this->get_current_user_id() );
				if ( $user_obj->is_valid_form( true ) ) {
					if ( $user_obj->save_user() ) {
						$wp_user = get_user_by( 'email', $user_obj->email );
						/**
						 * Its send user temporary
						 *
						 * @since 1.0
						 */
						do_action( 'apbd-vtpos/action/send-temp-password-email', $wp_user, $user_obj->password );

						$this->response->set_response( true, 'Successfully created', $user_obj );
					} else {
						$this->response->set_response( false, \Appsbd_Lite\V1\Core\Kernel_Lite::get_msg_for_api(), $user_obj );
					}

					return $this->response;
				} else {
					$this->response->set_response( false, \Appsbd_Lite\V1\Core\Kernel_Lite::get_msg_for_api(), $user_obj );
					return $this->response;
				}
			}
		} else {
			$this->response->set_response( false, 'Error on creation' );
			return $this->response;
		}
	}
}

<?php
/**
 * Its for Pos Role module
 *
 * @package VitePos_Lite\Modules
 */

namespace VitePos_Lite\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Appsbd_Lite\V1\Core\BaseModule;
use Appsbd_Lite\V1\Libs\ACL_Resource;
use Appsbd_Lite\V1\libs\Ajax_Confirm_Response;
use Appsbd_Lite\V1\libs\Ajax_Data_Response;
use Appsbd_Lite\V1\libs\Ajax_Response;
use Appsbd_Lite\V1\libs\AppInput;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications\Transformers\Count;
use VitePos_Lite\Core\Vitepos_Module;
use VitePos_Lite\Models\Database\Mapbd_Pos_Role;
use VitePos_Lite\Models\Database\Mapbd_Pos_Role_Access;

/**
 * Class Apbd_pos_role
 */
class POS_Role extends Vitepos_Module {
	/**
	 * The initialize is generated by appsbd
	 */
	public function initialize() {

		add_action( 'apbd-vtpos/action/role-added', array( $this, 'new_role_added' ) );
		add_action( 'apbd-vtpos/action/role-updated', array( $this, 'updated_role' ) );
		add_action( 'apbd-vtpos/action/role-deleted', array( $this, 'removed_role' ) );
		add_filter( 'editable_roles', array( $this, 'role_prefix' ), 999 );
	}

	/**
	 * The role prefix is generated by appsbd
	 *
	 * @param array $roles Its the role list.
	 *
	 * @return array
	 */
	public function role_prefix( $roles ) {

		if ( ! function_exists( 'get_current_screen' ) && file_exists( ABSPATH . '/wp-admin/includes/screen.php' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}
		$current_screen = '';
		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();
		}
		if ( 'user-new' === $current_screen->id || 'user-edit' === $current_screen->id ) {
			self::reset_roles();
			foreach ( $roles as $role_slug => &$role ) {
				if ( str_starts_with( $role_slug, 'vtpos-' ) ) {
					$role['name'] = $role['name'] . ' (Vitepos)';
				}
			}
		}

		return $roles;
	}

	/**
	 * The get action prefix is generated by appsbd
	 *
	 * @return string
	 */
	public function get_action_prefix() {
		return 'apbd-vite-pos';
	}

	/**
	 * The option form is generated by appsbd
	 */
	public function option_form() {
		$this->set_title( 'User Role List' );
		$this->display();
	}


	/**
	 * The on init is generated by appsbd
	 */
	public function on_init() {
		parent::on_init();
		add_filter( 'apbd-vtpos/acl-resource', array( $this, 'default_resources' ) );
		add_filter(
			'user_has_cap',
			function ( $all_caps, $caps, $args, $user ) {
				$all_caps = Mapbd_Pos_Role::set_capabilities_by_role( $all_caps, $user );

				return $all_caps;
			},
			10,
			4
		);
		$this->add_ajax_action( 'add-role', array( $this, 'add' ) );
		$this->add_ajax_action( 'add-wordpress-roles', array( $this, 'add_wordpress_roles' ) );
		$this->add_ajax_action( 'reset-role', array( $this, 'reset_role' ) );
		$this->add_ajax_action( 'copy-role', array( $this, 'copy_role' ) );
		$this->add_ajax_action( 'edit-role', array( $this, 'edit' ) );
		$this->add_ajax_action( 'role-details', array( $this, 'item_details' ) );
		$this->add_ajax_action( 'delete-role', array( $this, 'delete_item' ) );
		$this->add_ajax_action( 'access-data', array( $this, 'access_data' ) );
		$this->add_ajax_action( 'status-change', array( $this, 'status_change' ) );
		$this->add_ajax_action( 'acl-toggle', array( $this, 'acl_toggle' ) );
		$this->add_ajax_action( 'import-wp-role', array( $this, 'import_wp_roles' ) );
	}

	/**
	 * The on active is generated by appsbd
	 */
	public function on_active() {
		parent::on_active();
		Mapbd_pos_role::create_db_table();
		Mapbd_pos_role_access::create_db_table();
		Mapbd_pos_role::set_default_role();
	}

	/**
	 * The get menu title is generated by appsbd
	 *
	 * @return mixed Its string.
	 */
	public function get_menu_title() {
		return $this->__( 'User Role' );
	}

	/**
	 * The get menu sub title is generated by appsbd
	 *
	 * @return mixed Its string.
	 */
	public function get_menu_sub_title() {
		return $this->__( 'View all Role List' );
	}

	/**
	 * The get menu icon is generated by appsbd
	 *
	 * @return string Its string.
	 */
	public function get_menu_icon() {
		return 'fa fa-users';
	}

	/**
	 * The NewRoleAdded is generated by appsbd
	 *
	 * @param any $role Its string.
	 */
	public function new_role_added( $role ) {
		if ( $role instanceof Mapbd_pos_role ) {
			$existing_roles = wp_roles()->get_names();
			if ( 'Y' == $role->is_editable && ! isset( $existing_roles[ $role->slug ] ) ) {
				add_role(
					$role->slug,
					$role->name,
					array(
						'read'    => true,
						'level_0' => true,
					)
				);
			}
		}
	}

	/**
	 * The reset roles is generated by appsbd
	 */
	public static function reset_roles() {
		$existing_roles = wp_roles()->get_names();
		$roles          = Mapbd_Pos_Role::fetch_all();
		foreach ( $roles as $role ) {
			if ( 'Y' == $role->is_editable && ! isset( $existing_roles[ $role->slug ] ) ) {
				add_role(
					$role->slug,
					$role->name,
					array(
						'read'    => true,
						'level_0' => true,
					)
				);
			}
		}
	}

	/**
	 * The is exists role is generated by appsbd
	 *
	 * @param mixed $role_slug Its the role slug.
	 *
	 * @return bool
	 */
	public static function is_exists_role( $role_slug ) {
		$existing_roles = wp_roles()->get_names();
		if ( ! isset( $existing_roles[ $role_slug ] ) ) {
			self::reset_roles();
			$existing_roles = wp_roles()->get_names();
		}
		if ( isset( $existing_roles[ $role_slug ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * The RemovedRole is generated by appsbd
	 *
	 * @param any $role Its string.
	 */
	public function removed_role( $role ) {
		if ( $role instanceof Mapbd_pos_role ) {
			$existing_roles = wp_roles()->get_names();
			if ( substr( $role->slug, 0, 6 ) == 'vtpos-' ) {
				if ( 'Y' == $role->is_editable && isset( $existing_roles[ $role->slug ] ) ) {
					remove_role( $role->slug );
				}
			}
		}
	}

	/**
	 * The UpdatedRole is generated by appsbd
	 *
	 * @param any $role_id Its string.
	 */
	public function updated_role( $role_id ) {
		$non_changeable_roles = array( 'administrator', 'subscribe' );
		$role                 = Mapbd_pos_role::find_by( 'id', $role_id );
		if ( ! empty( $role ) ) {
			if ( in_array( $role->slug, $non_changeable_roles ) ) {
				return;
			}
			$existing_roles = wp_roles()->get_names();
			if ( 'Y' == $role->is_editable ) {

				if ( isset( $existing_roles[ $role->slug ] ) ) {
					remove_role( $role->slug );
				}
				if ( 'A' == $role->status ) {
					add_role(
						$role->slug,
						$role->name,
						array(
							'read'    => true,
							'level_0' => true,
						)
					);
				}
			}
		}
	}

	/**
	 * The default resources is generated by appsbd
	 *
	 * @param any $resources Its string.
	 *
	 * @return mixed Its string.
	 */
	public function default_resources( $resources ) {

		$resources[] = ACL_Resource::get_resource( 'pos-menu', 'POS Menu', '01. POS', '' );

		$resources[] = ACL_Resource::get_resource( 'order-list', 'Order List', '02. Order', '' );
		$resources[] = ACL_Resource::get_resource( 'order-hold', 'Order Hold List', '02. Order', '' );
		$resources[] = ACL_Resource::get_resource( 'order-offline', 'Order offline', '02. Order', '' );
		$resources[] = ACL_Resource::get_resource( 'order-details', 'Order details', '02. Order', '' );
		$resources[] = ACL_Resource::get_resource(
			'payment-note',
			'Payment note',
			'02. Order',
			'This roles user can see payment notes on order details'
		);
		$resources[] = ACL_Resource::get_resource(
			'can-see-any-outlet-orders',
			'Can View Any Outlet Orders',
			'02. Order',
			'This role user can view any outlets order details'
		);

		$resources[] = ACL_Resource::get_resource( 'customer-menu', 'Customer Menu', '03. Customer', '' );
		$resources[] = ACL_Resource::get_resource( 'customer-add', 'Customer Add', '03. Customer', '' );
		$resources[] = ACL_Resource::get_resource( 'customer-edit', 'Customer Edit', '03. Customer', '' );
		$resources[] = ACL_Resource::get_resource( 'customer-delete', 'Customer Delete', '03. Customer', '' );

		$resources[] = ACL_Resource::get_resource( 'product-menu', 'Product Menu', '04. Product', '' );
		$resources[] = ACL_Resource::get_resource( 'product-add', 'Product Add', '04. Product', '' );
		$resources[] = ACL_Resource::get_resource( 'product-edit', 'Product Edit', '04. Product', '' );
		$resources[] = ACL_Resource::get_resource( 'product-delete', 'Product Delete', '04. Product', '' );
		$resources[] = ACL_Resource::get_resource( 'make-favorite', 'Make Favourite', '04. Product', '' );

		$resources[] = ACL_Resource::get_resource( 'stock-menu', 'Stock Menu', '05. Stock', '' );
		$resources[] = ACL_Resource::get_resource( 'stock-add', 'Stock Add', '05. Stock', '' );

		$resources[] = ACL_Resource::get_resource( 'purchase-menu', 'Purchase Menu', '06. Purchase', '' );
		$resources[] = ACL_Resource::get_resource( 'purchase-details', 'Purchase Details', '06. Purchase', '' );
		$resources[] = ACL_Resource::get_resource(
			'can-see-any-outlet-purchases',
			'Can View Any Outlet Purchases',
			'06. Purchase',
			'This role user can view any outlets purchase details'
		);
		$resources[] = ACL_Resource::get_resource(
			'updated-price-list',
			'Price Update List',
			'06. Purchase',
			'This role user can see the products list which need to update the prices'
		);

		$resources[] = ACL_Resource::get_resource( 'vendor-menu', 'Vendor Menu', '07. Vendor', '' );
		$resources[] = ACL_Resource::get_resource( 'vendor-add', 'Vendor Add', '07. Vendor', '' );
		$resources[] = ACL_Resource::get_resource( 'vendor-edit', 'Vendor Edit', '07. Vendor', '' );
		$resources[] = ACL_Resource::get_resource( 'vendor-delete', 'Vendor delete', '07. Vendor', '' );

		$resources[] = ACL_Resource::get_resource( 'user-menu', 'User Menu', '08. User', '' );
		$resources[] = ACL_Resource::get_resource( 'user-add', 'User Add', '08. User', '' );
		$resources[] = ACL_Resource::get_resource( 'user-edit', 'User Edit', '08. User', '' );
		$resources[] = ACL_Resource::get_resource( 'user-delete', 'User delete', '08. User', '' );
		$resources[] = ACL_Resource::get_resource(
			'any-outlet-user-create',
			'Can Create Any Outlet User',
			'08. User',
			'The role user can create any outlets user if get this access'
		);
		$resources[] = ACL_Resource::get_resource(
			'change-any-user-pass',
			'Can Change Any User Password',
			'08. User',
			'The role user can change any users password'
		);

		$resources[] = ACL_Resource::get_resource( 'barcode-menu', 'Barcode Menu', '09. Barcode', '' );

		$resources[] = ACL_Resource::get_resource(
			'drawer-log',
			'Cash Drawer Log',
			'10. Drawer Log',
			'This roles user can see cash drawer log'
		);
		$resources[] = ACL_Resource::get_resource(
			'any-drawer-log',
			'Any Outlets Cash Drawer Log',
			'10. Drawer Log',
			'This roles user can see cash drawer log'
		);
		$resources[] = ACL_Resource::get_resource(
			'close-drawers',
			'Close any drawer',
			'10. Drawer Log',
			'This roles user can close any opened drawer'
		);

		$resources[] = ACL_Resource::get_resource(
			'addon-menu',
			'Addon Menu',
			'11. Addon Panel',
			'This roles user can see addon panel'
		);
		$resources[] = ACL_Resource::get_resource(
			'addon-add',
			'Addon Add',
			'11. Addon Panel',
			'This roles user can add addon'
		);
		$resources[] = ACL_Resource::get_resource( 'addon-edit', 'Addon Edit', '11. Addon Panel', '' );
		$resources[] = ACL_Resource::get_resource( 'addon-delete', 'Addon Delete', '11. Addon Panel', '' );

		$resources[] = ACL_Resource::get_resource(
			'cashier-menu',
			'Cashier Menu',
			'12. Cashier Panel',
			'This roles user can see restaurant cashier panel'
		);
		$resources[] = ACL_Resource::get_resource(
			'cancel-order',
			'Cancel Order',
			'12. Cashier Panel',
			'This roles user can cancel order'
		);
		$resources[] = ACL_Resource::get_resource(
			'cancel-order-request',
			'Order Cancel Request',
			'12. Cashier Panel',
			'This roles user can request for cancel the order if it is in preparing status'
		);
		if ( POS_Settings::get_pos_mode() == 'P' ) {
			$resources[] = ACL_Resource::get_resource(
				'cashier-to-kitchen',
				'Cashier Order To Kitchen',
				'12. Cashier Panel',
				'This roles user can make order and sent to kitchen'
			);
		}

		$resources[] = ACL_Resource::get_resource(
			'table-menu',
			'Table Menu',
			'13. Table Panel',
			'This roles user can see restaurant kitchen panel'
		);
		$resources[] = ACL_Resource::get_resource(
			'table-add',
			'Table Add',
			'13. Table Panel',
			'This roles user can see restaurant kitchen panel'
		);
		$resources[] = ACL_Resource::get_resource(
			'table-edit',
			'Table Edit',
			'13. Table Panel',
			'This roles user can see restaurant kitchen panel'
		);
		$resources[] = ACL_Resource::get_resource(
			'table-delete',
			'Table Delete',
			'13. Table Panel',
			'This roles user can see restaurant kitchen panel'
		);

		$resources[] = ACL_Resource::get_resource( 'report-menu', 'Report Panel', '16. Report Panel', '' );

		return $resources;
	}

	/**
	 * The is editable is generated by appsbd
	 *
	 * @param mixed $id Its id of role.
	 * @param null  $response Its response.
	 */
	public function is_role_editable( $id, &$response = null ) {
		if ( empty( $response ) ) {
			$response = new Ajax_Confirm_Response();
		}
		$role = Mapbd_Pos_Role::find_by( 'id', $id );
		if ( ! empty( $role->slug ) ) {
			$non_changeable_roles = array( 'administrator', 'subscribe' );
			if ( in_array( $role->slug, $non_changeable_roles ) || 'Y' != $role->is_editable ) {
				$this->add_error( 'Non editable role' );
				$response->display_with_response( false );
			}
		} else {
			$this->add_error( 'No role data found' );
			$response->display_with_response( false );
		}
	}

	/**
	 * The add is generated by appsbd
	 */
	public function add() {

		$response = new Ajax_Response();
		if ( APPSBD_IS_POST_BACK ) {
			$n_object = new Mapbd_pos_role();
			AppInput::set_bool_value( 'status', 'A', 'I' );
			$n_object->is_editable( 'Y' );
			$n_object->status( 'A' );
			if ( $n_object->set_from_post_data( true ) ) {
				if ( $n_object->save() ) {
					$this->add_info( 'Successfully added' );
					$response->display_with_response( true );
				}
			}
		}
		$response->display_with_response( false );
	}

	/**
	 * The add wordpress roles is generated by appsbd
	 */
	public function add_wordpress_roles() {
		$response = new Ajax_Response();
		$wp_roles = new \WP_Roles();
		$is_ok    = true;
		if ( APPSBD_IS_POST_BACK ) {
			$slugs = AppInput::post_value( 'roles' );
			if ( empty( $slugs ) ) {
				$this->add_error( 'No roles to add' );
				$response->display_with_response( false );
			}
			$error   = 0;
			$success = 0;
			foreach ( $slugs as $slug ) {
				$skips = array( 'contributor', 'subscriber', 'customer' );
				if ( isset( $wp_roles->roles[ $slug ] ) && ! in_array( $slug, $skips ) ) {
					$role_name = $wp_roles->roles[ $slug ]['name'];
					$n_object  = new Mapbd_pos_role();
					if ( ! $n_object::add_role_if_not_exists( $slug, $role_name, true, true ) ) {
						$error++;
					} else {
						$success++;
					}
				}
			}
			if ( $error > 0 ) {
				$this->add_error( '%s role(s) failed to add', $error );
			}
			if ( $success > 0 ) {
				$this->add_info( '%s role(s) added', $success );
			}
			$response->display_with_response( true );
		}
		$response->display_with_response( false );
	}

	/**
	 * The import wp role is generated by appsbd
	 */
	public function import_wp_roles() {
		$response  = new Ajax_Response();
		$obj       = new \WP_Roles();
		$roles     = $obj->roles;
		$res_roles = array();
		if ( ! empty( $roles ) ) {
			$exs   = Mapbd_Pos_Role::fetch_all_key_value( 'slug', 'name' );
			$skips = array( 'contributor', 'subscriber', 'customer' );
			foreach ( $roles as $slug => $role ) {
				if ( isset( $exs[ $slug ] ) || in_array( $slug, $skips ) ) {
					continue;
				}

				$it          = new \stdClass();
				$it->slug    = $slug;
				$it->name    = $role['name'];
				$res_roles[] = $it;
			}
		}
		$response->display_with_response( true, $res_roles );
	}

	/**
	 * The add is generated by appsbd
	 */
	public function reset_role() {
		$response      = new Ajax_Response();
		$role_to_reset = trim( AppInput::post_value( 'selected_role', '' ) );
		if ( ! empty( $role_to_reset ) ) {
			if ( 'administrator' == strtolower( $role_to_reset ) ) {
				$this->add_error( 'Administrator can not be reset' );
				$response->set_response( false );
			} elseif ( Mapbd_pos_role_access::delete_by_role_slug( $role_to_reset ) ) {
				$this->add_info( 'Reset has been successfully done' );
				$response->set_response( true );
			} else {
				$this->add_error( 'Failed to reset try again' );
				$response->set_response( false );
			}
		} else {
			$this->add_error( 'Failed to reset try again' );
			$response->set_response( false );
		}
		$response->display( true );
	}

	/**
	 * The add is generated by appsbd
	 */
	public function copy_role() {
		$response  = new Ajax_Response();
		$from_slug = AppInput::post_value( 'from' );
		$to_slug   = AppInput::post_value( 'to' );
		if ( ! empty( $from_slug ) && ! empty( $to_slug ) ) {
			if ( 'administrator' == strtolower( $from_slug ) ) {
				/**
				 * Its for check is there any change before process
				 *
				 * @since 1.0
				 */
				$resources = apply_filters( 'apbd-vtpos/acl-resource', array() );
				Mapbd_pos_role_access::delete_by_role_slug( $to_slug );
				foreach ( $resources as $resource ) {
					Mapbd_Pos_Role_Access::add_access_status( $to_slug, $resource->action_param );
				}
			} else {
				$access_list = Mapbd_Pos_Role_Access::find_all_by( 'role_slug', $from_slug );
				Mapbd_pos_role_access::delete_by_role_slug( $to_slug );
				foreach ( $access_list as $access ) {
					Mapbd_Pos_Role_Access::add_access_status( $to_slug, $access->resource_id );
				}
			}
			$this->add_info( 'Role copied successfully' );
			$response->set_response( true );
		} else {
			$this->add_error( 'Failed to reset try again' );
			$response->set_response( false );
		}
		$response->display();
	}

	/**
	 * The edit is generated by appsbd
	 *
	 * @param string $param_id Its string.
	 */
	public function edit( $param_id = '' ) {
		$response = new Ajax_Confirm_Response();
		$param_id = AppInput::post_value( 'id' );
		if ( empty( $param_id ) ) {
			$this->add_error( 'Invalid request' );
			$response->display_with_response( false );

			return;
		}
		$this->is_role_editable( $param_id, $response );
		if ( APPSBD_IS_POST_BACK ) {
			$uobject = new Mapbd_Pos_Role();
			AppInput::set_bool_value( 'status', 'A', 'I' );
			if ( $uobject->set_from_post_data( false ) ) {
				$propes = 'name,role_description,status';
				$uobject->unset_all_excepts( $propes );
				$uobject->set_where_update( 'id', $param_id );
				if ( $uobject->update() ) {
					$role = Mapbd_Pos_Role::find_by( 'id', $param_id );
					/**
					 * Its for role update
					 *
					 * @since 1.0
					 */
					do_action( 'apbd-vtpos/action/role-updated', $param_id, $role );
					$this->add_info( 'Successfully updated' );
					$response->display_with_response( true );

					return;
				}
			}
		}
		$this->add_error( 'Update failed' );
		$response->display_with_response( false );
	}


	/**
	 * The view warehouse details is generated by appsbd
	 */
	public function item_details() {
		$response = new Ajax_Confirm_Response();
		$item_id  = AppInput::post_value( 'id' );
		if ( empty( $item_id ) ) {
			$this->add_error( 'Invalid request' );
			$response->display_with_response( false );

			return;
		}
		$propes  = 'id,name,role_description,status';
		$details = new Mapbd_Pos_Role();
		$details->id( $item_id );
		$details->is_editable( 'Y' );
		if ( $details->select() ) {
			$response->display_with_response( true, $details->get_properties_api_response( $propes ) );
		} else {
			$this->add_error( 'No role found with this request param' );
			$response->display_with_response( false, null );
		}
	}

	/**
	 * The data is generated by appsbd
	 */
	public function data() {
		$main_response = new Ajax_Data_Response();
		$main_response->set_download_filename( 'apbd-vitepost-role-list' );
		$mainobj = new Mapbd_pos_role();
		$mainobj->set_search_by_param( $main_response->src_by, 'name,phone' );
		$mainobj->set_sort_by_param( $main_response->sort_by );

		$records = $mainobj->count_all(
			$main_response->src_item,
			$main_response->src_text,
			$main_response->multiparam,
			'after'
		);
		if ( $records > 0 ) {
			$main_response->set_grid_records( $records );
			$result = $mainobj->select_all_grid_data(
				'',
				'',
				'',
				$main_response->limit,
				$main_response->limit_start(),
				$main_response->src_item,
				$main_response->src_text,
				$main_response->multiparam,
				'after'
			);
			if ( $result ) {
				foreach ( $result as &$data ) {
					if ( 'Y' != $data->is_editable ) {
						$data->status   = '';
						$data->is_agent = '';
					}
					$data->max_discount = 100.00;
				}
			}
			$main_response->set_grid_data( $result );
		}
		$main_response->display_grid_response();
	}

	/**
	 * The access data is generated by appsbd
	 */
	public function access_data() {
		$main_response = new Ajax_Data_Response();
		$main_response->set_download_filename( 'apbd-wps-role-access-list' );
		$res                  = Mapbd_Pos_Role_Access::get_resource_list();
		$main_response->limit = count( $res );
		$main_response->set_grid_records( $main_response->limit );
		$roles       = Mapbd_Pos_Role::get_role_list();
		$access_list = Mapbd_Pos_Role_Access::get_access_list();
		if ( $res ) {
			$main_response->set_grid_records( count( $res ) );
			foreach ( $res as &$data ) {
				foreach ( $roles as $role ) {
					if ( 'administrator' == $role->slug ) {
						$data->{$role->slug} = 'Y';
					} elseif ( ! empty( $access_list[ $data->action_param ][ $role->slug ] ) && 'Y' == $access_list[ $data->action_param ][ $role->slug ] ) {
						$data->{$role->slug} = 'Y';
					} else {
						$data->{$role->slug} = 'N';
					}
				}
			}
		}
		$main_response->set_grid_data( $res );

		$main_response->display_grid_response();
	}


	/**
	 * The delete item is generated by appsbd
	 */
	public function delete_item() {
		$param         = AppInput::post_value( 'id' );
		$main_response = new Ajax_Confirm_Response();
		$this->is_role_editable( $param, $main_response );
		$move_to_role = AppInput::post_value( 'slug' );

		if ( empty( $param ) || empty( $move_to_role ) ) {
			$this->add_error( 'Invalid Request' );
			$main_response->display_with_response( false );

			return;
		}
		$role_obj = Mapbd_Pos_Role::find_by( 'slug', $move_to_role );
		if ( empty( $role_obj ) ) {
			$this->add_error( 'Move to role does not exists' );
			$main_response->display_with_response( false );

			return;
		}
		$mr = new Mapbd_Pos_Role();
		$mr->id( $param );
		if ( $mr->select() ) {
			$allusers = get_users( array( 'role__in' => array( $mr->slug ) ) );
			if ( count( $allusers ) > 0 ) {
				foreach ( $allusers as $user ) {
					if ( $user instanceof \WP_User ) {
						$user->set_role( $move_to_role );
					}
				}
			}
			if ( Mapbd_Pos_Role::delete_by_slug( $mr->slug ) ) {
				/**
				 * Its for role delete
				 *
				 * @since 1.0
				 */
				do_action( 'apbd-vtpos/action/role-deleted', $mr );
				$this->add_info( 'Successfully deleted' );
				$main_response->display_with_response( true );
			} else {
				$this->add_error( 'Delete failed try again' );
				$main_response->display_with_response( false );
			}
		} else {
			$this->add_error( 'Role does not exists' );
			$main_response->display_with_response( false );
		}
	}

	/**
	 * The status change is generated by appsbd
	 */
	public function status_change() {
		$param = AppInput::post_value( 'id' );
		if ( empty( $param ) ) {
			$this->DisplayWithResponse( false, $this->__( 'Invalid Request' ) );

			return;
		}
		$main_response = new Ajax_Confirm_Response();
		$this->is_role_editable( $param, $main_response );
		$mr = new Mapbd_pos_role();
		$mr->id( $param );
		if ( $mr->select( 'status' ) ) {
			$new_status = 'A' == $mr->status ? 'I' : 'A';
			$uo         = new Mapbd_pos_role();
			$uo->status( $new_status );
			$uo->set_where_update( 'id', $param );
			if ( $uo->Update() ) {
				/**
				 * Its for role update
				 *
				 * @since 1.0
				 */
				do_action( 'apbd-vtpos/action/role-updated', $param );
				$this->add_info( 'Successfully Updated' );
				$main_response->display_with_response( true, $new_status );
			} else {
				$this->add_info( 'Update failed try again' );
				$main_response->display_with_response( false, $mr->status );
			}
		}
	}

	/**
	 * The is agent status change is generated by appsbd
	 */
	public function is_agent_status_change() {
		$param = APBD_GetValue( 'id' );
		if ( empty( $param ) ) {
			$this->DisplayWithResponse( false, $this->__( 'Invalid Request' ) );

			return;
		}
		$main_response = new AppsbdAjaxConfirmResponse();
		$mr            = new Mapbd_pos_role();
		$status_change = $mr->GetPropertyOptionsTag( 'is_agent' );

		$mr->id( $param );
		if ( $mr->Select( 'is_agent' ) ) {
			$new_status = 'Y' == $mr->is_agent ? 'N' : 'Y';
			$uo         = new Mapbd_pos_role();
			$uo->is_agent( $new_status );
			$uo->SetWhereUpdate( 'id', $param );
			if ( $uo->Update() ) {
				$status_text = appsbd_get_text_by_key( $uo->is_agent, $status_change );
				APBD_AddLog( 'U', $uo->settedPropertyforLog(), 'l002', 'Wp_apbd_wps_role' );
				/**
				 * Its for role update
				 *
				 * @since 1.0
				 */
				do_action( 'apbd-vtpos/action/role-updated', $param );
				$main_response->DisplayWithResponse( true, $this->__( 'Successfully Updated' ), $status_text );
			} else {
				$main_response->DisplayWithResponse( false, $this->__( 'Update failed try again' ) );
			}
		}
	}

	/**
	 * The acl toggle is generated by appsbd
	 */
	public function acl_toggle() {
		$role_slug     = AppInput::post_value( 'role_slug' );
		$res_id        = AppInput::post_value( 'action_param' );
		$main_response = new Ajax_Confirm_Response();
		$acl           = Mapbd_pos_role_access::find_by( 'resource_id', $res_id, array( 'role_slug' => $role_slug ) );
		$is_updated    = false;
		$final_status  = '';
		if ( ! empty( $acl ) ) {

			$new_status = 'Y' == $acl->role_access ? 'N' : 'Y';
			if ( Mapbd_pos_role_access::update_status( $acl->id, $new_status ) ) {
				$is_updated   = true;
				$final_status = $new_status;
			} else {
				$final_status = $acl->role_access;
			}
		} else {

			$new_status = 'Y';
			if ( Mapbd_pos_role_access::add_access_status( $role_slug, $res_id ) ) {
				$is_updated   = true;
				$final_status = $new_status;
			} else {
				$final_status = 'N';
			}
		}
		if ( $is_updated ) {
			$this->add_info( 'Successfully Updated' );
		} else {
			$this->add_error( 'Failed to update' );
		}
		$main_response->display_with_response( $is_updated, $final_status );
	}
}

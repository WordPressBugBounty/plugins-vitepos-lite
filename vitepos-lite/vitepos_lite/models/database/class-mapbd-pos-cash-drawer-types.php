<?php
/**
 * Pos Warehouse Database Model
 *
 * @package VitePos_Lite\Models\Database
 */

namespace VitePos_Lite\Models\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use VitePos_Lite\Core\ViteposModelLite;
use VitePos_Lite\Modules\POS_Payment;

/**
 * Class Mapbd_pos_cash_drawer_log
 *
 * @package VitePos_Lite\Models\Database
 */
class Mapbd_Pos_Cash_Drawer_Types extends ViteposModelLite {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property cash_drawer_id
	 *
	 * @var int
	 */
	public $cash_drawer_id;
	/**
	 * Its property order id
	 *
	 * @var int
	 */
	public $order_id;
	/**
	 * Its property note
	 *
	 * @var String
	 */
	/**
	 * Its property payment type
	 *
	 * @var String
	 */
	public $payment_type;
	/**
	 * Its property amount
	 *
	 * @var float
	 */
	public $amount;
	/**
	 * Its property user_id
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * Its property entry_time
	 *
	 * @var String
	 */
	public $entry_time;


	/**
	 * Mapbd_pos_cash_drawer_log constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->SetValidation();
		$this->table_name     = 'apbd_pos_cash_drawer_types';
		$this->primary_key    = 'id';
		$this->unique_key     = array();
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-vite-pos';
	}


	/**
	 * The set validation is generated by appsbd
	 */
	public function set_validation() {
		$this->validations = array(
			'id'             => array(
				'Text' => 'Id',
				'Rule' => 'max_length[10]|integer',
			),
			'cash_drawer_id' => array(
				'Text' => 'Cash Drawer Id',
				'Rule' => 'required|max_length[11]|integer',
			),
			'order_id'       => array(
				'Text' => 'Ref Id',
				'Rule' => 'max_length[11]|integer',
			),

			'amount'         => array(
				'Text' => 'Amount',
				'Rule' => 'max_length[11]|numeric',
			),
			'user_id'        => array(
				'Text' => 'User Id',
				'Rule' => 'max_length[11]|integer',
			),
			'payment_type'   => array(
				'Text' => 'Payment Type',
				'Rule' => 'max_length[1]',
			),
			'entry_time'     => array(
				'Text' => 'Entry Time',
				'Rule' => 'max_length[20]',
			),

		);
	}

	/**
	 * The get property raw options is generated by appsbd
	 *
	 * @param mixed   $property Its the property.
	 * @param boolean $is_with_select False if with select.
	 *
	 * @return array|string[]
	 */
	public function get_property_raw_options( $property, $is_with_select = false ) {
		$return_obj = array();
		switch ( $property ) {
			case 'payment_type':
				$return_obj = array(
					'C' => 'Cash',
					'S' => 'Swipe',
					'O' => 'Other',
				);
				break;
			default:
		}
		if ( $is_with_select ) {
			return array_merge( array( '' => 'Select' ), $return_obj );
		}

		return $return_obj;
	}

	/**
	 * The AddLog is generated by appsbd
	 * The AddLog is generated by appsbd
	 *
	 * @param mixed  $cash_drawer_id Its cash_drawe_id param.
	 * @param int    $user_id Its user_id param.
	 * @param int    $order_id Its order_id param.
	 * @param String $payment_type Its payment_type param.
	 * @param mixed  $amount Its amount param.
	 *
	 * @return bool
	 */
	public static function AddLog( $cash_drawer_id, $user_id, $order_id, $payment_type, $amount ) {
		$newobj = new self();
		$newobj->cash_drawer_id( $cash_drawer_id );
		$newobj->order_id( $order_id );
		$newobj->user_id( $user_id );
		$newobj->amount( $amount );
		$newobj->payment_type( $payment_type );
		$newobj->entry_time( gmdate( 'Y-m-d' ) );

		return $newobj->save();
	}

	/**
	 * The get order list is generated by appsbd
	 *
	 * @param mixed $cash_drawer_id Its cash_drawer_id param.
	 *
	 * @return array
	 */
	public static function get_order_list( $cash_drawer_id ) {
		$db       = self::get_db_object();
		$prefix   = $db->prefix;
		$query    = "SELECT 
					order_id,
					sum(case when payment_type='C' then amount else 0 end) as cash,
					sum(case when payment_type='S' then amount else 0 end) as swipe,
					sum(case when payment_type='O' then amount else 0 end) as other,
					sum(case when payment_type='_' then amount else 0 end) as change_amount
				FROM {$prefix}apbd_pos_cash_drawer_types
				WHERE cash_drawer_id =$cash_drawer_id
				GROUP BY order_id
				";
		$this_obj = new self();
		return $this_obj->select_query( $query );
	}
	/**
	 * The get order summary is generated by appsbd
	 *
	 * @param mixed $cash_drawer_id Its cash_drawer_id param.
	 *
	 * @return array
	 */
	public static function get_order_summary( $cash_drawer_id ) {
		$db                      = self::get_db_object();
		$prefix                  = $db->prefix;
		$query                   = "SELECT 				
					sum(case when payment_type='C' then amount else 0 end) as cash,
					sum(case when payment_type='S' then amount else 0 end) as swipe,
					sum(case when payment_type='O' then amount else 0 end) as other,
					sum(case when payment_type='_' then amount else 0 end) as change_amount
				FROM {$prefix}apbd_pos_cash_drawer_types
				WHERE cash_drawer_id =$cash_drawer_id
				GROUP BY cash_drawer_id
				";
		$this_obj                = new self();
		$response                = new \stdClass();
		$response->cash          = 0;
		$response->swipe         = 0;
		$response->other         = 0;
		$response->change_amount = 0;
		$results                 = $this_obj->select_query( $query );
		if ( ! empty( $results[0] ) ) {
			$response->cash          = $results[0]->cash;
			$response->swipe         = $results[0]->swipe;
			$response->other         = $results[0]->other;
			$response->change_amount = $results[0]->change_amount;
		}
		return $response;
	}
	/**
	 * The get order summary is generated by appsbd
	 *
	 * @param mixed $cash_drawer_id Its cash_drawer_id param.
	 *
	 * @return array
	 */
	public static function get_order_summary_by_types( $cash_drawer_id ) {
		$db                      = self::get_db_object();
		$prefix                  = $db->prefix;
		$query                   = "SELECT 	payment_type,SUM(amount) as total
FROM {$prefix}apbd_pos_cash_drawer_types
WHERE cash_drawer_id =$cash_drawer_id
GROUP BY payment_type";
		$this_obj                = new self();
		$results                 = $this_obj->select_query( $query );
		if ( ! empty( $results[0] ) ) {
			foreach ( $results as &$item ) {
				/**
				 * Its for check is there any change before process
				 *
				 * @since 3.0
				 */
				$item->title = apply_filters( 'vitepos/filter/payment-name', $item->payment_type, $item->payment_type );
			}
			return $results;
		}
		return array();
	}
	/**
	 * The get order list is generated by appsbd
	 *
	 * @param mixed $cash_drawer_id Its cash_drawer_id param.
	 *
	 * @return array
	 */
	public static function get_order_list_payment_methods( $cash_drawer_id ) {
		$db       = self::get_db_object();
		$prefix   = $db->prefix;
		$payment_methods = POS_Payment::get_payment_methods();
		$colums = '';
		foreach ( $payment_methods as $payment_method ) {
			$colums .= "sum(case when payment_type='{$payment_method->id}' then amount else 0 end) as {$payment_method->id},";
		}
		$query    = "SELECT 
					order_id,
					$colums
					sum(case when payment_type='_' then amount else 0 end) as change_amount
				FROM {$prefix}apbd_pos_cash_drawer_types
				WHERE cash_drawer_id =$cash_drawer_id
				GROUP BY order_id
				";
		$this_obj = new self();
		return $this_obj->select_query( $query );
	}
	/**
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {
		$this_obj = new static();
		$table    = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `cash_drawer_id` int(11) NOT NULL,
					  `order_id` int(50) NOT NULL,
					  `payment_type` char(1) NOT NULL DEFAULT '',
					  `amount` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `user_id` int(11) NOT NULL,
					  `entry_time` timestamp NULL DEFAULT current_timestamp(),
					  PRIMARY KEY (`id`) USING BTREE,
					  KEY `order_id` (`order_id`)
					)";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}
}

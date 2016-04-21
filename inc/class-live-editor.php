<?php

/** Plugin admin class */
require 'live-editor/inc/class-admin.php';
/** Plugin public class */
require 'live-editor/inc/class-public.php';

/**
 * Pootle Page Builder Live Editor main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Pootle_Page_Builder_Live_Editor {

	/**
	 * @var 	Pootle_Page_Builder_Live_Editor Instance
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var     string Token
	 * @access  public
	 * @since   1.0.0
	 */
	public static $token;

	/**
	 * @var     string Version
	 * @access  public
	 * @since   1.0.0
	 */
	public static $version;

	/**
	 * @var 	string Plugin main __FILE__
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $file;

	/**
	 * @var 	string Plugin directory url
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $url;

	/**
	 * @var 	string Plugin directory path
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $path;

	/**
	 * @var 	Pootle_Page_Builder_Live_Editor_Admin Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $admin;

	/**
	 * @var 	Pootle_Page_Builder_Live_Editor_Public Instance
	 * @access  public
	 * @since 	1.0.0
	 */
	public $public;

	/**
	 * Main Pootle Page Builder Live Editor Instance
	 *
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Pootle_Page_Builder_Live_Editor instance
	 */
	public static function instance( $file ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   =   'pootle-live-editor';
		self::$file    =   $file;
		self::$url     =   plugin_dir_url( $file ) . 'inc/live-editor/';
		self::$path    =   plugin_dir_path( $file ) . 'live-editor/';
		self::$version =   '1.0.0';

		add_action( 'init', array( $this, 'init' ) );
	} // End __construct()

	/**
	 * Initiates the plugin
	 * @action init
	 * @since 1.0.0
	 */
	public function init() {
		if ( class_exists( 'Pootle_Page_Builder' ) ) {

			//Initiate admin
			$this->_admin();

			//Initiate public
			$this->_public();

			//Mark this add on as active
			add_filter( 'pootlepb_installed_add_ons', array( $this, 'add_on_active' ) );

		}
	} // End init()

	/**
	 * Initiates admin class and adds admin hooks
	 * @since 1.0.0
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Pootle_Page_Builder_Live_Editor_Admin::instance();

		// Enqueues the admin scripts
		add_action( 'pootlepb_enqueue_admin_scripts', array( $this->admin, 'enqueue' ) );
		//Adds Live editor link
		add_action( 'admin_bar_menu', array( $this->admin, 'add_item' ), 999 );
	}

	/**
	 * Initiates public class and adds public hooks
	 * @since 1.0.0
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Pootle_Page_Builder_Live_Editor_Public::instance();

		//Adds frontend actions
		add_action( 'get_header', array( $this->public, 'actions' ) );
		//Ajax action to save live editor data and render new grid
		add_action( 'wp_ajax_pootlepb_live_editor', array( $this->public, 'sync' ) );
		//Ajax action to save live editor data and render new grid
		add_action( 'wp_ajax_pootlepb_live_page', array( $this->admin, 'new_live_page' ) );

	} // End enqueue()

	/**
	 * Marks this add on as active on
	 * @param array $active Active add ons
	 * @return array Active add ons
	 * @since 1.0.0
	 */
	public function add_on_active( $active ) {
		// To allows ppb add ons page to fetch name, description etc.
		$active[ self::$token ] = self::$file;
		return $active;
	}
}
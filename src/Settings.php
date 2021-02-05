<?php


namespace TechSpokes\LoginRedirects;


use TechSpokes\LoginRedirects\Tools\GetWPRoles;
use TechSpokes\LoginRedirects\Tools\IsEmptyStatic;

/**
 * Class Settings
 *
 * Handles plugin options.
 *
 * @package TechSpokes\LoginRedirects
 */
class Settings {

	use GetWPRoles, IsEmptyStatic;

	/**
	 * @const string OPTION_PREFIX Contains prefix for unique option names.
	 */
	public const OPTION_PREFIX = 'tslr_';

	public const OPTION_REDIRECT_URL       = 'redirect_url';
	public const OPTION_FIRST_REDIRECT_URL = 'first_redirect_url';

	public const UI = 'login-redirects';

	/**
	 * @var \TechSpokes\LoginRedirects\Settings $instance
	 */
	protected static $instance;

	/**
	 * @return \TechSpokes\LoginRedirects\Settings
	 */
	public static function getInstance(): Settings {
		if ( !self::$instance instanceof Settings ) {
			self::setInstance( new self() );
		}

		return self::$instance;
	}

	/**
	 * @param \TechSpokes\LoginRedirects\Settings $instance
	 */
	public static function setInstance( Settings $instance ) {
		self::$instance = $instance;
	}

	/**
	 * @param string      $option
	 * @param string|null $default
	 * @param bool        $not_empty
	 *
	 * @return false|mixed
	 */
	public static function get_option( string $option, ?string $default = null, bool $not_empty = false ) {
		if ( true === $not_empty ) {
			$value = get_option( self::get_option_name( $option ), $default );

			return empty( $value ) ? $default : $value;
		}

		return get_option( self::get_option_name( $option ), $default );
	}

	/**
	 * Settings constructor.
	 */
	protected function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ), 10, 0 );
		add_action( 'admin_menu', array( $this, 'add_ui' ), 10, 0 );
	}

	/**
	 * @param string $option
	 *
	 * @return string
	 */
	protected static function get_option_name( string $option ): string {
		return self::OPTION_PREFIX . $option;
	}

	/**
	 * @param string ...$keys
	 *
	 * @return string
	 */
	protected static function uJoin( string ...$keys ): string {
		return join( '_', func_get_args() );
	}

	/**
	 * Registers settings in UI.
	 */
	public function register_settings() {
		foreach ( array_keys( self::get_roles() ) as $role ) {
			register_setting(
				self::UI,
				self::get_option_name( self::uJoin( $role, self::OPTION_REDIRECT_URL ) ),
				array(
					'sanitize_callback' => 'esc_url_raw',
				)
			);
			register_setting(
				self::UI,
				self::get_option_name( self::uJoin( $role, self::OPTION_FIRST_REDIRECT_URL ) ),
				array(
					'sanitize_callback' => 'esc_url_raw',
				)
			);
		}
	}

	/**
	 * Registers UI in WP.
	 */
	public function add_ui() {
		add_options_page(
			__( 'Login Redirects', 'login-redirects' ),
			__( 'Login Redirects', 'login-redirects' ),
			'manage_options',
			self::UI,
			array( $this, 'ui_page' )
		);
		foreach ( self::get_roles() as $role => $name ) {
			add_settings_section(
				$role,
				$name,
				'__return_empty_string',
				self::UI
			);
			add_settings_field(
				$redirect_option = self::uJoin( $role, self::OPTION_REDIRECT_URL ),
				__( 'Login redirect URL', 'login-redirects' ),
				array( $this, 'text_field' ),
				self::UI,
				$role,
				array(
					'label_for'   => $redirect_option,
					'input_attrs' => array(
						'type'  => 'text',
						'id'    => $redirect_option,
						'name'  => self::get_option_name( $redirect_option ),
						'class' => 'large-text code',
						'value' => self::get_option( $redirect_option, '', true ),
					),
					'description' => sprintf(
						__( 'Please enter the URL to redirect %s to when they login. Leave empty to keep default redirect.', 'login-redirects' ),
						strtolower( $name )
					),
				)
			);
			add_settings_field(
				$first_redirect_option = self::uJoin( $role, self::OPTION_FIRST_REDIRECT_URL ),
				__( 'First login redirect URL', 'login-redirects' ),
				array( $this, 'text_field' ),
				self::UI,
				$role,
				array(
					'label_for'   => $first_redirect_option,
					'input_attrs' => array(
						'type'  => 'text',
						'id'    => $first_redirect_option,
						'name'  => self::get_option_name( $first_redirect_option ),
						'class' => 'large-text code',
						'value' => self::get_option( $first_redirect_option, '', true ),
					),
					'description' => sprintf(
						__( 'Please enter the URL to redirect %s to when they login for the first time. Leave empty to keep default redirect.', 'login-redirects' ),
						strtolower( $name )
					),
				)
			);
		}
	}

	/**
	 * @param array $args
	 */
	public function text_field( array $args ) {
		/**
		 * @var array  $input_attrs
		 * @var string $description
		 */
		extract( $args, EXTR_OVERWRITE );
		array_walk( $input_attrs, function ( string &$value, string $key ) {
			$value = sprintf(
				'%1$s="%2$s"',
				$key,
				esc_attr( $value )
			);
		} );
		/** @noinspection HtmlUnknownAttribute */
		printf(
			'<p><input %1$s></p><p class="description">%2$s</p>',
			join( ' ', $input_attrs ),
			$description
		);
	}

	/**
	 * Displays UI page.
	 */
	public function ui_page() {
		printf(
			'<div class="wrap %1$s-settings-page">%2$s<form action="%3$s" method="post">',
			self::UI,
			sprintf( '<h2>%s</h2>', esc_html( get_admin_page_title() ) ),
			esc_url( admin_url( 'options.php' ) )
		);
		settings_fields( self::UI );
		do_settings_sections( self::UI );
		submit_button();
		echo '</form></div>';
	}

}

<?php


namespace TechSpokes\LoginRedirects;


use TechSpokes\LoginRedirects\Tools\GetWPRoles;
use TechSpokes\LoginRedirects\Tools\IsEmptyStatic;

/**
 * Class Plugin
 *
 * @package TechSpokes\LoginRedirects
 */
class Plugin {

	use GetWPRoles, IsEmptyStatic;

	public const NEW_USER_MARKER = 'has_never_logged_in';

	/**
	 * @var \TechSpokes\LoginRedirects\Plugin $instance
	 */
	protected static $instance;

	/**
	 * @var \TechSpokes\LoginRedirects\Settings $settings
	 */
	protected $settings;

	/**
	 * @var string[]|array
	 */
	protected $redirects;

	/**
	 * @var string[]|array
	 */
	protected $first_login_redirects;

	/**
	 * @param int $user_id
	 *
	 * @noinspection PhpUnused
	 */
	public static function mark_new_user( int $user_id ) {
		update_user_meta( $user_id, self::NEW_USER_MARKER, 1 );
	}

	/**
	 * @return \TechSpokes\LoginRedirects\Plugin
	 */
	public static function getInstance(): Plugin {

		if ( !( self::$instance instanceof Plugin ) ) {
			self::setInstance( new self() );
		}

		return self::$instance;
	}

	/**
	 * @param \TechSpokes\LoginRedirects\Plugin $instance
	 */
	protected static function setInstance( Plugin $instance ) {

		self::$instance = $instance;
	}

	/**
	 * Plugin constructor.
	 */
	protected function __construct() {
		$this->setSettings( Settings::getInstance() );
		add_action( 'user_register', array( __CLASS__, 'mark_new_user' ), 15, 1 );
		add_filter( 'login_redirect', array( $this, 'maybe_modify_redirect' ), 15, 3 );
	}

	/**
	 * @param int $user_id
	 */
	protected static function unmark_user( int $user_id ) {
		delete_user_meta( $user_id, self::NEW_USER_MARKER );
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	protected static function has_never_logged_in( int $user_id ): bool {
		return !self::isEmpty( get_user_meta( $user_id, self::NEW_USER_MARKER, true ) );
	}

	/**
	 * @param string             $target
	 * @param string             $request
	 * @param \WP_User|\WP_Error $user
	 *
	 * @return string
	 */
	public function maybe_modify_redirect( string $target, string $request, $user ): string {
		if (
			//do not interact if the request is not empty and it is not default admin url
			( !self::isEmpty( $request ) && ( admin_url() !== $request ) )
			//neither do anything if user is an error
			|| is_wp_error( $user )
		) {
			return $target;
		}
		//initial request URL is empty and user is a user
		//return target if the user has no roles
		if ( self::isEmpty( $user_roles = self::get_user_roles( $user->ID ) ) ) {
			return $target;
		}
		//check if the user has never logged in before
		if ( self::has_never_logged_in( $user->ID ) ) {
			//unmark the user as they have just logged in for the first time
			self::unmark_user( $user->ID );
			//grab first time redirects
			if ( !self::isEmpty( $first_time_redirects = self::getFirstLoginRedirects() ) ) {
				//check if the user has the role that has redirect specified
				foreach ( $first_time_redirects as $role => $first_time_redirect ) {
					if ( in_array( $role, $user_roles ) ) {
						//do the redirect if user has role that redirects
						return $first_time_redirect;
					}
				}
			}
		}
		//see if regular redirect applies to this user
		if ( !self::isEmpty( $redirects = self::getRedirects() ) ) {
			//check across the roles with redirects if the user has the role that redirects
			foreach ( $redirects as $role => $redirect ) {
				if ( in_array( $role, $user_roles ) ) {
					//do the redirect if user has role that redirects
					return $redirect;
				}
			}
		}

		//no suitable redirect found - return target
		return $target;
	}

	/**
	 * @return \TechSpokes\LoginRedirects\Settings
	 */
	public function getSettings(): Settings {
		return $this->settings;
	}

	/**
	 * @param \TechSpokes\LoginRedirects\Settings $settings
	 *
	 * @return Plugin
	 */
	public function setSettings( Settings $settings ): Plugin {
		$this->settings = $settings;

		return $this;
	}

	/**
	 * @return array|string[]
	 */
	public function getRedirects(): array {
		if ( !is_array( $this->redirects ) ) {
			$redirects = array();
			foreach ( self::get_roles() as $role ) {
				if ( !self::isEmpty( $redirect = $this->getSettings()::get_redirect_url( $role ) ) ) {
					$redirects[ $role ] = $redirect;
				}
			}
			$this->setRedirects( $redirects );
		}

		return $this->redirects;
	}

	/**
	 * @param array|string[] $redirects
	 *
	 * @return Plugin
	 */
	public function setRedirects( array $redirects ): Plugin {
		$this->redirects = $redirects;

		return $this;
	}

	/**
	 * @return array|string[]
	 */
	public function getFirstLoginRedirects(): array {
		if ( !is_array( $this->first_login_redirects ) ) {
			$redirects = array();
			foreach ( self::get_roles() as $role ) {
				if ( !self::isEmpty( $redirect = $this->getSettings()::get_first_redirect_url( $role ) ) ) {
					$redirects[ $role ] = $redirect;
				}
			}
			$this->setFirstLoginRedirects( $redirects );
		}

		return $this->first_login_redirects;
	}

	/**
	 * @param array|string[] $first_login_redirects
	 *
	 * @return Plugin
	 */
	public function setFirstLoginRedirects( array $first_login_redirects ): Plugin {
		$this->first_login_redirects = $first_login_redirects;

		return $this;
	}

}


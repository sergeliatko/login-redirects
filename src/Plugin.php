<?php


namespace TechSpokes\LoginRedirects;


use TechSpokes\LoginRedirects\Tools\IsEmptyStatic;

/**
 * Class Plugin
 *
 * @package TechSpokes\LoginRedirects
 */
class Plugin {

	use IsEmptyStatic;

	/**
	 * @var \TechSpokes\LoginRedirects\Plugin $instance
	 */
	protected static $instance;

	/**
	 * @var \TechSpokes\LoginRedirects\Settings $settings
	 */
	protected $settings;

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

}


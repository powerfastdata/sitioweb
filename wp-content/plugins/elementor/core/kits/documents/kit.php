<?php
namespace Elementor\Core\Kits\Documents;

use Elementor\Core\DocumentTypes\PageBase;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Kits\Documents\Tabs;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Manager as PageManager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Kit extends PageBase {
	/**
	 * @var Tabs\Tab_Base[]
	 */
	private $tabs;

	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		$this->tabs = [
			'global-colors' => new Tabs\Global_Colors( $this ),
			'global-typography' => new Tabs\Global_Typography( $this ),
			'theme-style-typography' => new Tabs\Theme_Style_Typography( $this ),
			'theme-style-buttons' => new Tabs\Theme_Style_Buttons( $this ),
			'theme-style-images' => new Tabs\Theme_Style_Images( $this ),
			'theme-style-form-fields' => new Tabs\Theme_Style_Form_Fields( $this ),
			'settings-site-identity' => new Tabs\Settings_Site_Identity( $this ),
			'settings-background' => new Tabs\Settings_Background( $this ),
			'settings-layout' => new Tabs\Settings_Layout( $this ),
			'settings-lightbox' => new Tabs\Settings_Lightbox( $this ),
			'settings-custom-css' => new Tabs\Settings_Custom_CSS( $this ),
		];
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['has_elements'] = false;
		$properties['show_in_finder'] = false;
		$properties['show_on_admin_bar'] = false;
		$properties['edit_capability'] = 'edit_theme_options';
		$properties['support_kit'] = true;

		return $properties;
	}

	public function get_name() {
		return 'kit';
	}

	public static function get_title() {
		return __( 'Kit', 'elementor' );
	}

	protected function get_have_a_look_url() {
		return '';
	}

	public static function get_editor_panel_config() {
		$config = parent::get_editor_panel_config();
		$config['default_route'] = 'panel/global/menu';

		$config['needHelpUrl'] = 'https://go.elementor.com/global-settings';

		return $config;
	}

	public function get_css_wrapper_selector() {
		return '.elementor-kit-' . $this->get_main_id();
	}

	public function save( $data ) {
		$saved = parent::save( $data );

		if ( ! $saved ) {
			return false;
		}

		// Should set is_saving to true, to avoid infinite loop when updating
		// settings like: 'site_name" or "site_description".
		$this->set_is_saving( true );

		foreach ( $this->tabs as $tab ) {
			$tab->on_save( $data );
		}

		$this->set_is_saving( false );

		// When deleting a global color or typo, the css variable still exists in the frontend
		// but without any value and it makes the element to be un styled even if there is a default style for the base element,
		// for that reason this method removes css files of the entire site.
		Plugin::instance()->files_manager->clear_cache();

		return $saved;
	}

	/**
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_document_controls();

		foreach ( $this->tabs as $tab ) {
			$tab->register_controls();
		}
	}

	protected function get_post_statuses() {
		return [
			'draft' => sprintf( '%s (%s)', __( 'Disabled', 'elementor' ), __( 'Draft', 'elementor' ) ),
			'publish' => __( 'Published', 'elementor' ),
		];
	}

	public function add_repeater_row( $control_id, $item ) {
		$meta_key = PageManager::META_KEY;
		$document_settings = $this->get_meta( $meta_key );

		if ( ! $document_settings ) {
			$document_settings = [];
		}

		if ( ! isset( $document_settings[ $control_id ] ) ) {
			$document_settings[ $control_id ] = [];
		}

		$document_settings[ $control_id ][] = $item;

		$page_settings_manager = SettingsManager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $document_settings, $this->get_id() );

		/** @var Kit $autosave **/
		$autosave = $this->get_autosave();

		if ( $autosave ) {
			$autosave->add_repeater_row( $control_id, $item );
		}

		// Remove Post CSS.
		$post_css = Post_CSS::create( $this->post->ID );

		$post_css->delete();

		// Refresh Cache.
		Plugin::$instance->documents->get( $this->post->ID, false );

		$post_css = Post_CSS::create( $this->post->ID );

		$post_css->enqueue();
	}
}

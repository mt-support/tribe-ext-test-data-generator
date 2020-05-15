<?php
namespace Tribe\Extensions\Test_Data_Generator;
use Tribe__Settings;
use Tribe__Template as Template;

class Page {

    /**
     * Stores the template class used.
     *
     * @since 1.0.0
     *
     * @var Template
     */
    protected $template;

    /**
     * Gets the instance of template class set for the metabox.
     *
     * @since 1.0.0
     *
     * @return Template Instance of the template we are using to render this metabox.
     */
    public function get_template() {
        if ( empty( $this->template ) ) {
            $this->set_template();
        }
        return $this->template;
    }

    /**
     * Normally ran when the class is setting up but configures the template instance that we will use render non v2 contents.
     *
     * @since 1.0.0
     *
     * @return void Setter with no return.
     */
    public function set_template() {
        $this->template = new Template();
        $this->template->set_template_origin( tribe( Plugin::class ) );
        $this->template->set_template_folder( 'src/admin-views' );
        // Setup to look for theme files.
        $this->template->set_template_folder_lookup( false );
        // Configures this templating class extract variables.
        $this->template->set_template_context_extract( true );
    }


    /**
     * @since 1.0.0
     * @var string
     */
    protected $menu_hook;

    /**
     * Returns registered submenu slug.
     * @since 1.0.0
     * @return string Registered submenu slug.
     */
    public function get_slug() {
        return 'test-data-generator';
    }

    /**
     * Returns the registered submenu page hook.
     * @since 1.0.0
     * @return string Registered submenu page hook.
     */
    public function get_menu_hook() {
        return $this->menu_hook;
    }

    /**
     * Add admin menu.
     * @since 1.0.0
     */
    public function add_menu() {
        $parent = class_exists( 'Tribe__Events__Main' ) ? Tribe__Settings::$parent_page : Tribe__Settings::$parent_slug;
        $this->menu_hook = add_submenu_page(
            $parent,
            __( 'Test Data Generator', 'tribe-ext-test-data-generator' ),
            __( 'Test Data', 'tribe-ext-test-data-generator' ),
            'edit_posts',
            $this->get_slug(),
            [ $this, 'render' ]
        );
    }

    /**
     * Render admin menu page.
     * @since 1.0.0
     */
    public function render() {
        $args = [];
        $this->get_template()->template( 'page', $args );
    }
}
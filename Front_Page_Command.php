<?php

/**
 * Set the default landing page for a site.
 *
 * ## EXAMPLES
 *
 *     # Set default landing page to 'Home'
 *     wp static-front-page set 'Home'
 *
 */
class Front_Page_Command extends WP_CLI_Command
{
    /**
     * Set the site front page
     *
     * ## EXAMPLES
     *
     *     # Set the site front page
     *     wp static-front-page <page>
     *
     * @synopsis <page>
     */
    public function __invoke($args)
    {
        $page = get_page_by_title($args[0]);
        $blog = get_bloginfo('name');
        if ($page) {
            update_option('page_on_front', $page->ID);
            update_option('show_on_front', 'page');
            WP_CLI::line("Front page on " . $blog . " set to '" . $args[0] . "'");
        } else {
            WP_CLI::line("Page '" . $args[0] . "' not found in " . $blog);
        }
    }

}

WP_CLI::add_command('static-front-page', 'Front_Page_Command');


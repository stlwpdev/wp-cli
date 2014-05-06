<?php

/**
 * Import / Export widgets
 *
 * ## EXAMPLES
 *
 *     # Import widgets from a file
 *     wp widgets import <file>
 *
 *     # Export widgets to a file
 *     wp widgets export-file <file>
 *
 *     # Export widgets to standard-out
 *     wp widgets export
 */
class Widgets_Command extends WP_CLI_Command
{

    /**
     * Import widgets from file
     *
     * ## EXAMPLES
     *
     *     # Import widgets from a file
     *     wp widgets import <file>
     *
     * @subcommand import
     * @synopsis <file>
     */
    public function import($args)
    {
        global $wpdb;
        $file = file_get_contents($args[0]);
        $import = json_decode($file);
        if (is_array($import)) {
            $this->flush_existing_widgets($wpdb);
            foreach ($import as $item) {
                $this->store_widget_option($wpdb, $item->option_name, $item->option_value);
            }
        }
    }

    /**
     * Export widgets
     *
     * ## EXAMPLES
     *
     *     # Export widgets
     *     wp widgets export
     *
     * @subcommand export
     */
    public function export($args)
    {
        global $wpdb;
        WP_CLI::line($this->serialize_widgets($wpdb));
    }

    /**
     * Export widgets to file
     *
     * ## EXAMPLES
     *
     *     # Export widgets to a file
     *     wp widgets export <file>
     *
     * @subcommand export-file
     * @synopsis <file>
     */
    public function export_file($args)
    {
        global $wpdb;
        $fp = fopen($args[0], 'w');
        if ($fp) {
            fwrite($fp, $this->serialize_widgets($wpdb));
            fclose($fp);
        } else {
            WP_CLI::line("Failed to open " + $args[0] + " for writing.");
        }
    }

    /**
     * @param $wpdb
     * @return string
     */
    private function serialize_widgets($wpdb)
    {
        return json_encode($wpdb->get_results(
            'select option_name, option_value from ' . $wpdb->options .
            " where option_name like '%widget%'"));
    }

    /**
     * @param $wpdb
     */
    private function flush_existing_widgets($wpdb)
    {
        $wpdb->query('delete from ' . $wpdb->options .
            " where option_name like '%widget%'");
    }

    /**
     * @param $wpdb
     * @param $option_name
     * @param $option_value
     */
    private function store_widget_option($wpdb, $option_name, $option_value)
    {
        $wpdb->query(
            $wpdb->prepare("insert into " . $wpdb->options .
                " (option_name, option_value)" .
                " values (%s, %s)", $option_name, $option_value)
        );
    }
}

WP_CLI::add_command('widgets', 'Widgets_Command');

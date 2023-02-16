<?php
defined('ABSPATH') || exit;



/**
 * WP_CLI 脚本
 * 调用方法：`wp vpsaff`
 * 
 */
if (!class_exists('JVps_AdminAff_Command')) {
    class JVps_AdminAff_Command
    {
        public function process_vps_stock()
        {
            $post_type = JVps_Admin::$slug;
            $args = array(
                'post_type' =>  $post_type,
                'orderby'   => 'post_modified',
                'order' => 'DESC',
            );

            $url_args = array();

            query_posts($args);
            while (have_posts()) : the_post();
                // if (get_the_ID() != 18){
                // 	break;
                // }
                $post_ID = get_the_ID();
                $terms = get_the_terms($post_ID, JVpsCustomTaxonomy::$slug);
                $term_meta = get_option('jvps_taxonomy_' . $terms[0]->term_id);
                // var_dump($term_meta);
                $search = $term_meta['check_str'];
                $html = '';
                if ('Y' == $term_meta['tax_cron']) {
                    $html .= 'ID：' . $post_ID;
                    $url = $term_meta['tax_aff'] . get_post_meta($post_ID, JVPS_PREFIX_KEY . 'pid', true);
                    $html .= ' -- ' . $url . ' -- ';
                    $res = @wp_safe_remote_post($url, $url_args);
                    $results = wp_remote_retrieve_body($res);
                    // var_dump($results);
                    if (is_wp_error($res)) {
                        WP_Cli::warning(' TIMEOUT', 'JVps_Admin');
                        break;
                    }

                    if (false === strpos($results, $search)) {
                        $stock = 1;
                        $html .= __(' IN STOCK', 'JVps_Admin');
                    } else {
                        $stock = 0;
                        $html .= __(' OUT OF STOCK','JVps_Admin');
                    }
                    update_post_meta($post_ID, JVps_Admin::$key . 'stock', sanitize_text_field($stock));
                    // update_option( 'JVps_Admin_taxonomy_'.$terms[0]->term_id, $term_meta ); 
                } else {
                    $html .= __('No need to check.','JVps_Admin');
                    $html .= ' -- Brand：' . $terms[0]->name . ' -- ID：' . $post_ID;
                }

                $the_time = current_time('mysql');
                $my_post = array(
                    'ID'            => $post_ID,
                    'post_date'     => $the_time,
                );
                wp_update_post($my_post);
                $html .=  ' -- ' . $the_time;

                WP_CLI::success($html);
            endwhile;
        }


        public function __invoke($args)
        {
            $this->process_vps_stock();
        }
    }






    if (class_exists('WP_CLI')) {

        $instance = new JVps_AdminAff_Command();

        WP_CLI::add_command('jvps', $instance);
    }
}

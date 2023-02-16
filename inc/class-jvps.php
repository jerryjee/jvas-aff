<?php
if (!defined('ABSPATH')) {
    exit;
}

class JVps
{
    public function __construct()
    {
        add_shortcode('jvps', array($this, 'register_shortcodes'));
    }

    public function register_shortcodes($atts)
    {
        $atts = shortcode_atts(array(
            'fields' => '厂家，CPU，内存，硬盘，流量，价格，购买链接',
            'ids' => '1',
            'class' => ' table table-hover table-condensed table-bordered '
        ), $atts, 'jvps');

        $html = '<figure class="wp-block-table is-style-stripes">';
        $html .= '<table class="'.$atts['class'].'">';
        $fields = str_replace(array('，', '|'), ',', $atts['fields']);
        if ($fields) {
            $fields = array_map('trim', explode(',', $fields));
            $html .= '<thead><tr>';
            foreach ($fields as $field) {
                $html .= '<th>' . $field . '</th>';
            }
            $html .= '</tr></thead>';
        }
        if ($atts['ids']) {
            $ids = str_replace(array('，', '|'), ',', $atts['ids']);
            $ids = array_map('trim', explode(',', $ids));
            $args = array(
                'post__in' => $ids,
                'post_type' =>  JVps_Admin::$slug,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                // 'ignore_sticky_posts'   =>  true,
            );
            
            // the query
            $q = new WP_Query($args);
            if ($q->have_posts()) :
                $html .= '<tbody>';
                while ($q->have_posts()) : $q->the_post();
                    $html .= '<tr>';
                    $post_id = get_the_ID();
                    foreach ($fields as $field) {
                        $html .= '<td>' . JVps::get_post_meta($post_id, $field) . '</td>';
                    }
                    $html .= '</tr>';
                endwhile;
                $html .= '</tbody>';
            endif;

            wp_reset_postdata();
        }

        $html .= '</table>';
        $html .= '</figure>';

        return $html;
    }

    public static function get_post_meta($post_id, $key)
    {
        switch ($key):
            case 'id':
            case 'ID':
                return $post_id;
                break;
            case '厂商':
            case '厂家':
                $terms = get_the_terms(get_the_ID(), JVpsCustomTaxonomy::$slug);
                return $terms[0]->name;
                break;
            case '内存':
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'ram', true);
                break;
            case '流量':
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'traffic', true);
                break;
            case __('路由', 'jvps'):
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'route', true);
                break;
            case __('硬盘', 'jvps'):
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'disk', true);
                break;
            case '价格':
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'price', true);
                break;
            case 'CPU':
            case 'cpu':
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'cpu', true);
                break;
            case '带宽':
                return get_post_meta($post_id, JVPS_PREFIX_KEY . 'bandwidth', true);
                break;
            case '购买链接':
                $link = self::get_purchase_link($post_id);
                if ($link) {
                    $terms = get_the_terms(get_the_ID(), JVpsCustomTaxonomy::$slug);
                    return  sprintf(
                        '<a rel="nofollow" title="%s" href="%s" class="btn btn-primary active" role="button" aria-pressed="true">%s</a>',
                        $terms[0]->name,
                        $link,
                        __('购买', 'jvps')
                    );
                }
                return '-';
                break;
            default:
                return;
                break;
        endswitch;
    }

    public static function get_purchase_link($post_id)
    {
        $terms = get_the_terms($post_id, JVpsCustomTaxonomy::$slug);
        $term_meta = get_option("jvps_taxonomy_" . $terms[0]->term_id);
        $pid = get_post_meta($post_id, JVPS_PREFIX_KEY . 'pid', true);
        $link = esc_url($term_meta['tax_aff']) . $pid ;
        if (1 == get_post_meta($post_id, JVPS_PREFIX_KEY . 'stock', true) || $term_meta['tax_cron'] == 'N') {
            return $link;
        }
        return;
    }
}

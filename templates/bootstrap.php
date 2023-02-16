<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>

<main id="app">
    <div class="container-fluid">
        <section>
            <p><?php the_content(); ?></p>

        </section>
        <div class="jads"></div>
        <?php query_posts($args);
        if (have_posts()) : ?>
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <!-- <th>#ID</th> -->
                            <th><?php _e('厂家', 'jvps'); ?></th>
                            <?php foreach ($columns as $key => $val) :  if (in_array($key, $skip_columns))   continue;    ?>
                                <th><?php echo $val; ?></th>
                            <?php endforeach;   ?>
                            <!-- <th><?php _e('更新时间', 'jvps'); ?></th> -->
                            <th><?php _e('库存', 'jvps'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while (have_posts()) : the_post();
                            $terms = get_the_terms(get_the_ID(), JVpsCustomTaxonomy::$slug);
                            $term_meta = get_option("jvps_taxonomy_" . $terms[0]->term_id);
                        ?>

                            <tr class="<?php if (is_sticky()) {
                                            echo 'table-primary ';
                                        } ?>" title="<?php the_ID(); ?>#<?php the_title(); ?> - <?php _e('更新于：', 'jvps'); ?><?php the_time('Y-m-d G:i:s'); ?>">
                                <!-- <td><?php the_ID(); ?>#<?php the_title(); ?></td> -->
                                <td>
                                    <?php printf(
                                        '<a href="%s" class="jvps-toggle nav-link" title="%s">%s</a>',
                                        get_search_link($terms[0]->name),
                                        $terms[0]->description,
                                        $terms[0]->name
                                    ); ?>
                                </td>

                                <?php foreach ($columns as $key => $val) :  if (in_array($key, $skip_columns))   continue;    ?>
                                    <td><?php echo get_post_meta(get_the_ID(), $jkey . $key, true); ?></td>
                                <?php endforeach;   ?>
                                <!-- <td><?php the_time('Y-m-d G:i:s'); ?></td> -->
                                <td>
                                    <?php 
                                    $pid = get_post_meta(get_the_ID(), JVPS_PREFIX_KEY . 'pid', true);
                                    $sale_str = sprintf(
                                        '<a rel="nofollow" href="%s" class="btn btn-primary active" role="button" aria-pressed="true">%s</a>',
                                        esc_url($term_meta['tax_aff']) . $pid,
                                        __('购买', 'jvps')
                                    );
                                    $soldout_str = sprintf('<button class="btn btn-secondary" disabled="true">%s</button>', __('缺货', 'jvps'));
                                    if (get_post_meta(get_the_ID(), $jkey . 'stock', true) == 1 || $term_meta['tax_cron'] == 'N') {
                                        echo $sale_str;
                                    } else {
                                        echo $soldout_str;
                                    }
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

    </div>

    <?php wp_reset_query();   ?>



<?php endif;    ?>


</main>



<?php get_footer();
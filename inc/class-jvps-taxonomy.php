<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class  JVpsCustomTaxonomy{

    public static $slug = 'jvps_tax';

    public static $rewrite = 'jvps';

    function __construct(){
        add_action( 'init', array( $this , 'jvps_register_taxonomy' ) );

        // 新建分类页面添加自定义字段输入框
        add_action( 'jvps_tax_add_form_fields', array( $this, 'add_tax_field' ) );
        // 编辑分类页面添加自定义字段输入框
        add_action( 'jvps_tax_edit_form_fields', array( $this, 'edit_tax_field' ) );
 
        // 保存自定义字段数据
        add_action( 'edited_jvps_tax', array( $this, 'save_tax_meta' ), 10, 2 );
        add_action( 'create_jvps_tax', array( $this, 'save_tax_meta' ), 10, 2 );
  
  
    } // __construct

    function jvps_register_taxonomy() {
        $labels = [
           'name'              => _x( 'Brand', 'taxonomy general name' ),
           'singular_name'     => _x( 'Brand', 'taxonomy singular name' ),
           'search_items'      => __( 'Search Brands' ),
           'all_items'         => __( 'All Brands' ),
           'parent_item'       => __( 'Parent Brand' ),
           'parent_item_colon' => __( 'Parent Brand:' ),
           'edit_item'         => __( 'Edit Brand' ),
           'update_item'       => __( 'Update Brand' ),
           'add_new_item'      => __( 'Add New Brand' ),
           'new_item_name'     => __( 'New Brand Name' ),
           'menu_name'         => __( 'Brand Category' ),
        ];
        $args   = [
           'hierarchical'      => true, // make it hierarchical (like categories)
           'labels'            => $labels,
           'show_ui'           => true,
           'show_admin_column' => true,
           'query_var'         => true,
           'rewrite'           => false,
        //    'rewrite'           => [ 'slug' => self::$rewrite ],
        ];
        register_taxonomy( self::$slug, [ self::$rewrite ], $args );
     }    

     public function add_tax_field( $term ){
         
        // $term_id 是当前分类的id
        $term_id = method_exists('term','term_id') ? $term->term_id : '';
         
        // 获取已保存的option
        $term_meta = get_option( "jvps_taxonomy_$term_id" );
        // option是一个二维数组
        $tax_aff = isset( $term_meta['tax_aff'] ) ? esc_url_raw( $term_meta['tax_aff'] ) : '';
        $tax_cron = isset( $term_meta['tax_cron'] ) ? esc_html( $term_meta['tax_cron'] ) : '';
        $check_str = isset( $term_meta['check_str'] ) ? esc_html( $term_meta['check_str'] ) : '';
         
        /**
         *   TODO: 在这里追加获取其他自定义字段值，如：
         *   $keywords = $term_meta['tax_keywords'] ? $term_meta['tax_keywords'] : '';
         */
    ?>
        <div class="form-field">
            <label for="term_meta[tax_aff]"><?php _e('aff','jvps');?></label>
            <input type="text" name="term_meta[tax_aff]" id="term_meta[tax_aff]" value="<?php echo $tax_aff; ?>" size="40" />
            <p class="description"><?php  _e('Aff Desscription','jvps');?></p>
        
        </div><!-- /.form-field -->
         
        <div class="form-field">
            <label for="term_meta[tax_cron]"><?php _e('Cron','jvps');?></label>
            <select name="term_meta[tax_cron]" id="term_meta[tax_cron]" class="postform">
                <option value="N" <?php echo $tax_cron == 'N' ? 'selected' : '';?>><?php _e('No','jvps');?></option>
                <option value="Y" <?php echo $tax_cron == 'Y' ? 'selected' : '';?>><?php _e('Yes','jvps');?></option>
            </select>
            <p class="description"><?php  _e('Need cron','jvps');?></p>
        </div><!-- /.form-field -->

        <div class="form-field">
            <label for="term_meta[check_str]"><?php _e('Check String','jvps');?></label>
            <input type="text" name="term_meta[check_str]" id="term_meta[check_str]" value="<?php echo $check_str; ?>" size="40" />
            <p class="description"><?php  _e('Check String','jvps');?></p>
        </div><!-- /.form-field -->        

    <?php
    } // edit_tax_image_field


    /**
     * 编辑分类页面添加自定义字段输入框
     *
     * @uses get_option()       从option表中获取option数据
     * @uses esc_url()          确保字符串是url
     */
    public function edit_tax_field( $term ){
         
        // $term_id 是当前分类的id
        $term_id = $term->term_id;
         
        // 获取已保存的option
        $term_meta = get_option( "jvps_taxonomy_$term_id" );
        // option是一个二维数组
        $tax_aff = isset( $term_meta['tax_aff'] ) ? esc_url_raw( $term_meta['tax_aff'] ) : '';
        $tax_cron = isset( $term_meta['tax_cron'] ) ? esc_html( $term_meta['tax_cron'] ) : '0';
        $check_str = isset( $term_meta['check_str'] ) ? esc_html( $term_meta['check_str'] ) : '';
         
    ?>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_aff]"><?php _e('aff','jvps');?></label>
                <td>
                    <input type="text" name="term_meta[tax_aff]" id="term_meta[tax_aff]" value="<?php echo $tax_aff; ?>" size="68" />
                    <p class="description"><?php  _e('Aff Desscription','jvps');?></p>
                </td>
            </th>
        </tr><!-- /.form-field -->
         
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_cron]"><?php _e('Cron','jvps');?></label>
                <td>
                <select name="term_meta[tax_cron]" id="term_meta[tax_cron]" class="postform">
                    <option value="N" <?php echo $tax_cron == 'N' ? 'selected' : '';?>><?php _e('No','jvps');?></option>
                    <option value="Y" <?php echo $tax_cron == 'Y' ? 'selected' : '';?>><?php _e('Yes','jvps');?></option>
                </select>
                <p class="description"><?php  _e('Need cron','jvps');?></p>
                </td>
            </th>
        </tr><!-- /.form-field -->

        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[check_str]"><?php _e('Check String','jvps');?></label>
                <td>
                <input type="text" name="term_meta[check_str]" id="term_meta[check_str]" value="<?php echo $check_str; ?>" size="40" />
                <p class="description"><?php  _e('Check String','jvps');?></p>
                </td>
            </th>
        </tr><!-- /.form-field -->


    <?php
    } // edit_tax_image_field
  
    /**
     * 保存自定义字段的数据
     *
     * @uses get_option()      从option表中获取option数据
     * @uses update_option()   更新option数据，如果没有就新建option
     */
    public function save_tax_meta( $term_id ){
  
        if ( isset( $_POST['term_meta'] ) ) {
             
            // $term_id 是当前分类的id
            $t_id = $term_id;
            $term_meta = array();
             
            // 获取表单传过来的POST数据，POST数组一定要做过滤
            $term_meta['tax_aff'] = isset ( $_POST['term_meta']['tax_aff'] ) ? esc_url_raw( $_POST['term_meta']['tax_aff'] ) : '';
            $term_meta['tax_cron'] = isset ( $_POST['term_meta']['tax_cron'] ) ? sanitize_text_field( $_POST['term_meta']['tax_cron'] ) : 'N';
            $term_meta['check_str'] = isset ( $_POST['term_meta']['check_str'] ) ? sanitize_text_field ( $_POST['term_meta']['check_str'] ) : '';

            /**
             *   TODO: 在这里追加获取其他自定义字段表单的值，如：
             *   $term_meta['tax_keywords'] = isset ( $_POST['term_meta']['tax_keywords'] ) ? $_POST['term_meta']['tax_keywords'] : '';
             */
 
            // 保存option数组
            update_option( "jvps_taxonomy_$t_id", $term_meta );
  
        } // if isset( $_POST['term_meta'] )
    } // save_tax_meta
  
} // JVpsCustomTaxonomy
  

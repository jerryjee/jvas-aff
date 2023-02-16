<?php
if (!defined('ABSPATH')) {
	exit;
}

class JVps_Admin
{

	public static $key = JVPS_PREFIX_KEY;

	public static $slug = 'jvps';

	public static $rewrite_slug_default = 'jgo';

	public function __construct()
	{
		add_action('init', array($this, 'register_post_type'));
		add_filter('post_updated_messages', array($this, 'updated_message'));
		add_action('admin_menu', array($this, 'add_meta_box'));
		add_action('save_post', array($this, 'meta_box_save'), 1, 2);
		add_action('manage_posts_custom_column', array($this, 'columns_data'));
		add_filter('manage_edit-jvps_columns', array($this, 'columns_filter'));
		add_action('plugins_loaded', array('JVpsTemplate', 'get_instance'));
	}

	public static function jvps_column()
	{
		return array( 
			'pid'	=>	__('pid','jvps'),
			'location' => __('位置', 'jvps'),
			'cpu' => __('CPU', 'jvps'),
			'ram' => __('内存', 'jvps'),
			'disk' => __('硬盘', 'jvps'),
			// 'brand' => __('Brand Name','jvps') ,
			'bandwidth' => __('带宽', 'jvps'),
			'traffic' => __('流量', 'jvps'),
			'route' => __('路由', 'jvps'),
			'ipv4' => __('Ipv4', 'jvps'),
			'arch' => __('架构', 'jvps'),
			'price' => __('价格', 'jvps'),
			// 'stock' => '' ,
		);
	}

	/**
	 * Register Post Type.
	 */
	public function register_post_type()
	{

		$slug = self::$slug;

		$rewrite_slug_default = self::$rewrite_slug_default;

		$labels = array(
			'name'               => __('JVps Aff', 'jvps'),
			'singular_name'      => __('Aff', 'jvps'),
			'add_new'            => __('Add New', 'jvps'),
			'add_new_item'       => __('Add New Aff', 'jvps'),
			'edit'               => __('Edit', 'jvps'),
			'edit_item'          => __('Edit Aff', 'jvps'),
			'new_item'           => __('New Aff', 'jvps'),
			'view'               => __('View Aff', 'jvps'),
			'view_item'          => __('View Aff', 'jvps'),
			'search_items'       => __('Search Aff', 'jvps'),
			'not_found'          => __('No Aff found', 'jvps'),
			'not_found_in_trash' => __('No Aff found in Trash', 'jvps'),
			'messages'           => array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: %s: link for the update */
				1  => __('URL updated. <a href="%s">View URL</a>', 'jvps'),
				2  => __('Custom field updated.', 'jvps'),
				3  => __('Custom field deleted.', 'jvps'),
				4  => __('URL updated.', 'jvps'),
				/* translators: %s: date and time of the revision */
				5  => isset($_GET['revision']) ? sprintf(__('Post restored to revision from %s', 'jvps'), wp_post_revision_title((int) $_GET['revision'], false)) : false, // phpcs:ignore
				/* translators: %s: URL to view */
				6  => __('URL updated. <a href="%s">View URL</a>', 'jvps'),
				7  => __('URL saved.', 'jvps'),
				8  => __('URL submitted.', 'jvps'),
				9  => __('URL scheduled', 'jvps'),
				10 => __('URL draft updated.', 'jvps'),
			),
		);

		$labels = apply_filters('jvps_cpt_labels', $labels);

		$rewrite_slug = apply_filters('jvps_slug', $rewrite_slug_default);

		$rewrite_slug = sanitize_title($rewrite_slug, $rewrite_slug_default);

		// Ref: https://developer.wordpress.org/reference/functions/add_post_type_support/.
		$supports_array = apply_filters('jvps_post_type_supports', array('title',));

		// Ref: https://developer.wordpress.org/reference/functions/register_post_type/.
		register_post_type(
			$slug,
			array(
				'labels'              => $labels,
				'public'              => true,
				'exclude_from_search' => apply_filters('jvps_exclude_from_search', true),
				'show_ui'             => true,
				'query_var'           => true,
				'menu_position'       => 20,
				'supports'            => $supports_array,
				// 'rewrite'             => array(
				// 	'slug'       => $rewrite_slug,
				// 	'with_front' => false,
				// ),				
				'rewrite'             => false,
				'show_in_rest'        => true,
			)
		);
		register_taxonomy_for_object_type('jvps_tax', $slug);
	}

	/**
	 * Colum filter.
	 *
	 * @param  array $columns Columns.
	 *
	 * @return array          Filtered columns.
	 */
	public function columns_filter($columns)
	{
		$admin_columns = array(

			// 'stock' => '' ,
		);
		$columns = array(
			'brand' => __('厂家', 'jvps'),
			'title'     => __('套餐名', 'jvps'),
			'id'	=>	__('ID'),
			'pid'	=>	__('pid','jvps'),
			'cb'        => '<input type="checkbox" />',
			'location' => __('位置', 'jvps'),
			'cpu' => __('CPU', 'jvps'),
			'ram' => __('内存', 'jvps'),
			'disk' => __('硬盘', 'jvps'),
			'bandwidth' => __('带宽', 'jvps'),
			'traffic' => __('流量', 'jvps'),
			'route' => __('路由', 'jvps'),
			'ipv4' => __('Ipv4', 'jvps'),
			'arch' => __('架构', 'jvps'),
			'price' => __('价格', 'jvps'),
			// 'aff'	=> __('Aff','jvps'),
			'stock'	=>	__('库存', 'jvps'),
		);

		return $columns;
	}

	/**
	 * Columns data.
	 *
	 * @param  array $column Columns.
	 */
	public function columns_data($column)
	{

		global $post;

		$$column = get_post_meta($post->ID, self::$key . $column, true);
		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		);
		if ('brand' == $column) {
			echo get_the_term_list($post->ID, 'jvps_tax');
		} elseif ('stock' === $column) {
			echo esc_html($stock ? '有货' : '无货');
		} elseif ('id' == $column){
			echo $post->ID;
		} else {
			echo $$column;
		}

	}

	/**
	 * Update message.
	 *
	 * @param  array $messages Messages.
	 *
	 * @return array           Messages.
	 */
	public function updated_message($messages)
	{

		$surl_object = get_post_type_object('jvps');

		$messages['jvps'] = $surl_object->labels->messages;

		$permalink = get_permalink();

		if ($permalink) {
			foreach ($messages['jvps'] as $id => $message) {
				$messages['jvps'][$id] = sprintf($message, $permalink);
			}
		}

		return $messages;
	}

	/**
	 * Add metabox.
	 */
	public function add_meta_box()
	{
		add_meta_box('jvps', __('VPS Information', 'jvps'), array($this, 'meta_box'), 'jvps', 'normal', 'high');
		add_meta_box('jvps_sticky', __('Sticky', 'jvps'), array($this, 'jvps_sticky'), 'jvps', 'side', 'high');
		wp_register_script('js',  JVPS_URL . '/inc/plugin-admin.js', array('jquery'), '0.1', true);
	}

	function jvps_sticky()
	{
		printf('<input id="super-sticky" name="sticky" type="checkbox" value="sticky" %s /><label for="super-sticky" class="selectit">置顶</label>', checked(is_sticky(), true, false));
	}

	/**
	 * Metabox.
	 */
	public function meta_box()
	{

		global $post;

		$admin_columns = self::jvps_column();

		// $arch_options = array('OpenVz','KVM','Arm');

		printf('<input type="hidden" name="_jvps_nonce" value="%s" />', esc_attr(wp_create_nonce(plugin_basename(__FILE__))));

		foreach ($admin_columns as $key => $val) {
			$column = self::$key . $key;
			printf('<p><label for="%s">%s</label></p>', $column, esc_html($val));
			if ($key == 'price') {
				printf(
					'<p><textarea rows="6" cols="100" name="%s" id="%s">%s</textarea></p>',
					$column,
					$column,
					esc_attr(get_post_meta($post->ID, $column, true))
				);
			} else {
				printf(
					'<p><input style="%s" type="text" name="%s" id="%s" value="%s" /></p>',
					'width:99%',
					$column,
					$column,
					esc_attr(get_post_meta($post->ID, $column, true))
				);
			}
		}
	}

	/**
	 * Metabox save function.
	 *
	 * @param  string  $post_id Post Id.
	 * @param  WP_Post $post   Post.
	 */
	public function meta_box_save($post_id, $post)
	{

		$key = self::$key;

		// Verify the nonce.
		// phpcs:ignore
		if (!isset($_POST['_jvps_nonce']) || !wp_verify_nonce($_POST['_jvps_nonce'], plugin_basename(__FILE__))) {
			return;
		}

		// Don't try to save the data under autosave, ajax, or future post.
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		};

		if (defined('DOING_AJAX') && DOING_AJAX) {
			return;
		};

		if (defined('DOING_CRON') && DOING_CRON) {
			return;
		};

		// Is the user allowed to edit the URL?
		if (!current_user_can('edit_posts') || 'jvps' !== $post->post_type) {
			return;
		}

		foreach (self::jvps_column() as $k => $v) {
			$k = $key . $k;
			$value = isset($_POST[$k]) ? sanitize_text_field($_POST[$k]) : '';
			if ($value) {
				// Save/update.
				update_post_meta($post->ID, $k, $value);
			} else {
				// Delete if blank.
				delete_post_meta($post->ID, $k);
			}
		}
	}
}

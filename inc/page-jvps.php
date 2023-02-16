<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$check = isset( $_GET['c'] ) ? (int)$_GET['c'] : false;

$post_type = JVps_Admin::$slug;
$args = array(
	'post_type' =>  $post_type,
	'orderby'   => 'post_modified',
	'order' => 'DESC',
);
$columns = JVps_Admin::jvps_column();
$skip_columns = array('arch');
$jkey = JVps_Admin::$key;

if ( !$check ){
	require_once dirname( plugin_dir_path( __FILE__ ) ).'/templates/bootstrap.php';
	exit();
}

$url_args = array(

);

query_posts( $args );
while (have_posts()) : the_post(); 
	// if (get_the_ID() != 18){
	// 	break;
	// }
	$post_ID = get_the_ID();
	$terms = get_the_terms( $post_ID , JVpsCustomTaxonomy::$slug );
	$term_meta = get_option( 'jvps_taxonomy_'.$terms[0]->term_id);
	// var_dump($term_meta);
	$search = $term_meta['check_str'];
	echo '<p>';

	if ( $term_meta['tax_cron'] == 'Y' ){
		echo 'ID：'.$post_ID;
		$url = $term_meta['tax_aff'].get_the_title();
		echo ' -- '. $url .' -- ';
		$res = @wp_safe_remote_post( $url , $url_args );
		$results = wp_remote_retrieve_body( $res );
		// var_dump($results);
		if (is_wp_error($res) && $errReturn == FALSE) {
			_e(' TIMEOUT','jvps');
			break;
		}
		
		if(strpos($results,$search) === false){ 
			$stock = 1;
			_e(' IN STOCK','jvps');
		}else{
			$stock = 0;
			_e(' OUT OF STOCK');
		}
		update_post_meta( $post_ID , JVps_Admin::$key.'stock' , sanitize_text_field( $stock ) );
		// update_option( 'jvps_taxonomy_'.$terms[0]->term_id, $term_meta ); 
	}else{
		_e('No need to check.');
		echo ' -- Brand：'.$terms[0]->name.' -- ID：'. $post_ID;
	}

	$the_time = current_time('mysql');
	$my_post = array(
        'ID'            => $post_ID,
        'post_date'     => $the_time,
    );
	wp_update_post( $my_post );
	echo ' -- '.$the_time;
	echo "</p>\r\n";












           
endwhile;

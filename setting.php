<?php
/*Plugin Name: Custom Setting 
 * Plugin URI: https://www.exmapleplugin@restapi.com/plugin
 * Author: DP
 * Author URI: https://www.exmaplepluginauthor@restapi.com/
 * Description: This plugin contains All custom settings
 * Version: 1.0.0
*/



define('CUSTOM_SETTING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_SETTING_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!class_exists('Stripe\Stripe')) {

    require_once(CUSTOM_SETTING_PLUGIN_PATH . '/vendor/autoload.php');
    require_once(CUSTOM_SETTING_PLUGIN_PATH  . 'secret_key.php');
}
add_action('init', 'create_custom_product_post_type');
function create_custom_product_post_type()
{
    $args = array(
        'labels' => array(
            'name' => __('Product'),
            'singular_name' => __('Product'),
            'add_new_post' => _('Add New Product'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-archive',
        'rewrite' => array('slug' => 'product', 'with_front' => false),
        'position' => '5'
    );

    register_post_type('product_type', $args);
}


add_action('init', 'product_category_register');
function product_category_register()
{
    $labels = array(
        'name' => 'Product Category',
        'singular_name' => 'Product Category',
        'search_items' => 'Search Product Categories',
        'all_items' => 'All Product Categories',
        'edit_item' => 'Edit Product Category',
        'add_new_item' => 'Add New Product Category',
        'update_item' => 'Update Product Category',
        'menu_name' => 'Product Category'
    );

    $args = array(
        'label' => 'Product Category',
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'hierarchical' => true,
        'rewrite' => array(
            'slug' => 'product-category',
            'with_front' => false,
            'hierarchical' => true
        ),
    );

    register_taxonomy('product_category', 'product_type', $args);
}


add_action('add_meta_boxes', 'add_product_meta_box');
function add_product_meta_box()
{
    add_meta_box(
        'product_meta_box', //id
        'Product Detail', //title
        'display_product_meta_box',
        'product_type', //post_type
        'normal', //where to display
        'high' //priority
    );
}

function display_product_meta_box($post)
{

    $names = [];
    $index = 1;
    while ($name_value = get_post_meta($post->ID, 'name_' . $index, true)) {
        $names[] = $name_value;
        $index++;
    }

    $typos = [];
    $index = 1;
    while ($typo_value = get_post_meta($post->ID, 'typo_' . $index, true)) {
        $typos[] = $typo_value;
        $index++;
    }

    require_once(CUSTOM_SETTING_PLUGIN_PATH . '/html/display_product_meta_box.php');
}



add_action('save_post', 'save_product_meta_box_data');
function save_product_meta_box_data($post_id)
{

    $index = 1;
    while (get_post_meta($post_id, 'name_' . $index, true)) {
        delete_post_meta($post_id, 'name_' . $index);
        $index++;
    }
    $index = 1;
    while (get_post_meta($post_id, 'typo_' . $index, true)) {
        delete_post_meta($post_id, 'typo_' . $index);
        $index++;
    }

    if (isset($_POST['name']) && is_array($_POST['name'])) {
        foreach ($_POST['name'] as $index => $name_value) {
            update_post_meta($post_id, 'name_' . ($index + 1), $name_value);
        }
    }

    if (isset($_POST['typo']) && is_array($_POST['typo'])) {
        foreach ($_POST['typo'] as $index => $typo_value) {
            update_post_meta($post_id, 'typo_' . ($index + 1), $typo_value);
        }
    }
}



function website_custom_style()
{

    wp_enqueue_style('main_css', get_stylesheet_uri());

    wp_enqueue_style('style_css', get_template_directory_uri() . '/css/styles.css');

    wp_enqueue_style('bootsrap_css', "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css");

    wp_enqueue_script(
        'bootstrap-js-file',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        array(),
        '5.3.3',
        true
    );

    wp_enqueue_script(
        'jQuery',
        'https://code.jquery.com/jquery-3.7.1.js',
        array(),
        '3.7.1',
        true
    );

    if (@$_GET['post_type'] == 'product') {
        wp_enqueue_script(
            'js-file',
            CUSTOM_SETTING_PLUGIN_URL . '/js/main.js',
            array('jQuery'),
            '1.0.0',
            true
        );
    }
}
add_action("wp_enqueue_scripts", "website_custom_style");


// Add the custom columns to the book post type:
add_filter('manage_product_posts_columns', 'set_custom_edit_product_columns');
function set_custom_edit_product_columns($columns)
{

    if (isset($columns['date'])) {
        unset($columns['date']);
    }
    $columns['author'] = _('Author');
    $columns['date'] = _('Date');
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_product_posts_custom_column', 'custom_product_column', 10, 2);
function custom_product_column($column, $post_id)
{
    switch ($column) {
        case 'author':
            echo the_author_meta('user_nicename', $post_id);
            break;
    }
}


add_action('admin_enqueue_scripts', 'my_plugin_add_js');
function my_plugin_add_js()
{
    wp_enqueue_script(
        'my-js-file',
        CUSTOM_SETTING_PLUGIN_URL . '/js/main.js',
        array('jquery'),
        '1.0',
        true
    );
}




add_filter('get_avatar', 'custom_user_avatar', 10, 5);
function custom_user_avatar($avatar, $id_or_email, $size, $default, $alt)
{

    if (is_numeric($id_or_email)) {
        $user = get_user_by('id', $id_or_email);
    } elseif (is_object($id_or_email)) {
        $user = $id_or_email;
    } else {
        $user = get_user_by('email', $id_or_email);
    }


    if ($user) {


        $att_id = get_user_meta($user->ID, 'profile_image', true);


        if ($att_id) {
            $custom_image = wp_get_attachment_url($att_id);
        } else {
            $custom_image = site_url() . "/wp-content/uploads/2024/10/images.png";
        }


        if (!empty($custom_image)) {
            $avatar = '<img src="' . esc_url($custom_image) . '" alt="' . esc_attr($alt) . '" class="avatar avatar-' . esc_attr($size) . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" />';
        }
    }

    return $avatar;
}





add_action('edit_user_profile', 'remove_unwanted_fields');
function remove_unwanted_fields($user)
{
    // Remove specific fields based on conditions
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.user-rich-editing-wrap , .user-admin-color-wrap , .user-comment-shortcuts-wrap , .user-admin-bar-front-wrap ,.user-language-wrap , #application-passwords-section', ).hide();

        });
    </script>
<?php
}

//function to remove_html_from_jwt_auth_error
add_filter('rest_post_dispatch', 'remove_html_from_jwt_auth_error', 10, 3);
function remove_html_from_jwt_auth_error($response, $server, $request)
{
    if (isset($response->data['message'])) {

        $response->data['message'] = wp_strip_all_tags($response->data['message']);
    }
    return $response;
}



add_action('wp_ajax_nopriv_load_more', 'load_more');
add_action('wp_ajax_load_more', 'load_more');

function load_more()
{
    $paged = isset($_POST['page']) ? intval($_POST['page']) + 1 : 1;
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $term_slug = isset($_POST['term_slug']);

    $args = [
        'post_type' => 'book',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'paged'          => $paged,
        'tax_query' => array(
            array(
                'taxonomy' => 'books_type',
                'terms' => $cat_id,
            )
        )
    ];
    $query = new WP_Query($args);
    $posts_data = $query->posts;
    if ($query->have_posts()) {

        require_once(CUSTOM_SETTING_PLUGIN_PATH . '/html/load_more.php');

        wp_reset_postdata();
    } else {

        echo 'no_more';
    }

    wp_die();
}



add_action('wp_ajax_nopriv_load_category_post', 'load_category_post');
add_action('wp_ajax_load_category_post', 'load_category_post');

function load_category_post()
{
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    if ($cat_id !== 0) {
        $term = get_term_by('id', $cat_id, 'books_type');
        $args = array(
            'post_type' => 'book',
            'post_status' => 'publish',
            'post_per_page' => 9,
            'tax_query' => array(
                array(
                    'taxonomy' => 'books_type',
                    'terms' => $cat_id
                ),
            ),

        );
    } else {
        $args = array(
            'post_type'      => 'book',
            'post_status'    => 'publish',
            'posts_per_page' => 9,
        );
    }
    $query = new WP_Query($args);
    $posts_data = $query->posts;

    require_once(CUSTOM_SETTING_PLUGIN_PATH . '/html/load_category_post.php');

    wp_die();
}


add_action('wp_ajax_nopriv_export_movie_data', 'export_movie_data');
add_action('wp_ajax_export_movie_data', 'export_movie_data');

function export_movie_data()
{
    $args = [
        'post_type' => 'movie',
        'posts_per_page' => -1
    ];

    $query = new WP_Query($args);

    //csv header 
    $csv = 'Post_ID,Post_Title,Post_Date,Category,Thumbnail URL' . "\n";

    if ($query->have_posts()) {


        while ($query->have_posts()) {
            $query->the_post();

            $thumbnail_id = get_post_thumbnail_id();
            $thumbnail_url = wp_get_attachment_url($thumbnail_id);
            $post_date_and_time = get_the_date('Y-m-d') . ' ' . get_the_time('H:i:s');
            $category_details = get_the_terms(get_the_ID(), 'movies-type');
            if ($category_details && !is_wp_error($category_details)) {
                foreach ($category_details as $category) {
                    $category_name = $category->name;
                }
            }
            //$csv .= '"' . get_the_ID() . '","' . str_replace('"', '""', get_the_title()) . '","' . str_replace('"', '""', get_the_date()) . '","' . str_replace('"', '""', get_the_content()) . '","' . str_replace('"', '""', $thumbnail_url) . '"' . "\n";
            $csv .= '"' . get_the_ID() . '","' . get_the_title() . '","' . $post_date_and_time . '","'  . $category_name . '","' .
                $thumbnail_url . '"' . "\n";
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=movie_data.csv');
        echo $csv;
        exit;
    } else {
        echo "0";
    }
}


// function create_news_post_type()
// {
//     $args = array(
//         'labels'      => array(
//             'name'          => __('News'),
//             'singular_name' => __('News'),
//         ),
//         'public'      => true,
//         'has_archive' => true,
//         'supports'    => array('title', 'editor', 'thumbnail'),
//         'menu_icon'   => 'dashicons-book-alt',
//         'rewrite'     => array('slug' => 'news'),
//     );
//     register_post_type('news', $args);
// }
// add_action('init', 'create_news_post_type');


add_filter('manage_news_posts_columns', 'set_custom_columns');
function set_custom_columns($columns)
{

    if (isset($columns['date'])) {
        unset($columns['date']);
    }
    $columns['category'] = __('Category', 'text_domain');
    $columns['date'] = __('Date', 'text_domain');
    return $columns;
}

add_action('manage_news_posts_custom_column', 'custom_movie_column', 10, 2);
function custom_movie_column($column, $post_id)
{
    switch ($column) {
        case 'category':
            echo get_post_meta($post_id, 'category', true);
            break;
    }
}

add_filter('manage_book_posts_columns', 'set_author_column');
function set_author_column($columns)
{
    if (isset($columns['date'])) {
        unset($columns['date']);
    }
    $columns['author'] = _('Author');
    $columns['date'] = _('Date');
    return $columns;
}

add_filter('manage_quiz_posts_columns', 'set_quiz_custom_columns');
function set_quiz_custom_columns($columns)
{
    if (isset($columns['date'])) {
        unset($columns['date']);
    }
    $columns['author'] = _('Author');
    $columns['date'] = _('Date');
    return $columns;
}

function custom_quiz_column($column, $post_id)
{
    switch ($column) {
        case 'author':
            echo the_author_meta('user_nicename', $post_id);
            break;
    }
}
add_action('manage_quiz_posts_custom_columns', 'custom_quiz_column', 10, 2);


add_action('wp_ajax_nopriv_import_data', 'import_data');
add_action('wp_ajax_import_data', 'import_data');
function import_data()
{

    function upload_image_from_url($thumbnail_url)
    {

        $image_name = basename($thumbnail_url);
        $image_data = file_get_contents($thumbnail_url);
        $upload_dir = wp_upload_dir();
        $image_path = $upload_dir['path'] . '/' . $image_name;
        if ($image_data === false) {
            return new WP_Error('image_fetch_failed', 'Failed to fetch image data');
        }

        $image_saved = file_put_contents($image_path, $image_data);
        if ($image_saved == FALSE) {
            return new WP_Error('Upload failed', 'Failed to upload file');
        }
        return $image_path;
    }

    function insert_image_to_wp_posts($image_path, $post_id)
    {
        $upload_dir = wp_upload_dir();
        $image_url = str_replace($upload_dir['path'], $upload_dir['url'], $image_path);
        $file_info = array(
            'guid' => $image_url,
            'post_mime_type' => mime_content_type($image_path),
            'post_title' => sanitize_file_name(pathinfo($image_path, PATHINFO_FILENAME)),
            'post_status' => 'inherit',
            'post_parent' => $post_id,
        );
        $attachment_id = wp_insert_attachment($file_info, $image_path, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $image_path);
        wp_update_attachment_metadata($attachment_id, $attachment_metadata);
        return $attachment_id;
    }

    $csvFile = get_template_directory() . '/news_data.csv';


    if (!file_exists($csvFile)) {
        echo 'CSV file not found';
        return;
    }

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        $header = fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== FALSE) {
            $post_title = isset($data[1]) ? $data[1] : '';
            $post_date = isset($data[2]) ? $data[2] : '';
            $category = isset($data[3]) ? $data[3] : '';
            $thumbnail_url = isset($data[4]) ? $data[4] : '';
            $post_data = array(
                'post_title'    => $post_title,
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'news',
                'post_date'     => $post_date,
            );

            if ($post_data):
                $post_id = wp_insert_post($post_data);
                update_post_meta($post_id, 'category', $category);
                if ($thumbnail_url) {
                    $image_path = upload_image_from_url($thumbnail_url);
                    if (!is_wp_error($image_path)) {
                        $attachment_id = insert_image_to_wp_posts($image_path, $post_id);
                        if (!is_wp_error($attachment_id)) {
                            update_post_meta($post_id, "_thumbnail_id", $attachment_id);
                        }
                    } else {
                        echo "Error while inserting image into WP Posts";
                    }
                } else {
                    error_log('thumbnail url not generated');
                }
            endif;
        }
        fclose($handle);
        echo '1'; // Success
    } else {
        echo '0'; // Failure opening the file
    }
}


function wc_new_order_column($columns)
{
    $columns['amount_action'] = 'Amount Action';
    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'wc_new_order_column');




function wc_custom_order_column_content($column, $post_id)
{
    if ($column === 'amount_action') {
        $order = wc_get_order($post_id);
        $refunds = $order->get_refunds();
        $amount = $order->get_total();
        $total_amount = ceil($order->get_total());
        $refunded_amount = 0;
        foreach ($refunds as $refund) {
            $refunded_amount += abs($refund->get_amount());
        }

        if ($refunded_amount != $total_amount) {
            echo '<button type="button" class="btn btn-primary"   data-toggle="modal" data-target="#refundModal_' . $post_id . '">
                    Refund
            </button>';

            include CUSTOM_SETTING_PLUGIN_PATH . '/html/order_refund_modal.php';
        } else {
            echo '<button type="button" class="btn btn-secondary   disabled">
                   Refunded
            </button>';
        }
    }
}
add_action('manage_shop_order_posts_custom_column', 'wc_custom_order_column_content', 10, 2);


function enqueue_bootstrap_files()
{
    wp_enqueue_style('style_css', CUSTOM_SETTING_PLUGIN_URL . '/css/styles.css');
    if (@$_GET['post_type'] == 'shop_order') {

        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');


        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_bootstrap_files');


function wc_refund_button_script()
{
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.refund-button').on('click', function() {
                var orderId = $(this).data('order-id');
                $.ajax({
                    url: 'admin-ajax.php',
                    method: 'POST',
                    data: {
                        action: 'process_stripe_refund',
                        order_id: orderId
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            alert(response.data.message);
                            window.location.href = 'http://localhost/wp/wp-admin/edit.php?post_type=shop_order';
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error);
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>

<?php
}
add_action('admin_footer', 'wc_refund_button_script');


function process_stripe_refund()
{

    if (!isset($_POST['order_id'])) {
        wp_send_json_error(['message' => 'Order ID missing.']);
    }

    $order_id = absint($_POST['order_id']);
    $order = wc_get_order($order_id);

    if (!$order) {
        wp_send_json_error(['message' => 'Order not found.']);
    }

    $charge_id = $order->get_transaction_id();

    if (!$charge_id) {
        wp_send_json_error(['message' => 'Stripe charge ID not found.']);
    }




    \Stripe\Stripe::setApiKey('sk_test_51QIPVsRq3Pbq5XLIKYAjtlCvMtp2VPGvMYFHzwuMFDsV5Acyi5hAvkMorh3oZIDHzGAgasHcVJuGBhXDZAYRMykA00Db32EDMH');

    try {

        $refund = \Stripe\Refund::create([
            'charge' => $charge_id,
        ]);


        $order->update_status('refunded', 'Order refunded via Stripe.');

        wp_send_json_success([
            'message' => 'Refund processed successfully.',
            'Refund' => $refund
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error processing refund: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_process_stripe_refund', 'process_stripe_refund');

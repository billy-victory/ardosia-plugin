<?php
/**
 * Admin functionality for Ardosia Plugin
 */

// Add admin menu item for quote submissions
function pps_quotes_admin_menu() {
    add_menu_page(
        'Ardosia Quotes', 
        'Ardosia Quotes', 
        'manage_options', 
        'pps-quotes', 
        'pps_quotes_admin_page', 
        'dashicons-calculator', 
        30
    );
}
add_action('admin_menu', 'pps_quotes_admin_menu');

// Admin page content
function pps_quotes_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pps_quotes';
    
    // Handle CSV export if requested
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=ardosia-quotes-export-' . date('Y-m-d') . '.csv');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, array(
            'ID', 
            'Date', 
            'Email', 
            'Postcode', 
            'Paving Type', 
            'Size Option', 
            'Size Detail',
            'Area (m²)', 
            'Price per m²', 
            'Total Cost'
        ));
        
        // Get all quotes
        $quotes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC", ARRAY_A);
        
        // Add data rows
        foreach ($quotes as $quote) {
            fputcsv($output, array(
                $quote['id'],
                $quote['time'],
                $quote['email'],
                $quote['postcode'],
                $quote['paving_type'],
                $quote['size_option'],
                $quote['size_detail'],
                number_format($quote['area'], 2),
                '£' . number_format($quote['price_per_sqm'], 2),
                '£' . number_format($quote['total_cost'], 2)
            ));
        }
        
        fclose($output);
        exit;
    }
    
    // Handle deletion if confirmed
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_quote_' . $_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, array('id' => $id), array('%d'));
        echo '<div class="notice notice-success is-dismissible"><p>Quote #' . $id . ' has been deleted successfully.</p></div>';
    }
    
    // Get quotes with pagination
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset = ($paged - 1) * $per_page;
    
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);
    
    $quotes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY time DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ),
        ARRAY_A
    );
    
    // Display the page content
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Ardosia Quote Submissions</h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=pps-quotes&action=export')); ?>" class="page-title-action">Export to CSV</a>
        
        <hr class="wp-header-end">
        
        <?php if (empty($quotes)): ?>
            <div class="notice notice-info">
                <p>No quote submissions found.</p>
            </div>
        <?php else: ?>
            <div class="tablenav top">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $total_items; ?> items</span>
                    <?php if ($total_pages > 1): ?>
                        <span class="pagination-links">
                            <?php
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total' => $total_pages,
                                'current' => $paged,
                                'type' => 'plain'
                            ));
                            echo $page_links;
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Email</th>
                        <th scope="col">Postcode</th>
                        <th scope="col">Paving Type</th>
                        <th scope="col">Size Option</th>
                        <th scope="col">Area</th>
                        <th scope="col">Total Cost</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td><?php echo esc_html($quote['id']); ?></td>
                            <td><?php echo esc_html(date('Y-m-d H:i', strtotime($quote['time']))); ?></td>
                            <td><?php echo esc_html($quote['email']); ?></td>
                            <td><?php echo esc_html($quote['postcode']); ?></td>
                            <td><?php echo esc_html($quote['paving_type']); ?></td>
                            <td><?php echo esc_html($quote['size_option']); ?></td>
                            <td><?php echo esc_html(number_format($quote['area'], 2)) . ' m²'; ?></td>
                            <td>£<?php echo esc_html(number_format($quote['total_cost'], 2)); ?></td>
                            <td>
                                <a href="#" class="button view-quote-details" data-id="<?php echo esc_attr($quote['id']); ?>">View Details</a>
                                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=pps-quotes&action=delete&id=' . $quote['id']), 'delete_quote_' . $quote['id'])); ?>" class="button delete-quote" onclick="return confirm('Are you sure you want to delete this quote?');">Delete</a>
                            </td>
                        </tr>
                        <tr class="quote-details" id="quote-details-<?php echo esc_attr($quote['id']); ?>" style="display: none;">
                            <td colspan="9">
                                <div class="quote-details-inner">
                                    <h3>Quote #<?php echo esc_html($quote['id']); ?> Details</h3>
                                    <table class="widefat">
                                        <tr>
                                            <th>Paving Type</th>
                                            <td><?php echo esc_html($quote['paving_type']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Size Option</th>
                                            <td><?php echo esc_html($quote['size_option']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Size Detail</th>
                                            <td><?php echo esc_html($quote['size_detail']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Area</th>
                                            <td><?php echo esc_html(number_format($quote['area'], 2)) . ' m²'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Price per m²</th>
                                            <td>£<?php echo esc_html(number_format($quote['price_per_sqm'], 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Cost</th>
                                            <td>£<?php echo esc_html(number_format($quote['total_cost'], 2)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo esc_html($quote['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Postcode</th>
                                            <td><?php echo esc_html($quote['postcode']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Submission Date</th>
                                            <td><?php echo esc_html(date('F j, Y, g:i a', strtotime($quote['time']))); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $total_items; ?> items</span>
                        <span class="pagination-links">
                            <?php echo $page_links; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <style>
        .quote-details-inner {
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            margin: 10px 0;
        }
        .quote-details-inner table {
            margin-top: 10px;
        }
        .quote-details-inner th {
            width: 150px;
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            $('.view-quote-details').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#quote-details-' + id).toggle();
            });
        });
    </script>
    <?php
}
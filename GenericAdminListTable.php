<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class GenericAdminListTable extends WP_List_Table {
    private $data;
    private $columns;
    private $sortable;
    private $per_page;

    public function __construct($args = []) {
        parent::__construct([
            'singular' => $args['singular'] ?? 'item',
            'plural'   => $args['plural'] ?? 'items',
            'ajax'     => false,
        ]);

        $this->data     = $args['data'] ?? [];
        $this->columns  = $args['columns'] ?? [];
        $this->sortable = $args['sortable'] ?? [];
        $this->per_page = $args['per_page'] ?? 10;
    }

    public function get_columns() {
        return $this->columns;
    }

    public function get_sortable_columns() {
        return $this->sortable;
    }

    public function prepare_items() {
        $current_page = $this->get_pagenum();
        $total_items = count($this->data);
        $this->items = array_slice($this->data, ($current_page - 1) * $this->per_page, $this->per_page);
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $this->per_page,
            'total_pages' => ceil($total_items / $this->per_page),
        ]);
    }

    public function column_default($item, $column_name) {
        return $item[$column_name] ?? '';
    }
}


/*


//callback function
function render_pending_restaurants_table() {
    echo '<div class="wrap"><h1>Pending Restaurant Approvals</h1>';

    // Use your custom WPQueryBuilder if already created
    $query = (new WPQueryBuilder())
        ->postType('restaurant')
        ->status('publish')
        ->meta('status', 'pending')
        ->get();

    $rows = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $restaurant_id = get_the_ID();
            $restaurant_title = get_the_title();

            $author_id = get_post_field('post_author', $restaurant_id);
            $user_info = get_userdata($author_id);

            $user_name = $user_info ? $user_info->display_name : 'Unknown';
            $user_email = $user_info ? $user_info->user_email : '-';

            $approve_url = esc_url(admin_url('admin-post.php?action=approve_restaurant&restaurant_id=' . $restaurant_id));
            $action_btn = "<a href='{$approve_url}' target='_blank' class='button button-primary'>Approve</a>";

            $rows[] = [
                'user'        => $user_name,
                'email'       => $user_email,
                'restaurant'  => $restaurant_title,
                'status'      => '<span style="color:orange;">Pending</span>',
                'action'      => $action_btn,
            ];
        }
        wp_reset_postdata();
    }

    $columns = [
        'user'       => 'User',
        'email'      => 'Email',
        'restaurant' => 'Restaurant',
        'status'     => 'Status',
        'action'     => 'Action',
    ];

    $sortable = [
        'user'       => ['user', false],
        'restaurant' => ['restaurant', false],
    ];

    $list_table = new GenericAdminListTable([
        'singular'  => 'restaurant',
        'plural'    => 'restaurants',
        'data'      => $rows,
        'columns'   => $columns,
        'sortable'  => $sortable,
        'per_page'  => 10,
    ]);

    $list_table->prepare_items();

    echo '<form method="post">';
    $list_table->display();
    echo '</form>';
    echo '</div>';
}


*/

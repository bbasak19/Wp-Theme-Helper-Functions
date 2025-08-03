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
new DynamicAdminMenuBuilder([
    [
        'page_title' => 'Restaurant Admin',
        'menu_title' => 'Restaurants',
        'menu_slug'  => 'restaurant-admin',
        'capability' => 'manage_options',
        'callback'   => function () {
            echo '<h1>Welcome to the Restaurant Panel</h1>';
        },
        'icon_url'   => 'dashicons-store',
        'position'   => 25
    ],
    [
        'page_title'  => 'Pending Approvals',
        'menu_title'  => 'Pending Restaurants',
        'menu_slug'   => 'pending-restaurants',
        'parent_slug' => 'restaurant-admin',
        'capability'  => 'manage_options',
        'callback'    => 'render_pending_restaurants_table'
    ]
]);
*/

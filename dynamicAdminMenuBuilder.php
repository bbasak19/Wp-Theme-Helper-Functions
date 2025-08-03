<?php 
class DynamicAdminMenuBuilder {
    private $menu_config;

    public function __construct($menu_config) {
       // Default fallback if not passed
        $default_menu = [
            [
                'page_title'  => 'Default Page',
                'menu_title'  => 'Default Menu',
                'menu_slug'   => 'default-menu',
                'capability'  => 'manage_options',
                'callback'    => [$this, 'default_render'],
                'icon_url'    => 'dashicons-admin-generic',
                'position'    => 100,
                'color'       => '#555',
            ]
        ];

        $this->menu_config = empty($menu_config) ? $default_menu : $menu_config;
        $this->menu_config = $menu_config;
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_head', [$this, 'add_custom_menu_styles']);
    }

    public function default_render() {
        echo '<h2>Welcome to the Default Admin Menu Page</h2>';
    }

    public function register_menus() {
        foreach ($this->menu_config as $menu) {
            $is_submenu = isset($menu['parent_slug']);

            if ($is_submenu) {
                add_submenu_page(
                    $menu['parent_slug'],
                    $menu['page_title'],
                    $menu['menu_title'],
                    $menu['capability'],
                    $menu['menu_slug'],
                    $menu['callback']
                );
            } else {
                add_menu_page(
                    $menu['page_title'],
                    $menu['menu_title'],
                    $menu['capability'],
                    $menu['menu_slug'],
                    $menu['callback'],
                    $menu['icon_url'] ?? 'dashicons-admin-generic',
                    $menu['position'] ?? null
                );
            }
        }
    }

    public function add_custom_menu_styles() {
        foreach ($this->menu_config as $menu) {
            if (isset($menu['menu_slug'], $menu['color'])) {
                echo "<style>
                    #toplevel_page_{$menu['menu_slug']} > a {
                        background-color: {$menu['color']} !important;
                        color: #fff !important;
                    }
                </style>";
            }
        }
    }

    // ✅ 1. Display all current admin menus
    public static function display_all_admin_menus() {
        global $menu, $submenu;

        echo "<h2>Admin Menus</h2><ul>";
        foreach ($menu as $m) {
            echo "<li><strong>{$m[0]}</strong> (slug: {$m[2]})</li>";

            if (isset($submenu[$m[2]])) {
                echo "<ul>";
                foreach ($submenu[$m[2]] as $sm) {
                    echo "<li>→ {$sm[0]} (slug: {$sm[2]})</li>";
                }
                echo "</ul>";
            }
        }
        echo "</ul>";
    }

    // ✅ 2. Hide specific menu for roles
    public static function hide_menu_for_roles($hide_config = []) {
        add_action('admin_menu', function () use ($hide_config) {
            foreach ($hide_config as $item) {
                $role = $item['role'];
                $menu_slug = $item['menu_slug'];
                $submenu_slug = $item['submenu_slug'] ?? null;

                if (current_user_can($role)) {
                    continue; // Allow this role to see the menu
                }

                // Hide top-level
                remove_menu_page($menu_slug);

                // Hide sub-menu
                if ($submenu_slug) {
                    remove_submenu_page($menu_slug, $submenu_slug);
                }
            }
        }, 999);
    }
}



/* Example 
new DynamicAdminMenuBuilder([
    // 🔹 Main Menu
    [
        'page_title' => 'Custom Admin',
        'menu_title' => 'My Admin',
        'menu_slug'  => 'my-admin-menu',
        'capability' => 'manage_options',
        'callback'   => 'custom_render_function',
        'icon_url'   => 'dashicons-admin-site',
        'position'   => 5,
        'color'      => '#0085ba'
    ],

    // 🔹 Submenu
    [
        'page_title'  => 'Pending Items',
        'menu_title'  => 'Pending Approvals',
        'menu_slug'   => 'pending-approvals',
        'parent_slug' => 'my-admin-menu', // This links it to the main menu above
        'capability'  => 'manage_options',
        'callback'    => 'render_pending_items_page',
    ]
]);

function custom_render_function() {
    echo '<h2>Welcome to My Admin Panel</h2>';
    echo '<p>This is the main admin menu content.</p>';
}

function render_pending_items_page() {
    echo '<h2>Pending Approvals</h2>';
    echo '<p>Here you can review and approve pending users or restaurants.</p>';
}
*/

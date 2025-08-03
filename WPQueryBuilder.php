<?php 
class WPQueryBuilder {
    private $args = [];

    public function postType($type) {
        $this->args['post_type'] = $type;
        return $this;
    }

    public function status($status) {
        $this->args['post_status'] = $status;
        return $this;
    }

    public function limit($number) {
        $this->args['posts_per_page'] = $number;
        return $this;
    }

    public function author($author_id) {
        $this->args['author'] = $author_id;
        return $this;
    }

    public function order($orderby = 'date', $order = 'DESC') {
        $this->args['orderby'] = $orderby;
        $this->args['order'] = $order;
        return $this;
    }

    public function meta($key, $value, $compare = '=', $type = 'CHAR') {
        $this->args['meta_query'][] = [
            'key'     => $key,
            'value'   => $value,
            'compare' => $compare,
            'type'    => $type
        ];
        return $this;
    }

    public function nestedMeta(array $meta_query, $relation = 'AND') {
        $this->args['meta_query'][] = array_merge(['relation' => $relation], $meta_query);
        return $this;
    }

    public function tax($taxonomy, $terms, $field = 'slug') {
        $this->args['tax_query'][] = [
            'taxonomy' => $taxonomy,
            'field'    => $field,
            'terms'    => (array) $terms,
        ];
        return $this;
    }

    public function buildArgs() {
        return $this->args;
    }

    public function get() {
        return new WP_Query($this->args);
    }
}


/*
Example Usage
$query = (new WPQueryBuilder())
    ->postType('restaurant')
    ->status('publish')
    ->meta('status', 'role_checked')
    ->limit(10)
    ->get();

 // Nested Meta Query Example (e.g., ACF with OR)
    $query = (new WPQueryBuilder())
    ->postType('restaurant')
    ->nestedMeta([
        [
            'key'     => 'approval',
            'value'   => 'pending',
            'compare' => '='
        ],
        [
            'key'     => 'featured',
            'value'   => 'yes',
            'compare' => '='
        ]
    ], 'OR')
    ->limit(5)
    ->get();

    // Taxonomy + Meta Combo
    $query = (new WPQueryBuilder())
    ->postType('restaurant')
    ->status('publish')
    ->tax('restaurant_category', 'fast-food')
    ->meta('status', 'approved')
    ->get();
*/

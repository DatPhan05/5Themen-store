<?php
$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/include/database.php';
require_once $rootPath . '/include/helpers.php';

class Post {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function insert_post($title, $slug, $thumbnail, $summary, $content, $category) {
        ...
    }

    public function get_all_posts() {
        ...
    }

    public function get_post($id) {
        ...
    }

    public function get_posts_by_category($category, $limit = 4) {
        ...
    }

    public function get_all_posts_by_category($category) {
        ...
    }

    public function update_post(...) {
        ...
    }

    public function delete_post($id) {
        ...
    }
}

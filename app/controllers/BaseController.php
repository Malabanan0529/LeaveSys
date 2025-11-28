<?php
class BaseController {
    protected $f3;
    protected $db;

    function __construct() {
        $this->f3 = Base::instance();
        $this->db = $this->f3->get('DB');
    }

    function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    function log($userId, $text) {
        $this->db->exec("INSERT INTO activity_logs (user_id, action_text) VALUES (?, ?)", [$userId, $text]);
    }
}
?>
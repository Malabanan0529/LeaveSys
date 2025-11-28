<?php
class MainController extends BaseController {

    function __construct() {
        parent::__construct();
        
        new \DB\SQL\Session($this->db, 'sessions', true);
    }

    function login($f3) {
        if ($f3->get('SESSION.user_id')) $f3->reroute('/dashboard');
        echo Template::instance()->render('login.html');
    }

    function register($f3) {
        if ($f3->get('SESSION.user_id')) $f3->reroute('/dashboard');
        echo Template::instance()->render('register.html');
    }

    function dashboard($f3) {
        $uid = $f3->get('SESSION.user_id');

        if (!$uid) {
            $f3->reroute('/');
            return;
        }

        $role = $f3->get('SESSION.role');

        if ($role === 'employee') {
            $balance = $this->db->exec("SELECT vacation_balance, sick_balance FROM users WHERE id = ?", [$uid]);
            $f3->set('balance', $balance[0] ?? ['vacation_balance'=>0, 'sick_balance'=>0]);
            
            $history = $this->db->exec("SELECT * FROM leave_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5", [$uid]);
            $f3->set('myReqs', $history);
        } else {
            $f3->set('totalReq', $this->db->exec("SELECT COUNT(*) as c FROM leave_requests")[0]['c']);
            $f3->set('pendingReq', $this->db->exec("SELECT COUNT(*) as c FROM leave_requests WHERE status='Pending'")[0]['c']);
            $f3->set('approvedReq', $this->db->exec("SELECT COUNT(*) as c FROM leave_requests WHERE status='Approved'")[0]['c']);
            $f3->set('rejectedReq', $this->db->exec("SELECT COUNT(*) as c FROM leave_requests WHERE status='Rejected'")[0]['c']);
            
            $pendingList = $this->db->exec("SELECT lr.*, u.full_name FROM leave_requests lr JOIN users u ON lr.user_id = u.id WHERE status='Pending' LIMIT 5");
            $f3->set('pendingList', $pendingList);
        }

        echo Template::instance()->render('dashboard.html');
    }

    function report($f3) {
        if ($f3->get('SESSION.role') === 'employee') die("Access Denied");
        
        $data = $this->db->exec("SELECT lr.*, u.full_name FROM leave_requests lr JOIN users u ON lr.user_id = u.id ORDER BY created_at DESC");
        $f3->set('reportData', $data);
        
        echo Template::instance()->render('report.html');
    }

    function showNotFound($f3) {
        while (ob_get_level()) ob_end_clean();
        
        $error_code = $f3->get('ERROR.code');
        $error_text = $f3->get('ERROR.text');
        $f3->set('error_message', $error_text);

        if ($error_code == 404) {
            $f3->set('page_title', '404 - Page Not Found');
            echo Template::instance()->render('not_found.html');
        } else {
            $f3->set('page_title', "Error $error_code");
            echo Template::instance()->render('sys_error.html');
        }
    }
}
?>
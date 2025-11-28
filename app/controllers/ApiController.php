<?php
class ApiController extends BaseController {

    function __construct() {
        parent::__construct();
        new \DB\SQL\Session($this->db, 'sessions', true);
    }

    function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    function auth($f3) {
        $username = $f3->get('POST.username');
        $password = $f3->get('POST.password');

        $user = $this->db->exec("SELECT id, full_name, password, role FROM users WHERE username = ?", [$username]);
        
        if ($user && $user[0]['password'] === $password) {
            $f3->set('SESSION.user_id', $user[0]['id']);
            $f3->set('SESSION.full_name', $user[0]['full_name']);
            $f3->set('SESSION.role', $user[0]['role']);
            
            $this->log($user[0]['id'], "User logged in");
            $this->json(['status' => 'success']);
        } else {
            $this->json(['status' => 'error', 'message' => 'Invalid credentials']);
        }
    }

    function register($f3) {
        $user = $f3->get('POST.username');
        $exists = $this->db->exec("SELECT id FROM users WHERE username = ?", [$user]);
        
        if ($exists) {
            $this->json(['status' => 'error', 'message' => 'Username exists']);
        }

        $this->db->exec(
            "INSERT INTO users (full_name, username, password, role, vacation_balance, sick_balance) VALUES (?, ?, ?, 'employee', 15, 10)",
            [$f3->get('POST.full_name'), $user, $f3->get('POST.password')]
        );
        
        $this->json(['status' => 'success']);
    }

    function submitLeave($f3) {
        if (!$f3->get('SESSION.user_id')) return;

        $uid = $f3->get('SESSION.user_id');
        $type = $f3->get('POST.leave_type');
        $start = $f3->get('POST.start_date');
        $end = $f3->get('POST.end_date');
        $reason = $f3->get('POST.reason');

        $days = round((strtotime($end) - strtotime($start)) / 86400) + 1;
        if ($days <= 0) $this->json(['status' => 'error', 'message' => 'Invalid dates']);

        if ($type !== 'Emergency') {
            $blocked = $this->db->exec("SELECT title FROM calendar_events WHERE start_date <= ? AND end_date >= ?", [$end, $start]);
            if ($blocked) $this->json(['status' => 'error', 'message' => "Blocked: " . $blocked[0]['title']]);
        }

        if ($type !== 'Emergency') {
            $col = ($type === 'Vacation') ? 'vacation_balance' : 'sick_balance';
            $bal = $this->db->exec("SELECT $col FROM users WHERE id = ?", [$uid])[0][$col];
            
            if ($bal < $days) $this->json(['status' => 'error', 'message' => "Insufficient $type balance."]);
            
            $this->db->exec("UPDATE users SET $col = $col - ? WHERE id = ?", [$days, $uid]);
        }

        $this->db->exec(
            "INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, days_count, reason) VALUES (?, ?, ?, ?, ?, ?)",
            [$uid, $type, $start, $end, $days, $reason]
        );
        
        $this->log($uid, "Applied for $days days ($type)");
        $this->json(['status' => 'success']);
    }

    function updateStatus($f3) {
        if ($f3->get('SESSION.role') === 'employee') return;
        
        $id = $f3->get('POST.request_id');
        $status = $f3->get('POST.status');
        
        if ($status === 'Rejected') {
            $req = $this->db->exec("SELECT * FROM leave_requests WHERE id = ?", [$id]);
            if ($req) {
                $r = $req[0];
                if ($r['leave_type'] !== 'Emergency') {
                    $col = ($r['leave_type'] === 'Vacation') ? 'vacation_balance' : 'sick_balance';
                    $this->db->exec("UPDATE users SET $col = $col + ? WHERE id = ?", [$r['days_count'], $r['user_id']]);
                }
            }
        }

        $this->db->exec("UPDATE leave_requests SET status = ? WHERE id = ?", [$status, $id]);
        $this->log($f3->get('SESSION.user_id'), "$status request #$id");
        $this->json(['status' => 'success']);
    }

    function getCalendar($f3) {
        $sql = "SELECT lr.*, u.full_name FROM leave_requests lr JOIN users u ON lr.user_id = u.id WHERE status='Approved'";
        if ($f3->get('SESSION.role') === 'employee') {
            $sql .= " AND lr.user_id = " . $f3->get('SESSION.user_id');
        }
        
        $leaves = $this->db->exec($sql);
        $events = $this->db->exec("SELECT * FROM calendar_events");

        $data = [];
        foreach($leaves as $r) {
            $data[] = [
                'title' => $r['full_name'] . " (" . $r['leave_type'] . ")",
                'start' => $r['start_date'],
                'end' => date('Y-m-d', strtotime($r['end_date'] . ' +1 day')),
                'color' => ($r['leave_type'] === 'Vacation') ? '#d53f8c' : '#069469'
            ];
        }
        foreach($events as $r) {
            $data[] = [
                'title' => "⛔ " . $r['title'],
                'start' => $r['start_date'],
                'end' => date('Y-m-d', strtotime($r['end_date'] . ' +1 day')),
                'color' => $r['color']
            ];
        }
        $this->json($data);
    }

    function addEvent($f3) {
        if ($f3->get('SESSION.role') === 'employee') return;
        $this->db->exec("INSERT INTO calendar_events (title, start_date, end_date, color, created_by) VALUES (?, ?, ?, ?, ?)",
            [$f3->get('POST.title'), $f3->get('POST.start_date'), $f3->get('POST.end_date'), $f3->get('POST.color'), $f3->get('SESSION.user_id')]);
        $this->json(['status' => 'success']);
    }

    function getCharts($f3) {
        $types = $this->db->exec("SELECT leave_type, COUNT(*) as c FROM leave_requests GROUP BY leave_type");
        $months = $this->db->exec("SELECT MONTH(start_date) as m, COUNT(*) as c FROM leave_requests WHERE YEAR(start_date) = YEAR(CURDATE()) GROUP BY m");
        
        $tData = ['Vacation'=>0, 'Sick'=>0, 'Emergency'=>0];
        foreach($types as $r) $tData[$r['leave_type']] = $r['c'];
        $mData = array_fill(1, 12, 0);
        foreach($months as $r) $mData[$r['m']] = $r['c'];

        $this->json(['doughnut' => array_values($tData), 'line' => array_values($mData)]);
    }

    function getLogs($f3) {
        $this->json($this->db->exec("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY created_at DESC LIMIT 10"));
    }

    function getProfile($f3) {
        $uid = $f3->get('SESSION.user_id');
        if (!$uid) return;
        $user = $this->db->exec("SELECT id, full_name, username, role, vacation_balance, sick_balance FROM users WHERE id = ?", [$uid]);
        if ($user) $this->json($user[0]);
        else $this->json(['status' => 'error', 'message' => 'User not found']);
    }

    function logout($f3) {
        $f3->clear('SESSION');
        $this->json(['status' => 'success']);
    }

    function getUsers($f3) {
        if ($f3->get('SESSION.role') !== 'admin') return;

        $users = $this->db->exec("SELECT id, full_name, username, role, vacation_balance, sick_balance FROM users ORDER BY id DESC");
        $this->json($users);
    }

    function addUser($f3) {
        if ($f3->get('SESSION.role') !== 'admin') return;
        
        $user = $f3->get('POST.username');
        if($this->db->exec("SELECT id FROM users WHERE username=?", [$user])) {
            $this->json(['status'=>'error', 'message'=>'Username exists']);
            return;
        }
        
        $vacation = $f3->get('POST.vacation_balance') ?: 15;
        $sick = $f3->get('POST.sick_balance') ?: 10;
        
        $this->db->exec("INSERT INTO users (full_name, username, password, role, vacation_balance, sick_balance) VALUES (?, ?, ?, ?, ?, ?)",
            [$f3->get('POST.full_name'), $user, $f3->get('POST.password'), $f3->get('POST.role'), $vacation, $sick]);
            
        $this->log($f3->get('SESSION.user_id'), "Created user: $user");
        $this->json(['status'=>'success']);
    }

    function updateUser($f3) {
        if ($f3->get('SESSION.role') !== 'admin') return;
        
        $id = $f3->get('POST.id');
        $fullName = $f3->get('POST.full_name');
        $username = $f3->get('POST.username');
        $role = $f3->get('POST.role');
        $password = $f3->get('POST.password');
        
        $vacation = $f3->get('POST.vacation_balance');
        $sick = $f3->get('POST.sick_balance');

        $exists = $this->db->exec("SELECT id FROM users WHERE username=? AND id!=?", [$username, $id]);
        if($exists) {
            $this->json(['status'=>'error', 'message'=>'Username already taken']);
            return;
        }

        if (!empty($password)) {
            $this->db->exec("UPDATE users SET full_name=?, username=?, role=?, password=?, vacation_balance=?, sick_balance=? WHERE id=?", 
                [$fullName, $username, $role, $password, $vacation, $sick, $id]);
        } else {
            $this->db->exec("UPDATE users SET full_name=?, username=?, role=?, vacation_balance=?, sick_balance=? WHERE id=?", 
                [$fullName, $username, $role, $vacation, $sick, $id]);
        }

        $this->log($f3->get('SESSION.user_id'), "Updated user ID: $id");
        $this->json(['status' => 'success']);
    }

    function deleteUser($f3) {
        if ($f3->get('SESSION.role') !== 'admin') return;
        
        $id = $f3->get('POST.id');
        
        if ($id == $f3->get('SESSION.user_id')) {
            $this->json(['status' => 'error', 'message' => 'Cannot delete your own account']);
            return;
        }

        $this->db->exec("DELETE FROM leave_requests WHERE user_id=?", [$id]);
        $this->db->exec("DELETE FROM activity_logs WHERE user_id=?", [$id]);
        $this->db->exec("DELETE FROM users WHERE id=?", [$id]);
        
        $this->log($f3->get('SESSION.user_id'), "Deleted user ID: $id");
        $this->json(['status' => 'success']);
    }
}
?>
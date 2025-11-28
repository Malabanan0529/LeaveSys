<?php
$f3 = require('lib/base.php');
$f3->config('app/config/config.ini');

$f3->set('DB', new DB\SQL(
    $f3->get('DB_DNS'),
    $f3->get('DB_USER'),
    $f3->get('DB_PASS')
));

new Session();

$f3->route('GET /', 'MainController->login');
$f3->route('GET /login', 'MainController->login');
$f3->route('GET /register', 'MainController->register');
$f3->route('GET /dashboard', 'MainController->dashboard');
$f3->route('GET /report', 'MainController->report');

$f3->route('POST /api/auth', 'ApiController->auth');           // login
$f3->route('POST /api/register', 'ApiController->register');   // register
$f3->route('POST /api/logout', 'ApiController->logout');       // logout
$f3->route('POST /api/leave/submit', 'ApiController->submitLeave'); // submit_leave
$f3->route('POST /api/leave/status', 'ApiController->updateStatus'); // update_status
$f3->route('POST /api/event/add', 'ApiController->addEvent');  // add_event

$f3->route('GET /api/users', 'ApiController->getUsers');       // Fetch list
$f3->route('POST /api/user/add', 'ApiController->addUser');    // Add user
$f3->route('POST /api/user/update', 'ApiController->updateUser'); // Edit user
$f3->route('POST /api/user/delete', 'ApiController->deleteUser'); // Delete user
$f3->route('GET /api/profile', 'ApiController->getProfile');   // Fetch current user details

$f3->route('GET /api/calendar', 'ApiController->getCalendar'); // action=get_calendar
$f3->route('GET /api/charts', 'ApiController->getCharts');     // action=get_chart_data
$f3->route('GET /api/logs', 'ApiController->getLogs');         // action=get_logs

$f3->set('ONERROR', 'MainController->showNotFound');

$f3->run();
?>
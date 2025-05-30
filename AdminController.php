<?php
require_once '../config/Database.php';
require_once '../models/Admin.php';

session_start();

$db = new Database();
$admin = new Admin($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../views/admin/login.php");
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validate input
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Username and password are required";
            header("Location: ../views/admin/login.php");
            exit;
        }

        try {$adminData = $admin->authenticate($username, $password);
              if ($adminData) {
                // Start a new session for security
                session_regenerate_id(true);
                
                // Configure session cookie to expire when browser closes
                ini_set('session.cookie_lifetime', '0');
                ini_set('session.use_strict_mode', '1');
                ini_set('session.cookie_secure', '1');
                ini_set('session.use_only_cookies', '1');
                
                $_SESSION['admin'] = $adminData;
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_login_time'] = time();
                ini_set('session.cookie_lifetime', '0');
                
                header("Location: ../views/admin/dashboard.php");
                exit;
            } else {
                $_SESSION['login_error'] = "Invalid username or password";
                header("Location: ../views/admin/login.php");
                exit;
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['login_error'] = "An error occurred during login. Please try again.";
            header("Location: ../views/admin/login.php");
            exit;
        }
        break;

    case 'logout':
        unset($_SESSION['admin']);
        session_destroy();
        header("Location: ../views/admin/login.php");
        exit;
        break;

    default:
        header("Location: ../views/admin/login.php");
        exit;
        break;
}

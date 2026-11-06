<?php
session_start();
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin/dashboard.php");
        exit;
    } else {
        $error = "Access Denied: Invalid Authentication.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Portal - Bethany</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-box { 
            background: var(--glass); 
            backdrop-filter: blur(20px); 
            padding: 50px; 
            border-radius: 30px; 
            border: 1px solid var(--glass-border); 
            width: 350px; 
            text-align: center;
        }
        input { 
            width: 100%; padding: 15px; margin: 10px 0; 
            background: rgba(0,0,0,0.3); border: 1px solid #333; 
            color: #fff; border-radius: 10px; box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="color: var(--accent-gold);">Bethany Admin</h2>
        <?php if($error): ?><p style="color: #ff4d4d;"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-premium" style="width: 100%; border: none; cursor: pointer; margin-top: 20px;">AUTHORIZE</button>
        </form>
        <a href="index.php" style="color: #555; text-decoration: none; font-size: 0.8rem; display: block; margin-top: 20px;">Return Home</a>
    </div>
</body>
</html>
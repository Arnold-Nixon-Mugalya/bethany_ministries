<?php
session_start();
require '../db.php';

// Security: Prevent unauthorized access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Logic: Handle Post Deletion
if (isset($_GET['delete_post'])) {
    $id = $_GET['delete_post'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Record successfully purged from database.";
}

// Logic: Handle New Post
if (isset($_POST['add_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileName = time() . "_" . $_FILES["image"]["name"];
        move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/" . $fileName);
        $imagePath = $fileName;
    }

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $imagePath]);
    $message = "Content broadcasted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bethany Admin | Command Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ADMIN SPECIFIC OVERRIDES */
        .admin-header {
            padding: 40px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 40px;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 900px) { .admin-grid { grid-template-columns: 1fr; } }

        .form-section {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 30px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .form-section h3 {
            margin-top: 0;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        input, textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            background: rgba(0,0,0,0.5);
            border: 1px solid #333;
            border-radius: 12px;
            color: white;
            font-family: inherit;
        }

        input:focus { border-color: var(--accent-gold); outline: none; }

        .management-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .manage-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid transparent;
        }

        .manage-item:hover { border-color: #444; }

        .btn-delete {
            color: #ff4d4d;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .toast {
            background: var(--accent-gold);
            color: black;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-weight: bold;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="container">
        <header class="admin-header">
            <div>
                <h1 style="font-size: 1.5rem; margin: 0;">CONTROL <span style="color: var(--accent-gold);">TERMINAL</span></h1>
                <p style="margin:0; font-size: 0.8rem; opacity: 0.6;">BETHANY MINISTRIES INTERNATIONAL</p>
            </div>
            <a href="../logout.php" class="btn-premium" style="padding: 10px 25px; font-size: 0.8rem;">SECURE LOGOUT</a>
        </header>

        <?php if($message): ?>
            <div class="toast"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            
            <div class="form-section">
                <h3>Broadcast New Content</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Sermon or News Title" required>
                    <textarea name="content" rows="6" placeholder="Spiritual insights or update details..." required></textarea>
                    <label style="display:block; margin-bottom: 10px; font-size: 0.8rem; color: #888;">Cover Media (Image):</label>
                    <input type="file" name="image" style="background: none; border: none; padding-left: 0;">
                    <button type="submit" name="add_post" class="btn-premium" style="width: 100%; border: none; cursor: pointer;">PUBLISH TO GLOBAL INDEX</button>
                </form>
            </div>

            <div class="form-section">
                <h3>Current Database Records</h3>
                <div class="management-list">
                    <?php
                    $posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
                    if(count($posts) > 0):
                        foreach($posts as $post):
                    ?>
                        <div class="manage-item">
                            <div>
                                <div style="font-weight: bold;"><?php echo htmlspecialchars($post['title']); ?></div>
                                <div style="font-size: 0.7rem; color: #666;"><?php echo $post['created_at']; ?></div>
                            </div>
                            <a href="?delete_post=<?php echo $post['id']; ?>" class="btn-delete" onclick="return confirm('Archive this record permanently?')">Delete</a>
                        </div>
                    <?php 
                        endforeach; 
                    else:
                        echo "<p style='color:#555;'>No records found in index.</p>";
                    endif;
                    ?>
                </div>
            </div>

            <div class="form-section">
                <h3>Event Chronology</h3>
                <form method="POST" action="dashboard.php">
                    <input type="text" name="event_title" placeholder="Event Name" required>
                    <input type="datetime-local" name="event_date" required style="color-scheme: dark;">
                    <button type="submit" name="add_event" class="btn-premium" style="width: 100%; border: none; cursor: pointer; background: #333; color: white;">SET COUNTDOWN</button>
                </form>
            </div>

            <div class="form-section" style="background: rgba(212, 175, 55, 0.05);">
                <h3>System Security</h3>
                <p style="font-size: 0.8rem; color: #888; margin-bottom: 20px;">Update terminal access credentials.</p>
                <form method="POST">
                    <input type="password" name="new_password" placeholder="New Terminal Password">
                    <button type="submit" name="change_pass" class="btn-premium" style="width: 100%; border: none; cursor: pointer; background: #fff; color: #000;">ENCRYPT & UPDATE</button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
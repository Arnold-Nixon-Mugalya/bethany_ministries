<?php
require 'db.php';
// Stats and Counter logic
$pdo->query("UPDATE site_stats SET visitors = visitors + 1 WHERE id = 1");
$stats = $pdo->query("SELECT visitors FROM site_stats WHERE id = 1")->fetch();
$visitorCount = $stats['visitors'];

$stmt = $pdo->query("SELECT * FROM events WHERE event_date > NOW() ORDER BY event_date ASC LIMIT 1");
$nextEvent = $stmt->fetch();

$postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BETHANY MINISTRIES INTERNATIONAL</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <section class="hero">
        <p style="letter-spacing: 5px; color: var(--accent-gold); margin-bottom: 0;">WELCOME TO</p>
        <h1>BETHANY</h1>
        <p>RAISING A GENERATION THAT IS GOING TO REVEAL JESUS TO THE WORLD.</p>
        <br>
        <a href="#posts" class="btn-premium">EXPLORE MINISTRIES</a>
    </section>

    <div class="container">
        <div class="stats-grid">
            <div class="glass-card">
                <h2><?php echo number_format($visitorCount); ?></h2>
                <span>Global Visitors</span>
            </div>
            <div class="glass-card">
                <h2><?php echo $postCount; ?></h2>
                <span>Resources Available</span>
            </div>
            <div class="glass-card">
                <h2 style="font-size: 2.5rem; margin-top: 15px;">24/7</h2>
                <span>Ministry Active</span>
            </div>
        </div>

        <div class="countdown-section">
            <?php if ($nextEvent): ?>
                <p style="text-transform: uppercase; letter-spacing: 3px; color: var(--accent-gold);">Next Major Event</p>
                <h3 style="font-size: 2rem; margin: 10px 0;"><?php echo htmlspecialchars($nextEvent['title']); ?></h3>
                <div id="timer">00:00:00:00</div>
                <input type="hidden" id="event-date" value="<?php echo $nextEvent['event_date']; ?>">
            <?php else: ?>
                <h3 style="opacity: 0.5;">NO UPCOMING EVENTS SCHEDULED</h3>
                <div id="timer">00:00:00:00</div>
            <?php endif; ?>
        </div>

        <h2 id="posts" class="posts-title">Recent Revelations</h2>
        <div class="stats-grid" style="margin-top: 0;">
            <?php foreach ($posts as $post): ?>
                <div class="premium-post-card">
                    <?php if($post['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" class="post-img">
                    <?php else: ?>
                        <div style="height:250px; background: #1a1a1a;"></div>
                    <?php endif; ?>
                    <div class="post-info">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p style="color: #888; font-size: 0.9rem;">
                            <?php echo substr(htmlspecialchars($post['content']), 0, 120); ?>...
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bethany Ministries International</p>
        <a href="login.php" class="admin-trigger">Access Terminal</a>
    </footer>

    <script>
        // Countdown Logic
        const eventInput = document.getElementById('event-date');
        const timerDisplay = document.getElementById('timer');

        if (eventInput) {
            const eventDate = new Date(eventInput.value).getTime();
            setInterval(() => {
                const now = new Date().getTime();
                const diff = eventDate - now;
                if (diff > 0) {
                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);
                    timerDisplay.innerHTML = `${d}d ${h}h ${m}m ${s}s`;
                }
            }, 1000);
        }
    </script>
</body>
</html>
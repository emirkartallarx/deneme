<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$messagesFile = "messages.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);
    $data = file_exists($messagesFile) ? json_decode(file_get_contents($messagesFile), true) : [];
    $data[] = ["user" => $username, "message" => $message, "time" => date("H:i:s d-m-Y")];
    file_put_contents($messagesFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: chat.php");
    exit;
}

if ($_GET['action'] ?? '' === 'delete' && $role === 'admin' && isset($_GET['id'])) {
    $data = json_decode(file_get_contents($messagesFile), true);
    unset($data[$_GET['id']]);
    $data = array_values($data); // yeniden indeksle
    file_put_contents($messagesFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: chat.php");
    exit;
}

$messages = file_exists($messagesFile) ? json_decode(file_get_contents($messagesFile), true) : [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Hoşgeldin, <?php echo htmlspecialchars($username); ?></h2>
    <a class="logout" href="logout.php">Çıkış</a>
    <div class="chat-box">
        <?php foreach ($messages as $index => $msg): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($msg['user']); ?>:</strong>
                <?php echo htmlspecialchars($msg['message']); ?>
                <span class="time"><?php echo $msg['time']; ?></span>
                <?php if ($role === 'admin'): ?>
                    <a class="delete" href="?action=delete&id=<?php echo $index; ?>">[sil]</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" class="chat-form">
        <input type="text" name="message" placeholder="Mesaj yaz..." required>
        <button type="submit">Gönder</button>
    </form>
</div>

<script>
setTimeout(() => {
    location.reload();
}, 5000);
</script>
</body>
</html>


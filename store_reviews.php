<?php
session_start();
require_once 'db_connected.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submit'])) {
    $userName = trim($_POST['user_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($userName !== '' && $rating >= 1 && $rating <= 5 && $comment !== '') {
        $stmt = $conn->prepare("INSERT INTO store_reviews (user_name, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $userName, $rating, $comment);
        $stmt->execute();
        $stmt->close();
        header("Location: store_reviews.php");
        exit;
    }
}

$query = "SELECT * FROM store_reviews ORDER BY id DESC";
$result = $conn->query($query);
if (!$result) {
    die("Ошибка при получении отзывов: " . $conn->error);
}
$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Отзывы - Девичья феерия</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background: #fff0f6;
            color: #660033;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header, footer {
            background-color: #d81b60;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
        }
        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 0 15px;
        }
        h1 {
            color: #d81b60;
            font-family: 'Brush Script MT', cursive;
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 20px;
        }
        .review {
            background: white;
            border: 1px solid #d81b60;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(216, 27, 96, 0.3);
        }
        .review strong {
            font-size: 1.1em;
            color: #d81b60;
        }
        .review .rating {
            color: #ad1457;
            font-weight: bold;
            margin-left: 10px;
        }
        .review p {
            margin: 10px 0;
            white-space: pre-wrap;
        }
        .review small {
            color: #880e4f;
        }
        form {
            background: white;
            border: 1px solid #d81b60;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(216, 27, 96, 0.3);
        }
        form h2 {
            color: #d81b60;
            font-family: 'Brush Script MT', cursive;
            font-size: 1.8em;
            margin-bottom: 15px;
            text-align: center;
        }
        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        form input[type="text"],
        form select,
        form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #d81b60;
            border-radius: 5px;
            font-size: 1em;
            color: #660033;
        }
        form textarea {
            resize: vertical;
        }
        form button {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #ad1457;
        }
        .back-link {
            display: block;
            margin: 20px auto;
            text-align: center;
            font-weight: bold;
            color: #d81b60;
            text-decoration: none;
            font-size: 1.1em;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>Девичья феерия - Отзывы</header>
    <main>
        <h1>Отзывы о магазине</h1>
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <strong><?php echo htmlspecialchars($review['user_name']); ?></strong>
                    <div class="rating" title="Рейтинг: <?php echo $review['rating']; ?>/5">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $review['rating']): ?>
                                <span style="color: #d81b60;">&#9733;</span>
                            <?php else: ?>
                                <span style="color: #ccc;">&#9733;</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                    <small><?php echo $review['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Пока нет отзывов.</p>
        <?php endif; ?>

        <form method="post" action="store_reviews.php">
            <h2>Оставить отзыв о магазине</h2>
            <label for="user_name">Имя:</label>
            <input type="text" id="user_name" name="user_name" required />
            <label for="rating">Рейтинг (1-5):</label>
            <select id="rating" name="rating" required>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
            <label for="comment">Отзыв:</label>
            <textarea id="comment" name="comment" rows="4" required></textarea>
            <button type="submit" name="review_submit">Отправить отзыв</button>
        </form>
        <a href="beautiful_shop.html" class="back-link">← Вернуться на главную</a>
    </main>
    <footer>© 2025 Девичья феерия. Все права защищены.</footer>
</body>
</html>

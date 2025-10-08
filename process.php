<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат публикации</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body text-center">
            <?php
            require_once 'config.php';

            $ad_title = trim($_POST['ad_title'] ?? '');
            $ad_category = trim($_POST['ad_category'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $contact_email = trim($_POST['contact_email'] ?? '');
            $ad_text = trim($_POST['ad_text'] ?? '');

            $errors = [];

            if ($ad_title === '') {
                $errors[] = "Заголовок объявления обязателен.";
            }
            if ($ad_category === '') {
                $errors[] = "Категория обязательна.";
            }
            if ($contact_email === '') {
                $errors[] = "Email обязателен.";
            }
            if ($price === '' || $price === null) {
                $errors[] = "Цена обязательна.";
            }

            if (mb_strlen($ad_title, 'UTF-8') > 40) {
                $errors[] = "Заголовок не может быть длиннее 40 символов.";
            }
            if (mb_strlen($ad_category, 'UTF-8') > 40) {
                $errors[] = "Категория не может быть длиннее 40 символов.";
            }
            if (mb_strlen($contact_email, 'UTF-8') > 30) {
                $errors[] = "Email не может быть длиннее 30 символов.";
            }
            if (mb_strlen($ad_text, 'UTF-8') > 1000) {
                $errors[] = "Текст объявления не может быть длиннее 1000 символов.";
            }

            if ($contact_email !== '' && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Указан некорректный email.";
            }

            if ($price !== '' && $price !== null) {
                if (!is_numeric($price)) {
                    $errors[] = "Цена должна быть числом.";
                } else {
                    $price = (float)$price;
                    if ($price < 0) {
                        $errors[] = "Цена не может быть отрицательной.";
                    }
                    if ($price > 999999.99) {
                        $errors[] = "Цена не может быть больше 999999.99.";
                    }
                }
            }

            if (!empty($errors)) {
                echo '<div class="alert alert-danger">';
                echo '<h4>Ошибки при заполнении формы:</h4>';
                echo '<ul class="text-start">';
                foreach ($errors as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo '</ul>';
                echo '<a href="form.html" class="btn btn-primary">Вернуться к форме</a>';
                echo '</div>';
                exit;
            }

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO ads (ad_title, ad_category, price, contact_email, ad_text)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$ad_title, $ad_category, $price, $contact_email, $ad_text]);

                echo '<div class="alert alert-success">';
                echo '<h4>Объявление успешно опубликовано!</h4>';
                echo '<p><strong>Заголовок:</strong> ' . htmlspecialchars($ad_title) . '</p>';
                echo '<p><strong>Категория:</strong> ' . htmlspecialchars($ad_category) . '</p>';
                echo '<p><strong>Цена:</strong> ' . number_format((float)$price, 2, ',', ' ') . ' руб.</p>';
                echo '<p><strong>Контактный email:</strong> ' . htmlspecialchars($contact_email) . '</p>';
                
                $display_text = mb_strlen($ad_text, 'UTF-8') > 200 ? mb_substr($ad_text, 0, 200, 'UTF-8') . '...' : $ad_text;
                echo '<p><strong>Текст объявления:</strong><br><small>' . nl2br(htmlspecialchars($display_text)) . '</small></p>';
                
                echo '<a href="form.html" class="btn btn-primary mt-3">Подать другое объявление</a>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<h4>Ошибка при сохранении:</h4>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<a href="form.html" class="btn btn-primary">Вернуться к форме</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
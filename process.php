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

            if (empty($ad_title)) $errors[] = "Заголовок объявления обязателен.";
            if (empty($ad_category)) $errors[] = "Категория обязательна.";
            if (empty($contact_email)) $errors[] = "Email обязателен.";

            if (strlen($ad_title) > 40) $errors[] = "Заголовок не может быть длиннее 40 символов.";
            if (strlen($ad_category) > 40) $errors[] = "Категория не может быть длиннее 40 символов.";
            if (strlen($contact_email) > 30) $errors[] = "Email не может быть длиннее 30 символов.";
            if (strlen($ad_text) > 1000) $errors[] = "Текст объявления не может быть длиннее 1000 символов.";

            if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Указан некорректный email.";
            }

            if ($price === '' || !is_numeric($price) || $price < 0) {
                $errors[] = "Укажите корректную цену (неотрицательное число).";
            } else {
                if ($price > 999999.99) {
                    $errors[] = "Цена не может быть больше 999999.99.";
                }

                if (preg_match('/^\d+(\.\d{1,2})?$/', $price) === 0) {
                    $errors[] = "Цена должна содержать не более двух знаков после точки.";
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

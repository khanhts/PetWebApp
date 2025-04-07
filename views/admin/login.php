<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/assets/css/admin-login.css">
</head>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('#email, #password');
        const errorMessage = document.querySelector('.error-message');

        inputs.forEach(input => {
            input.addEventListener('focus', function () {
                if (errorMessage) {
                    errorMessage.style.display = 'none';
                }
            });
        });
    });
</script>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (!empty($_SESSION['admin_login_error'])): ?>
            <div class="error-message"><?php echo htmlspecialchars($_SESSION['admin_login_error']); ?></div>
        <?php endif; ?>
        <form action="/admin/login" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
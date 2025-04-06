<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/css/login.css">
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
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="/signup">Sign up here</a></p>
        <p><a href="/home">Go back to Home Page</a></p>
    </div>
</body>
</html>
<?php
require_once 'includes/init.php';

$error = '';
$email = '';


$userModel = new User($pdo);

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = ($_POST['password'] ?? '');
    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        $result = $userModel->login($email, $password);
        if (is_string($result)) {
            $error = $result;
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_role'] = $result['role'];

            if ($result['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: slots.php');
            }
            exit;
        }
    }
}


require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h3 mb-4 text-center">Login</h1>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">Account created successfully. Please log in.</div>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Don't have an account? <a href="register.php">Register</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
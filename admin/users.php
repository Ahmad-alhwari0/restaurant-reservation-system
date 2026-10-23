<?php
require_once '../includes/init.php';
/** @var PDO $pdo */

Auth::requireAdmin('../');
$userModel = new User($pdo);
$error = '';
$success = isset($_GET['updated']) ? 'User status updated.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'activate' || $action === 'deactivate') {
        if ($userModel->setActive($userId, $action === 'activate')) {
            header('Location: users.php?updated=1');
            exit;
        }
    }
    $error = 'Could not update this user.';
}

$users = $userModel->getAll();
$pageTitle = 'Manage Users';
$basePath  = '../';
require_once '../includes/header.php';
?>

<h1 class="h3 mb-4">Manage Users</h1>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge <?= $user['role'] === 'admin' ? 'text-bg-dark' : 'text-bg-light border' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="badge text-bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted text-nowrap"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td class="text-end">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="text-muted small">—</span>
                            <?php else: ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                    <?php if ($user['is_active']): ?>
                                        <button type="submit" name="action" value="deactivate" class="btn btn-sm btn-outline-danger">Disable</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="activate" class="btn btn-sm btn-outline-success">Enable</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
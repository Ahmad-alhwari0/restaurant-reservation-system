<?php
require_once '../includes/init.php';
/** @var PDO $pdo */

Auth::requireAdmin('../');
$reservationModel = new Reservation($pdo);
$error = '';
$success = isset($_GET['updated']) ? 'Reservation status updated.' : '';

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
if ($_SERVER['REQUEST_METHOD'] ==='POST') {
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $result = $reservationModel->updateStatus($reservationId, $action);
    if ($result === true) {
        header('Location: reservations.php?updated=1&' . http_build_query(['search' => $search, 'status' => $status]));
        exit;
    }
    $error = $result;
}

$reservations = $reservationModel->getAll($search, $status);

$pageTitle = 'Manage Reservations';
$basePath  = '../';
require_once '../includes/header.php';

$badgeClasses = [
    'pending'   => 'text-bg-warning',
    'confirmed' => 'text-bg-success',
    'rejected'  => 'text-bg-danger',
    'cancelled' => 'text-bg-secondary',
];

$actions = [
    'pending'   => ['confirmed' => ['Confirm', 'btn-success'], 'rejected' => ['Reject', 'btn-danger'], 'cancelled' => ['Cancel', 'btn-outline-secondary']],
    'confirmed' => ['cancelled' => ['Cancel', 'btn-outline-secondary']],
];
?>

<h1 class="h3 mb-4">Manage Reservations</h1>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="get" class="row g-2 mb-4">
    <div class="col-12 col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email"
            value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-8 col-md-3">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <?php foreach (['pending', 'confirmed', 'rejected', 'cancelled'] as $option): ?>
                <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-4 col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-dark flex-grow-1">Filter</button>
        <a href="reservations.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<?php if (empty($reservations)): ?>
    <div class="alert alert-info">No reservations found.</div>
<?php else: ?>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($reservation['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($reservation['email']) ?></div>
                            </td>
                            <td class="text-nowrap"><?= date('M j, Y', strtotime($reservation['slot_date'])) ?></td>
                            <td class="text-nowrap">
                                <?= date('g:i A', strtotime($reservation['start_time'])) ?> – <?= date('g:i A', strtotime($reservation['end_time'])) ?>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($reservation['note'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $badgeClasses[$reservation['status']] ?>"><?= ucfirst($reservation['status']) ?></span>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if (isset($actions[$reservation['status']])): ?>
                                    <?php foreach ($actions[$reservation['status']] as $newStatus => [$label, $btnClass]): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>">
                                            <button type="submit" name="action" value="<?= $newStatus ?>"
                                                class="btn btn-sm <?= $btnClass ?>"><?= $label ?></button>
                                        </form>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
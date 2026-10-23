<?php
require_once 'includes/init.php';
Auth::requireLogin();
$reservationModel = new Reservation($pdo);
$error = '';
$success = '';


if (isset($_GET['booked'])) {
    $success = 'Your reservation was created and is pending approval.';
}
if (isset($_GET['cancelled'])) {
    $success = 'Your reservation was cancelled.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = (int) ($_POST['reservation_id'] ?? 0);
    $result = $reservationModel->cancel($reservationId, $_SESSION['user_id']);
    if ($result === true) {
        header('Location: my-reservations.php?cancelled=1');
        exit;
    }
    $error = $result;
}

$reservations = $reservationModel->getByUser($_SESSION['user_id']);


$pageTitle = 'My Reservations';
require_once 'includes/header.php';

$badgeClasses = [
    'pending'   => 'text-bg-warning',
    'confirmed' => 'text-bg-success',
    'rejected'  => 'text-bg-danger',
    'cancelled' => 'text-bg-secondary',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Reservations</h1>
    <a href="slots.php" class="btn btn-dark btn-sm">+ New Reservation</a>
</div>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (empty($reservations)): ?>
    <div class="alert alert-info">You have no reservations yet.</div>
<?php else: ?>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php
                        $isFuture  = strtotime($reservation['slot_date'] . ' ' . $reservation['start_time']) > time();
                        $canCancel = $isFuture && in_array($reservation['status'], ['pending', 'confirmed']);
                        ?>
                        <tr>
                            <td><?= date('D, M j, Y', strtotime($reservation['slot_date'])) ?></td>
                            <td class="text-nowrap">
                                <?= date('g:i A', strtotime($reservation['start_time'])) ?> – <?= date('g:i A', strtotime($reservation['end_time'])) ?>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($reservation['note'] ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $badgeClasses[$reservation['status']] ?>">
                                    <?= ucfirst($reservation['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if ($canCancel): ?>
                                    <form method="post" class="d-inline"
                                        onsubmit="return confirm('Cancel this reservation?');">
                                        <input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                                    </form>
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

<?php require_once 'includes/footer.php'; ?>
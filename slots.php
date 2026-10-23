<?php
require_once 'includes/init.php';

Auth::requireLogin();
$timeSlotModel = new TimeSlot($pdo);
$reservationModel = new Reservation($pdo);
$error = '';
$date = $_GET['date'] ?? date('Y-m-d');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $slotId = (int) ($_POST['slot_id'] ?? 0);
    $note = trim($_POST['note'] ?? '');
    if ($note === '') {
        $note = null;
    }

    $slot = $timeSlotModel->getById($slotId);
    $result = $reservationModel->create($_SESSION['user_id'], $slot, $note);

    if ($result === true) {
        header('Location: my-reservations.php?booked=1');
        exit;
    }
    $error = $result;
}
$slots = $timeSlotModel->getByDate($date);
$pageTitle = 'Available Slots';
require_once 'includes/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 mb-0">Available Time Slots</h1>
    <form method="get" class="d-flex gap-2">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>">
        <button type="submit" class="btn btn-dark">Show</button>
    </form>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (empty($slots)): ?>
    <div class="alert alert-info">No time slots for this date. Try another day.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($slots as $slot): ?>
            <?php
            $isPast = strtotime($slot['slot_date'] . ' ' . $slot['start_time']) <= time();
            $isFull = $slot['remaining'] <= 0;
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h5 mb-0">
                                <?= date('g:i A', strtotime($slot['start_time'])) ?> – <?= date('g:i A', strtotime($slot['end_time'])) ?>
                            </h2>
                            <?php if ($isPast): ?>
                                <span class="badge text-bg-secondary">Past</span>
                            <?php elseif ($isFull): ?>
                                <span class="badge text-bg-danger">Full</span>
                            <?php else: ?>
                                <span class="badge text-bg-success"><?= (int) $slot['remaining'] ?> left</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-muted small mb-3">Capacity: <?= (int) $slot['capacity'] ?></p>

                        <?php if (!$isPast && !$isFull): ?>
                            <form method="post" class="mt-auto">
                                <input type="hidden" name="slot_id" value="<?= (int) $slot['id'] ?>">
                                <input type="text" name="note" class="form-control form-control-sm mb-2"
                                    placeholder="Note (optional)" maxlength="255">
                                <button type="submit" class="btn btn-dark btn-sm w-100">Book</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm w-100 mt-auto" disabled>Not available</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
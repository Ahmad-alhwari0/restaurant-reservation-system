<?php
class Reservation
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function findByUserAndSlot($userId, $slotId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE user_id=? AND  slot_id=?");
        $stmt->execute([$userId, $slotId]);

        return $stmt->fetch();
    }
    public function create($userId, $slot, $note)
    {
        if (!$slot) {
            return 'Time slot not found.';
        }
        $slotStart = strtotime($slot['slot_date'] . ' ' . $slot['start_time']);

        if ($slotStart <= time()) {
            return 'You cannot book a past time slot.';
        }

        $existing = $this->findByUserAndSlot($userId, $slot['id']);

        if ($existing) {
            if ($existing['status'] === 'pending' || $existing['status'] === 'confirmed') {
                return 'You already booked this time slot.';
            }
            if ($existing['status'] === 'rejected') {
                return 'Your reservation for this slot was rejected.';
            }
        }
        if ($slot['remaining'] <= 0) {
            return 'This time slot is fully booked.';
        }
        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE reservations SET status = 'pending', note = ? WHERE id = ?");
            $stmt->execute([$note, $existing['id']]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO reservations (user_id, slot_id, note) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $slot['id'], $note]);
        }

        return true;
    }
    public function getByUser($userId)
    {
        $stmt = $this->pdo->prepare("SELECT r.id, r.note, r.status, r.created_at,
        ts.slot_date, ts.start_time, ts.end_time FROM reservations r JOIN time_slots ts ON ts.id = r.slot_id
        WHERE r.user_id = ? ORDER BY ts.slot_date DESC, ts.start_time DESC");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
    public function cancel($reservationId, $userId)
    {
        $stmt = $this->pdo->prepare("SELECT r.id, r.status, ts.slot_date, ts.start_time FROM reservations r JOIN time_slots ts ON ts.id = r.slot_id WHERE r.id = ? AND r.user_id = ?");
        $stmt->execute([$reservationId, $userId]);

        $reservation = $stmt->fetch();

        if (!$reservation) {
            return 'Reservation not found.';
        }
        if (!in_array($reservation['status'], ['pending', 'confirmed'])) {
            return 'This reservation cannot be cancelled.';
        }
        $slotStart = strtotime($reservation['slot_date'] . ' ' . $reservation['start_time']);
        if ($slotStart <= time()) {
            return 'This time slot has already started.';
        }
        $stmt = $this->pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ?");
        $stmt->execute([$reservationId, $userId]);

        return true;
    }


    public function countByStatus()
    {
        $stmt = $this->pdo->query("SELECT status,COUNT(*) AS total FROM reservations GROUP BY status");
        $counts = ['pending' => 0, 'confirmed' => 0, 'rejected' => 0, 'cancelled' => 0];

        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }
}

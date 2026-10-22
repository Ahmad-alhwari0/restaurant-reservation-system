
INSERT INTO users (id, name, email, password, role, is_active) VALUES
(1, 'Admin',        'admin@restaurant.com', '$2y$10$OltH9p5UheE2gAXpxDL1eedsXVCkhO0zKKcMrR92303PGKsebQoWS', 'admin', 1),
(2, 'Ahmad Ali',    'ahmad@test.com',       '$2y$10$TLSspb5my.g572nhhIDlOu1fzDfRQoILGp7dfWlk/FGqa0ECj7lZm', 'user',  1),
(3, 'Sara Khaled',  'sara@test.com',        '$2y$10$TLSspb5my.g572nhhIDlOu1fzDfRQoILGp7dfWlk/FGqa0ECj7lZm', 'user',  1),
(4, 'Omar Disabled','omar@test.com',        '$2y$10$TLSspb5my.g572nhhIDlOu1fzDfRQoILGp7dfWlk/FGqa0ECj7lZm', 'user',  0);

INSERT INTO time_slots (id, slot_date, start_time, end_time, capacity) VALUES
(1, CURDATE() + INTERVAL 1 DAY, '19:00:00', '21:00:00', 5),
(2, CURDATE() + INTERVAL 1 DAY, '21:00:00', '23:00:00', 1),
(3, CURDATE() + INTERVAL 2 DAY, '13:00:00', '15:00:00', 4),
(4, CURDATE() + INTERVAL 3 DAY, '19:00:00', '21:00:00', 6),
(5, CURDATE() - INTERVAL 2 DAY, '19:00:00', '21:00:00', 5);

INSERT INTO reservations (user_id, slot_id, note, status) VALUES
(2, 1, 'Window table please', 'confirmed'),
(3, 1, NULL,                  'pending'),
(3, 2, NULL,                  'confirmed'),
(2, 3, NULL,                  'cancelled'),
(3, 3, NULL,                  'rejected'),
(3, 4, 'Birthday dinner',     'pending'),
(2, 5, NULL,                  'confirmed');
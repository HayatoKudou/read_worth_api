SET foreign_key_checks = 0;

TRUNCATE plans;
INSERT INTO `plans` (`id`, `name`, `price`, `max_members`, `max_books`, `created_at`, `updated_at`)
VALUES
    (1, 'free', 0, 30, 100, '2022-08-20 10:23:37', '2022-08-20 10:23:37'),
    (2, 'beta', 0, 9999, 9999, '2022-08-20 10:23:37', '2022-08-20 10:23:37');

TRUNCATE workspaces;
TRUNCATE users;
TRUNCATE belongings;
TRUNCATE book_category;
TRUNCATE books;
TRUNCATE book_histories;
TRUNCATE book_purchase_applies;
TRUNCATE book_rental_applies;
TRUNCATE book_reviews;
TRUNCATE roles;
TRUNCATE slack_credentials;

SET foreign_key_checks = 1;

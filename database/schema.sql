CREATE TABLE `plans`
(
    `id`          bigint unsigned NOT NULL AUTO_INCREMENT,
    `name`        varchar(50)     NOT NULL COMMENT 'プラン名',
    `price`       smallint        NOT NULL COMMENT 'プラン価格',
    `max_members` smallint        NOT NULL COMMENT 'メンバー上限数',
    `max_books`   smallint        NOT NULL COMMENT '書籍上限数',
    `created_at`  timestamp       NULL DEFAULT NULL,
    `updated_at`  timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `clients`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `plan_id`    bigint unsigned NOT NULL,
    `name`       varchar(255)    NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clients_name_unique` (`name`),
    CONSTRAINT `clients_fk1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `users`
(
    `id`                  bigint unsigned NOT NULL AUTO_INCREMENT,
    `name`                varchar(255)    NOT NULL,
    `email`               varchar(255)    NOT NULL,
    `google_access_token` varchar(255)         DEFAULT NULL,
    `api_token`           varchar(80)          DEFAULT NULL,
    `created_at`          timestamp       NULL DEFAULT NULL,
    `updated_at`          timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_api_token_unique` (`api_token`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `belongings`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned NOT NULL,
    `client_id`  bigint unsigned NOT NULL,
    `role_id`    bigint unsigned NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE (`user_id`, `client_id`),
    CONSTRAINT `belongings_fk1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `belongings_fk2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
    CONSTRAINT `belongings_fk3` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


CREATE TABLE `book_category`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `client_id`  bigint unsigned NOT NULL,
    `name`       varchar(255)    NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_category_fk1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `books`
(
    `id`               bigint unsigned NOT NULL AUTO_INCREMENT,
    `client_id`        bigint unsigned NOT NULL,
    `book_category_id` bigint unsigned NOT NULL,
    `status`           smallint        NOT NULL,
    `title`            varchar(255)    NOT NULL,
    `description`      text,
    `image_path`       varchar(255)         DEFAULT NULL,
    `url`              text,
    `created_at`       timestamp       NULL DEFAULT NULL,
    `updated_at`       timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `books_book_category_id_foreign` (`book_category_id`),
    CONSTRAINT `books_fk1` FOREIGN KEY (`book_category_id`) REFERENCES `book_category` (`id`),
    CONSTRAINT `books_fk2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `book_histories`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned NOT NULL,
    `book_id`    bigint unsigned NOT NULL,
    `action`     text            NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_histories_fk1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_histories_fk2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `book_purchase_applies`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned NOT NULL,
    `client_id`  bigint unsigned NOT NULL,
    `book_id`    bigint unsigned NOT NULL,
    `reason`     text            NOT NULL,
    `price`      int             NOT NULL,
    `step`       smallint        NOT NULL DEFAULT '1',
    `location`   varchar(255)             DEFAULT NULL,
    `created_at` timestamp       NULL     DEFAULT NULL,
    `updated_at` timestamp       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_purchase_applies_fk1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_purchase_applies_fk2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
    CONSTRAINT `book_purchase_applies_fk3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


CREATE TABLE `book_rental_applies`
(
    `id`                   bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`              bigint unsigned NOT NULL,
    `client_id`            bigint unsigned NOT NULL,
    `book_id`              bigint unsigned NOT NULL,
    `reason`               text            NOT NULL COMMENT '申請理由',
    `rental_date`          date            NOT NULL COMMENT '貸出日',
    `expected_return_date` date            NOT NULL COMMENT '返却予定日',
    `return_date`          date                 DEFAULT NULL COMMENT '返却日',
    `created_at`           timestamp       NULL DEFAULT NULL,
    `updated_at`           timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_rental_applies_fk1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_rental_applies_fk2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
    CONSTRAINT `book_rental_applies_fk3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;


CREATE TABLE `book_reviews`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned NOT NULL,
    `book_id`    bigint unsigned NOT NULL,
    `review`     text            NOT NULL,
    `rate`       smallint        NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `book_reviews_fk1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_reviews_fk2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `roles`
(
    `id`                 bigint unsigned NOT NULL AUTO_INCREMENT,
    `is_account_manager` tinyint(1)      NOT NULL DEFAULT '0',
    `is_book_manager`    tinyint(1)      NOT NULL DEFAULT '0',
    `is_client_manager`  tinyint(1)      NOT NULL DEFAULT '0',
    `created_at`         timestamp       NULL     DEFAULT NULL,
    `updated_at`         timestamp       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `slack_credentials`
(
    `id`           bigint unsigned NOT NULL AUTO_INCREMENT,
    `client_id`    bigint unsigned NOT NULL,
    `access_token` varchar(255)    NOT NULL,
    `channel_id`   varchar(255)    NOT NULL,
    `channel_name` varchar(255)    NOT NULL,
    `created_at`   timestamp       NULL DEFAULT NULL,
    `updated_at`   timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `slack_credentials_fk1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE `book_category`
(
    `id`         bigint unsigned                         NOT NULL AUTO_INCREMENT,
    `client_id`  bigint unsigned                         NOT NULL,
    `name`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                               NULL DEFAULT NULL,
    `updated_at` timestamp                               NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `book_category_client_id_foreign` (`client_id`),
    CONSTRAINT `book_category_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `book_histories`
(
    `id`         bigint unsigned                 NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned                 NOT NULL,
    `book_id`    bigint unsigned                 NOT NULL,
    `action`     text COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                       NULL DEFAULT NULL,
    `updated_at` timestamp                       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `book_histories_user_id_foreign` (`user_id`),
    KEY `book_histories_book_id_foreign` (`book_id`),
    CONSTRAINT `book_histories_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `book_purchase_applies`
(
    `id`         bigint unsigned                 NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned                 NOT NULL,
    `client_id`  bigint unsigned                 NOT NULL,
    `book_id`    bigint unsigned                 NOT NULL,
    `reason`     text COLLATE utf8mb4_unicode_ci NOT NULL,
    `price`      int                             NOT NULL,
    `step`       smallint                        NOT NULL DEFAULT '1',
    `location`   varchar(255) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
    `created_at` timestamp                       NULL     DEFAULT NULL,
    `updated_at` timestamp                       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `book_applications_user_id_foreign` (`user_id`),
    KEY `book_applications_client_id_foreign` (`client_id`),
    KEY `book_applications_book_id_foreign` (`book_id`),
    CONSTRAINT `book_applications_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_applications_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
    CONSTRAINT `book_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `book_rental_applies`
(
    `id`                   bigint unsigned                 NOT NULL AUTO_INCREMENT,
    `user_id`              bigint unsigned                 NOT NULL,
    `client_id`            bigint unsigned                 NOT NULL,
    `book_id`              bigint unsigned                 NOT NULL,
    `reason`               text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申請理由',
    `rental_date`          date                            NOT NULL COMMENT '貸出日',
    `expected_return_date` date                            NOT NULL COMMENT '返却予定日',
    `return_date`          date                                 DEFAULT NULL COMMENT '返却日',
    `created_at`           timestamp                       NULL DEFAULT NULL,
    `updated_at`           timestamp                       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `book_rental_applies_user_id_foreign` (`user_id`),
    KEY `book_rental_applies_client_id_foreign` (`client_id`),
    KEY `book_rental_applies_book_id_foreign` (`book_id`),
    CONSTRAINT `book_rental_applies_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_rental_applies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
    CONSTRAINT `book_rental_applies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE `book_reviews`
(
    `id`         bigint unsigned                 NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned                 NOT NULL,
    `book_id`    bigint unsigned                 NOT NULL,
    `review`     text COLLATE utf8mb4_unicode_ci NOT NULL,
    `rate`       smallint                        NOT NULL,
    `created_at` timestamp                       NULL DEFAULT NULL,
    `updated_at` timestamp                       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `book_reviews_user_id_foreign` (`user_id`),
    KEY `book_reviews_book_id_foreign` (`book_id`),
    CONSTRAINT `book_reviews_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
    CONSTRAINT `book_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `books`
(
    `id`               bigint unsigned                         NOT NULL AUTO_INCREMENT,
    `client_id`        bigint unsigned                         NOT NULL,
    `book_category_id` bigint unsigned                         NOT NULL,
    `status`           smallint                                NOT NULL,
    `title`            varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description`      text COLLATE utf8mb4_unicode_ci,
    `image_path`       varchar(255) COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `url`              text COLLATE utf8mb4_unicode_ci,
    `created_at`       timestamp                               NULL DEFAULT NULL,
    `updated_at`       timestamp                               NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `books_book_category_id_foreign` (`book_category_id`),
    KEY `books_client_id_foreign` (`client_id`),
    CONSTRAINT `books_book_category_id_foreign` FOREIGN KEY (`book_category_id`) REFERENCES `book_category` (`id`),
    CONSTRAINT `books_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `clients`
(
    `id`                      bigint unsigned                         NOT NULL AUTO_INCREMENT,
    `plan_id`                 bigint unsigned                         NOT NULL,
    `name`                    varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at`              timestamp                               NULL     DEFAULT NULL,
    `updated_at`              timestamp                               NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `clients_name_unique` (`name`),
    KEY `clients_plan_id_foreign` (`plan_id`),
    CONSTRAINT `clients_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `password_resets`
(
    `email`      varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token`      varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                               NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `plans`
(
    `id`          bigint unsigned                        NOT NULL AUTO_INCREMENT,
    `name`        varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'プラン名',
    `price`       smallint                               NOT NULL COMMENT 'プラン価格',
    `max_members` smallint                               NOT NULL COMMENT 'メンバー上限数',
    `max_books`   smallint                               NOT NULL COMMENT '書籍上限数',
    `created_at`  timestamp                              NULL DEFAULT NULL,
    `updated_at`  timestamp                              NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `roles`
(
    `id`                 bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`            bigint unsigned NOT NULL,
    `is_account_manager` tinyint(1)      NOT NULL DEFAULT '0',
    `is_book_manager`    tinyint(1)      NOT NULL DEFAULT '0',
    `is_client_manager`  tinyint(1)      NOT NULL DEFAULT '0',
    `created_at`         timestamp       NULL     DEFAULT NULL,
    `updated_at`         timestamp       NULL     DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `roles_user_id_foreign` (`user_id`),
    CONSTRAINT `roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `slack_credentials`
(
    `id`           bigint unsigned                         NOT NULL AUTO_INCREMENT,
    `client_id`    bigint unsigned                         NOT NULL,
    `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `channel_id`   varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `channel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at`   timestamp                               NULL DEFAULT NULL,
    `updated_at`   timestamp                               NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `slack_credentials_client_id_foreign` (`client_id`),
    CONSTRAINT `slack_credentials_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `users`
(
    `id`                  bigint unsigned                         NOT NULL AUTO_INCREMENT,
    `client_id`           bigint unsigned                                               DEFAULT NULL,
    `name`                varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email`               varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at`   timestamp                               NULL                  DEFAULT NULL,
    `password`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `password_setting_at` timestamp                               NULL                  DEFAULT NULL,
    `google_access_token` varchar(255) COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `api_token`           varchar(80) COLLATE utf8mb4_unicode_ci                        DEFAULT NULL,
    `remember_token`      varchar(100) COLLATE utf8mb4_unicode_ci                       DEFAULT NULL,
    `created_at`          timestamp                               NULL                  DEFAULT NULL,
    `updated_at`          timestamp                               NULL                  DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_api_token_unique` (`api_token`),
    KEY `users_client_id_foreign` (`client_id`),
    CONSTRAINT `users_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

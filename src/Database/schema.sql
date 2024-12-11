DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
    `id` int(11) NOT NULL,
    `street` varchar(255) NOT NULL,
    `number` int(11) NOT NULL,
    `city` varchar(255) NOT NULL,
    `country` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` int(11) NOT NULL,
    `created_at` datetime NOT NULL,
    `first_name` varchar(255) NOT NULL,
    `middle_name` varchar(255) NULL,
    `last_name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL,
    `address_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY(`address_id`) REFERENCES `addresses`(`id`)
);

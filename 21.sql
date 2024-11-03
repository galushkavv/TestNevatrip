-- Дамп структуры для таблица test_nevatrip.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `event_date` datetime NOT NULL,
  `equal_price` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп структуры для таблица test_nevatrip.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `price` int(11) NOT NULL,
  `barcode` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_tickets_ticket_types` (`type_id`),
  KEY `FK_tickets_orders` (`order_id`),
  CONSTRAINT `FK_tickets_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_tickets_ticket_types` FOREIGN KEY (`type_id`) REFERENCES `ticket_types` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп структуры для таблица test_nevatrip.ticket_types
CREATE TABLE IF NOT EXISTS `ticket_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

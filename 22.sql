-- Дамп структуры для таблица test_nevatrip.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `event_date` datetime NOT NULL,
  `barcode` varchar(120) NOT NULL DEFAULT '0',
  `equal_price` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп структуры для таблица test_nevatrip.orders_tickets
CREATE TABLE IF NOT EXISTS `orders_tickets` (
  `order_id` int(10) unsigned NOT NULL,
  `ticket_type_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`order_id`,`ticket_type_id`) USING BTREE,
  KEY `FK_orders_tickets_tickets` (`ticket_type_id`,`order_id`) USING BTREE,
  CONSTRAINT `FK_orders_tickets_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `FK_orders_tickets_tickets` FOREIGN KEY (`ticket_type_id`) REFERENCES `tickets` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп структуры для таблица test_nevatrip.ticket_types
CREATE TABLE IF NOT EXISTS `ticket_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Дамп структуры для таблица test_nevatrip.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `event_date` datetime NOT NULL,
  `ticket_adult_price` int(11) NOT NULL,
  `ticket_adult_quantity` int(11) unsigned NOT NULL,
  `ticket_kid_price` int(11) NOT NULL,
  `ticket_kid_quantity` int(11) unsigned NOT NULL,
  `barcode` varchar(120) NOT NULL,
  `equal_price` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
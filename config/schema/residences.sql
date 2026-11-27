-- Residences Schema
CREATE TABLE IF NOT EXISTS `residences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `subtitle` varchar(500),
  `description` text,
  `towers_count` int,
  `apartments_count` int,
  `residents_count` int,
  `developer` varchar(200),
  `location` varchar(200),
  `location_description` text,
  `image_url` varchar(500),
  `website` varchar(255),
  `facebook` varchar(255),
  `instagram` varchar(255),
  `twitter` varchar(255),
  `featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int DEFAULT 0,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`),
  KEY `featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Residence Phases (multiple phases/towers per residence)
CREATE TABLE IF NOT EXISTS `residence_phases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `residence_id` int NOT NULL,
  `phase_name` varchar(200) NOT NULL,
  `sort_order` int DEFAULT 0,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `residence_id` (`residence_id`),
  KEY `sort_order` (`sort_order`),
  CONSTRAINT `residence_phases_ibfk_1` FOREIGN KEY (`residence_id`) REFERENCES `residences` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Residence Gallery (multiple photos per residence)
CREATE TABLE IF NOT EXISTS `residence_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `residence_id` int NOT NULL,
  `image_filename` varchar(255),
  `image_dir` varchar(255),
  `image_url` varchar(500) NOT NULL,
  `caption` varchar(255),
  `alt_text` varchar(255),
  `sort_order` int DEFAULT 0,
  `created` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `residence_id` (`residence_id`),
  KEY `sort_order` (`sort_order`),
  CONSTRAINT `residence_gallery_ibfk_1` FOREIGN KEY (`residence_id`) REFERENCES `residences` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

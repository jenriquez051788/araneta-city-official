-- MySQL schema for CMS authentication: roles + users
-- Database: aci-newdb
-- Charset/collation and column conventions mirror config/schema/mainpage.sql
-- ------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Seed data for `roles`
--

LOCK TABLES `roles` WRITE;
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `status`, `created`, `modified`) VALUES
  (1, 'Super Admin', 'superadmin', 'Full access to everything, including user and role management.', 1, NOW(), NOW()),
  (2, 'Admin', 'admin', 'Manage content and users, but cannot manage roles.', 1, NOW(), NOW()),
  (3, 'Marketing', 'marketing', 'Manage marketing content only.', 1, NOW(), NOW());
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role_id` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Seed data for `users`
--
-- Default super admin login:
--   email:    superadmin@aranetacity.com
--   password: Admin@12345
-- The hash below is a bcrypt hash of 'Admin@12345' (CakePHP DefaultPasswordHasher compatible).
-- CHANGE THIS PASSWORD immediately after first login.
--

LOCK TABLES `users` WRITE;
INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `email`, `password`, `status`, `created`, `modified`) VALUES
  (1, 1, 'Super', 'Admin', 'superadmin@aranetacity.com', '$2y$10$ohzobCcg0NcMLU7VwifJyejpIypb0/XVIPqp1vCSLoZpcYHDVn9kO', 1, NOW(), NOW());
UNLOCK TABLES;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

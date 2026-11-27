-- Team Members Table
-- For about_management.php organizational structure

CREATE TABLE IF NOT EXISTS team_members (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    title VARCHAR(200) NOT NULL,
    department VARCHAR(150),
    group_role VARCHAR(150),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_department (department),
    INDEX idx_group_role (group_role),
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

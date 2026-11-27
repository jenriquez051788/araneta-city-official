-- Team Members Seed Data (REAL Araneta Group roster)
-- Transferred from about_management.php

-- Clear existing data first
TRUNCATE TABLE team_members;

INSERT INTO team_members (name, title, department, group_role, status, sort_order) VALUES
-- ============ ACI, Inc. (Property) ============
-- Senior Vice Presidents
('Noel B. Leonor', 'Office of the Chairman, ACI Chief of Staff', 'ACI, Inc.', 'Senior Vice Presidents', 1, 1),
('Antonio T. Mardo, CSM', 'Operations', 'ACI, Inc.', 'Senior Vice Presidents', 1, 2),
('John Peter Chick B. Castelo', 'Treasurer/Business Development', 'ACI, Inc.', 'Senior Vice Presidents', 1, 3),
('Janet Ruth Solsoloy', 'Human Resources', 'ACI, Inc.', 'Senior Vice Presidents', 1, 4),
('Lorna D. Fabian', 'Leasing', 'ACI, Inc.', 'Senior Vice Presidents', 1, 5),
-- Vice Presidents
('Morriel A. Abogado, CSM', 'Operations', 'ACI, Inc.', 'Vice Presidents', 1, 6),
('Atty. Philip U. Ang', 'Legal Affairs', 'ACI, Inc.', 'Vice Presidents', 1, 7),
('Erwin Dennis P. Lacson', 'Supply Chain Management', 'ACI, Inc.', 'Vice Presidents', 1, 8),
('Nelon N. Medina', 'Controllership', 'ACI, Inc.', 'Vice Presidents', 1, 9),
('Rolan D. Campos II', 'Treasury', 'ACI, Inc.', 'Vice Presidents', 1, 10),
('Marjorie C. Go', 'Marketing', 'ACI, Inc.', 'Vice Presidents', 1, 11),
('Jorge A. Fores', '', 'ACI, Inc.', 'Vice Presidents', 1, 12),
('Jose Amado A. Fores', '', 'ACI, Inc.', 'Vice Presidents', 1, 13),
-- Assistant Vice Presidents
('Marie Anthonnette M. Bunag', 'Leasing', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 14),
('Diane C. Romero', 'JAAF', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 15),
('Madel Parocha Cervantes', 'Digital Marketing', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 16),
('Kristine L. Reyes', 'Construction', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 17),
('Erneil Nanola', 'Business Development', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 18),
('Mike Canlas', 'IT', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 19),
('AJ Dela Fuente', 'Organization Development', 'ACI, Inc.', 'Assistant Vice Presidents', 1, 20),
-- Consultants
('Teofilo R. Asuncion Jr.', 'Organization Development', 'ACI, Inc.', 'Consultants', 1, 21),
('J. Rowell L. Recinto', 'General Management', 'ACI, Inc.', 'Consultants', 1, 22),
('Juliana I. Castro', 'Design', 'ACI, Inc.', 'Consultants', 1, 23),
('Jose B. Rasalan', 'Audit', 'ACI, Inc.', 'Consultants', 1, 24),
('Ma. Teresita Gonzaga', 'Archives', 'ACI, Inc.', 'Consultants', 1, 25),
('Francis Mercado', 'Architecture & Engineering', 'ACI, Inc.', 'Consultants', 1, 26),

-- ============ Uniprom, Inc. (Leisure & Entertainment) ============
('Irene L. Jose', 'COO', 'Uniprom, Inc.', 'Leadership', 1, 27),
('Veana A. Fores', 'Vice President', 'Uniprom, Inc.', 'Leadership', 1, 28),

-- ============ Progressive Dev. Corp. (Investments & Assets) ============
('Mar A. Roxas', 'President', 'Progressive Dev. Corp.', 'Leadership', 1, 29),
('Jay Enriquez', 'AVP, Controllership', 'Progressive Dev. Corp.', 'Officers', 1, 30),
('Desiderio A. Fuerte', 'Luzon Operations', 'Progressive Dev. Corp.', 'Officers', 1, 31),
('Cipriano E. Querol Jr.', 'Security', 'Progressive Dev. Corp.', 'Officers', 1, 32),
('Felix Manuel T. Trebol', 'AG Visayas Operations', 'Progressive Dev. Corp.', 'Officers', 1, 33),

-- ============ Araneta Hotels (Hospitality) ============
('Maria Garcia', 'General Manager', 'Araneta Hotels', 'Leadership', 1, 34),
('Joy De Mesa', 'Hotel Manager, Novotel Manila Araneta City', 'Araneta Hotels', 'Leadership', 1, 35),
('Therese Galindo', 'Cluster Head, Guest Experience, Sustainability & Safety', 'Araneta Hotels', 'Leadership', 1, 36),

-- ============ Philippine Pizza (Food Service) ============
('Liza Leah Juinio', 'COO', 'Philippine Pizza', 'Leadership', 1, 37),
('Maria Rosalita M. Herrera', 'Human Resources', 'Philippine Pizza', 'Senior Vice Presidents', 1, 38),
('Rafael Alfonso P. Blardony', 'Business Development', 'Philippine Pizza', 'Senior Vice Presidents', 1, 39),
('Don Eric A. Romarate', 'VP, Taco Bell & Dairy Queen', 'Philippine Pizza', 'Vice Presidents', 1, 40),
('Frank F. Gonzales', 'VP for Table Service, PPHI', 'Philippine Pizza', 'Vice Presidents', 1, 41),
('Erryl John E. Ramos', 'VP for Home Service, PPHI', 'Philippine Pizza', 'Vice Presidents', 1, 42),
('Rommel R. Blanco', 'Supply Chain Management', 'Philippine Pizza', 'Vice Presidents', 1, 43),
('Raymund R. Nobleza', 'Marketing', 'Philippine Pizza', 'Assistant Vice Presidents', 1, 44),
('Analiza Santiago', 'Finance', 'Philippine Pizza', 'Assistant Vice Presidents', 1, 45),
('Ralph Marasigan', 'Corporate Planning', 'Philippine Pizza', 'Assistant Vice Presidents', 1, 46),
('Grace Fornier Magno', 'Marketing', 'Philippine Pizza', 'Consultants', 1, 47),
('Nick David', 'Business Development', 'Philippine Pizza', 'Consultants', 1, 48),
('Dennis Hipolito', 'Food Innovation', 'Philippine Pizza', 'Consultants', 1, 49);

-- Sample Residences Data
INSERT INTO residences (title, slug, subtitle, description, towers_count, apartments_count, residents_count, developer, location, image_url, website, facebook, instagram, twitter, featured, status, sort_order, created, modified) VALUES
('Azure Urban Residences', 'azure-urban-residences', 'Modern Living in the Heart of Araneta City', 'Azure Urban Residences offers contemporary apartments with state-of-the-art amenities.', 3, 150, 450, 'Progressive Development Corp', 'Block A, Araneta City', 'https://example.com/azure.jpg', 'https://azure.example.com', 'https://facebook.com/azureurban', 'https://instagram.com/azureurban', 'https://twitter.com/azureurban', 1, 1, 1, NOW(), NOW()),
('Prestige Tower', 'prestige-tower', 'Premium Residential Excellence', 'Prestige Tower stands as a beacon of luxury and comfort for discerning residents.', 2, 120, 360, 'Araneta Hotels & Resorts', 'Block B, Araneta City', 'https://example.com/prestige.jpg', 'https://prestige.example.com', 'https://facebook.com/prestigetower', 'https://instagram.com/prestigetower', 'https://twitter.com/prestigetower', 1, 1, 2, NOW(), NOW()),
('The Harmonious', 'the-harmonious', 'Sustainable Living Community', 'The Harmonious combines eco-friendly design with modern comfort and convenience.', 4, 200, 600, 'Uniprom Corporation', 'Block C, Araneta City', 'https://example.com/harmonious.jpg', 'https://harmonious.example.com', 'https://facebook.com/theharmonious', 'https://instagram.com/theharmonious', 'https://twitter.com/theharmonious', 1, 1, 3, NOW(), NOW()),
('Zenith Towers', 'zenith-towers', 'Urban Living Redefined', 'Zenith Towers offers spectacular views and world-class facilities for modern families.', 5, 250, 750, 'Philippine Pizza Development', 'Block D, Araneta City', 'https://example.com/zenith.jpg', 'https://zenith.example.com', 'https://facebook.com/zenith-towers', 'https://instagram.com/zenith-towers', 'https://twitter.com/zenith-towers', 1, 1, 4, NOW(), NOW());

-- Sample Residence Phases
INSERT INTO residence_phases (residence_id, phase_name, sort_order, created) VALUES
(1, 'Phase 1 - Tower A', 1, NOW()),
(1, 'Phase 2 - Tower B', 2, NOW()),
(1, 'Phase 3 - Tower C', 3, NOW()),
(2, 'East Wing', 1, NOW()),
(2, 'West Wing', 2, NOW()),
(3, 'Block C1 - Towers 1-2', 1, NOW()),
(3, 'Block C2 - Towers 3-4', 2, NOW()),
(3, 'Block C3 - Tower 5', 3, NOW()),
(3, 'Block C4 - Tower 6', 4, NOW()),
(4, 'Zenith North', 1, NOW()),
(4, 'Zenith South', 2, NOW()),
(4, 'Zenith East', 3, NOW()),
(4, 'Zenith West', 4, NOW()),
(4, 'Zenith Central', 5, NOW());

-- Sample Residence Gallery
INSERT INTO residence_gallery (residence_id, image_url, caption, alt_text, sort_order, created) VALUES
(1, 'https://example.com/azure-lobby.jpg', 'Elegant Lobby', 'Azure Urban Residences Lobby', 1, NOW()),
(1, 'https://example.com/azure-bedroom.jpg', 'Spacious Bedroom', 'Typical bedroom with modern furnishings', 2, NOW()),
(1, 'https://example.com/azure-gym.jpg', 'Modern Gym Facility', 'Fully equipped gymnasium', 3, NOW()),
(1, 'https://example.com/azure-pool.jpg', 'Olympic-Size Swimming Pool', 'Recreational swimming pool', 4, NOW()),
(2, 'https://example.com/prestige-entrance.jpg', 'Grand Entrance', 'Prestige Tower Main Entrance', 1, NOW()),
(2, 'https://example.com/prestige-suite.jpg', 'Luxury Suite', 'Premium apartment unit', 2, NOW()),
(2, 'https://example.com/prestige-dining.jpg', 'Fine Dining Area', 'Upscale dining facility', 3, NOW()),
(3, 'https://example.com/harmonious-garden.jpg', 'Green Garden Area', 'Eco-friendly garden space', 1, NOW()),
(3, 'https://example.com/harmonious-studio.jpg', 'Yoga Studio', 'Wellness and meditation area', 2, NOW()),
(3, 'https://example.com/harmonious-condo.jpg', 'Model Unit', 'Sample apartment layout', 3, NOW()),
(4, 'https://example.com/zenith-skyline.jpg', 'City Skyline View', 'View from Zenith Towers', 1, NOW()),
(4, 'https://example.com/zenith-lounge.jpg', 'Sky Lounge', 'Rooftop recreational lounge', 2, NOW()),
(4, 'https://example.com/zenith-ballroom.jpg', 'Grand Ballroom', 'Events and celebration space', 3, NOW()),
(4, 'https://example.com/zenith-kids.jpg', 'Kids Play Area', 'Family-friendly recreation zone', 4, NOW());

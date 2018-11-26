# Pass for admin - wasderty
# Pass for staff - wasdee
INSERT INTO `accounts` (`name`, `email`, `password`)
VALUES
	('Admin', 'admin@esportsconstruct.com', '$2y$10$soBahLSBjfR/UmNoyEoZfuvRp.3X50TMtTzdo2zw.ji6WCFhAMTLC'),
	('Staff', 'staff@esportsconstruct.com', '$2y$10$c6Gg/GCp9RPUQQMbaogivOq83X2JgIgfS0EHSGPg2vr8QVxhvHC8G');

INSERT INTO `roles` (`name`, `display_name`, `description`)
VALUES
	('staff', 'Staff', 'Staff'),
	('owner', 'Owner', 'Owner');

INSERT INTO `permissions` (`name`, `display_name`, `description`)
VALUES
	('add_roles', 'Add Roles', 'Add Roles'),
	('manage_roles', 'Manage Roles', ''),
	('manage_perms', 'Manage Permissions', '');

INSERT INTO `role_user` (`user_id`, `role_id`)
VALUES
	(1, 2),
	(2, 1);

INSERT INTO `permission_role` (`permission_id`, `role_id`)
VALUES
	(1, 2),
	(2, 2),
	(3, 2);
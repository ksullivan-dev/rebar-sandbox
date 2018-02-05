-- Add the SQL for your "up" command here:

INSERT INTO `types` (name, slug) VALUES ('Phone','phone'), ('Tablet','tablet'), ('Console','console');

-- //@UNDO
-- Add the SQL for your "down" command here:

DELETE FROM `types`;

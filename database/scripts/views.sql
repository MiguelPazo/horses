CREATE VIEW animal_report_short AS
SELECT a.id, a.name, c.prefix, c.names , c.lastnames
FROM animals a
LEFT JOIN animal_agent b ON b.animal_id = a.id AND b.type = 'breeder'
LEFT JOIN agents c ON c.id = b.agent_id
WHERE a.deleted_at IS NULL;

CREATE VIEW animal_report AS
SELECT a.id, a.code, a.name, DATE_FORMAT(a.birthdate, '%d-%m-%Y') AS birthdate, c.prefix, 
TRIM(CONCAT(IF(c.names IS NULL, '', c.names), ' ', IF(c.lastnames IS NULL, '', c.lastnames))) AS breeder, 
TRIM(CONCAT(IF(e.names IS NULL, '', e.names), ' ', IF(e.lastnames IS NULL, '', e.lastnames))) AS 'owner',
mom.prefix AS mom_prefix, mom.name AS mom_name, dad.prefix AS dad_prefix, dad.name AS dad_name
FROM animals a
LEFT JOIN animal_agent b ON b.animal_id = a.id AND b.type = 'breeder'
LEFT JOIN agents c ON c.id = b.agent_id
LEFT JOIN animal_agent d ON d.animal_id = a.id AND d.type = 'owner'
LEFT JOIN agents e ON e.id = d.agent_id
LEFT JOIN animal_report_short mom ON mom.id = a.mom
LEFT JOIN animal_report_short dad ON dad.id = a.dad
WHERE a.deleted_at IS NULL;

CREATE VIEW animal_tournament_short AS
SELECT tournament_id, animal_id, COUNT(*) AS total_categories
FROM catalogs
GROUP BY animal_id, tournament_id;

CREATE VIEW animal_tournament AS
SELECT a.*, d.prefix, b.name, b.code, DATE_FORMAT(b.birthdate, '%d-%m-%Y') AS birthdate, d.names AS 'breeder', f.names AS 'owner'
FROM animal_tournament_short a
INNER JOIN animals b ON b.id = a.animal_id
LEFT JOIN animal_agent c ON c.animal_id = b.id AND c.type = 'breeder'
LEFT JOIN agents d ON d.id = c.agent_id
LEFT JOIN animal_agent e ON e.animal_id = b.id AND e.type = 'owner'
LEFT JOIN agents f ON f.id = e.agent_id
WHERE b.deleted_at IS NULL;

CREATE VIEW catalog_report AS
SELECT c.tournament_id AS tournament_id, c.id AS category_id, c.mode, c.order, c.description AS description, ca.group, ca.number, ca.animal_id, 
ar.prefix, ar.name, ar.code, DATE_FORMAT(a.birthdate, '%d-%m-%Y') AS birthdate, ar.dad_prefix, ar.dad_name, ar.mom_prefix, ar.mom_name, ar.breeder, ar.owner
FROM categories c
INNER JOIN catalogs ca ON ca.category_id = c.id
INNER JOIN animals a ON a.id = ca.animal_id
INNER JOIN animal_report ar ON ar.id = ca.animal_id
WHERE c.status <> 'deleted' AND ca.outsider = 0
ORDER BY c.tournament_id, c.order, ca.group, a.birthdate;
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
SELECT a.*, d.prefix, b.name, b.code, DATE_FORMAT(b.birthdate, '%d-%m-%Y') AS birthdate, d.names AS OWNER
FROM animal_tournament_short a
INNER JOIN animals b ON b.id = a.animal_id
LEFT JOIN animal_agent c ON c.animal_id = b.id AND c.type = 'breeder'
LEFT JOIN agents d ON d.id = c.agent_id;

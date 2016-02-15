SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM animal_agent;
DELETE FROM agents;
DELETE FROM catalogs;
DELETE FROM animals;

DELETE FROM category_users WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 2);
DELETE FROM stages WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 2);
DELETE FROM competitors WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 2);
DELETE FROM categories WHERE tournament_id = 2;
DELETE FROM tournaments WHERE id = 2;
SET FOREIGN_KEY_CHECKS = 1;


DELETE FROM stages WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM category_users WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM competitors WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM categories WHERE STATUS = 'deleted';

/*MIGRACION*/
CREATE TABLE temp1 (
category VARCHAR(5),
number VARCHAR(5),
catalog VARCHAR(5),
prefix VARCHAR(20),
NAME VARCHAR(45),
CODE VARCHAR(20),
birthdate VARCHAR(15),
dad_prefix VARCHAR(20),
dad_name VARCHAR(45),
mom_prefix VARCHAR(20),
mom_name VARCHAR(45),
breeder VARCHAR(100),
OWNER VARCHAR(100)
);

/*INSERT AGENTS*/
INSERT INTO agents (prefix, NAMES, created_at, updated_at)
SELECT a.*, CURRENT_TIMESTAMP AS created_at, CURRENT_TIMESTAMP AS deleted_at FROM (
SELECT b.prefix, a.name FROM (
SELECT DISTINCT NAME FROM (
SELECT breeder AS NAME
FROM temp1
UNION ALL
SELECT OWNER AS NAME
FROM temp1) a ) a
LEFT JOIN (SELECT prefix, breeder
FROM temp1
GROUP BY breeder) b ON b.breeder = a.name) a
WHERE a.name NOT IN(
    SELECT TRIM(CONCAT(NAMES, ' ' , lastnames)) FROM agents
);

/*ADD PREFIX COLUMNN TO ANIMALS*/
ALTER TABLE animals ADD prefix VARCHAR(20);

/*INSERT ANIMALS*/
INSERT INTO animals(prefix, CODE, NAME, birthdate)
SELECT a.prefix, IF(a.code = '', NULL, a.code) AS CODE, 
a.name, a.birthdate FROM (
SELECT prefix, CODE, NAME, STR_TO_DATE(birthdate, '%d/%m/%Y') AS birthdate
FROM temp1
UNION ALL
SELECT dad_prefix, NULL AS CODE, dad_name, NULL AS birthdate
FROM temp1
UNION ALL
SELECT mom_prefix, NULL AS CODE, mom_name, NULL AS birthdate
FROM temp1) a
WHERE a.name <> ''
GROUP BY a.name, a.prefix;

/*UPDATE MOM AND DAD*/
UPDATE animals a SET a.dad = (
SELECT f.dad FROM (
	SELECT d.id, b.id AS dad
	FROM temp1 a
	LEFT JOIN (
	    SELECT b.id, a.* 
	    FROM (
	    SELECT prefix, NAME
	    FROM temp1
	    GROUP BY NAME, prefix) a
	    LEFT JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
	) d ON d.name = a.name AND d.prefix = a.prefix
	LEFT JOIN (
	    SELECT b.id, a.* 
	    FROM (
	    SELECT dad_prefix, dad_name
	    FROM temp1
	    GROUP BY dad_name, dad_prefix) a
	    INNER JOIN animals b ON b.name = a.dad_name AND b.prefix = a.dad_prefix
	) b ON b.dad_name = a.dad_name AND b.dad_prefix = a.dad_prefix
) f WHERE f.id = a.id GROUP BY f.id
), a.mom = (
SELECT g.dad FROM (
	SELECT d.id, b.id AS dad
	FROM temp1 a
	LEFT JOIN (
	    SELECT b.id, a.* 
	    FROM (
	    SELECT prefix, NAME
	    FROM temp1
	    GROUP BY NAME, prefix) a
	    LEFT JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
	) d ON d.name = a.name AND d.prefix = a.prefix
	LEFT JOIN (
	    SELECT b.id, a.* 
	    FROM (
	    SELECT mom_prefix, mom_name
	    FROM temp1
	    GROUP BY mom_name, mom_prefix) a
	    INNER JOIN animals b ON b.name = a.mom_name AND b.prefix = a.mom_prefix
	) b ON b.mom_name = a.mom_name AND b.mom_prefix = a.mom_prefix
) g WHERE g.id = a.id GROUP BY g.id
)

/*UPDATE GENDER*/
UPDATE animals SET gender = (
    SELECT id, 'female' AS gender 
    FROM animals
    WHERE id IN (SELECT mom FROM animals GROUP BY mom)
)
WHERE id IN (SELECT a.mom FROM animals a GROUP BY a.mom);





SELECT * FROM temp1 WHERE NAME LIKE '%TITA%'





SELECT d.id, a.prefix, a.name, b.id AS dad, c.id AS mom
FROM temp1 a
LEFT JOIN (
    SELECT b.id, a.* 
    FROM (
    SELECT prefix, NAME
    FROM temp1
    GROUP BY NAME, prefix) a
    LEFT JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
) d ON d.name = a.name AND d.prefix = a.prefix
LEFT JOIN (
    SELECT b.id, a.* 
    FROM (
    SELECT dad_prefix, dad_name
    FROM temp1
    GROUP BY dad_name, dad_prefix) a
    INNER JOIN animals b ON b.name = a.dad_name AND b.prefix = a.dad_prefix
) b ON b.dad_name = a.dad_name AND b.dad_prefix = a.dad_prefix
LEFT JOIN (
    SELECT b.id, a.* 
    FROM (
    SELECT mom_prefix, mom_name
    FROM temp1
    GROUP BY mom_name, mom_prefix) a
    INNER JOIN animals b ON b.name = a.mom_name AND b.prefix = a.mom_prefix
) c ON c.mom_name = a.mom_name AND c.mom_prefix = a.mom_prefix






SELECT * FROM animals WHERE NAME = 'DOMINICA'

SELECT * FROM temp1 WHERE NAME = 'DOMINICA'


DELETE FROM animals

SELECT animal_id, agent_id, TYPE
FROM animal_agent


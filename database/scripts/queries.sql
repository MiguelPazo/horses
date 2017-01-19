SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM animal_agent;
DELETE FROM agents;
DELETE FROM catalogs;
DELETE FROM animals;

DELETE FROM category_users WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
DELETE FROM stages WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
DELETE FROM competitors WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
DELETE FROM catalogs WHERE tournament_id = 3;
DELETE FROM categories WHERE tournament_id = 3;
DELETE FROM tournaments WHERE id = 3;
SET FOREIGN_KEY_CHECKS = 1;


DELETE FROM stages WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM category_users WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM competitors WHERE category_id IN (SELECT id FROM categories WHERE STATUS = 'deleted');
DELETE FROM categories WHERE STATUS = 'deleted';

/*MIGRACION*/
DROP TABLE temp1;

CREATE TABLE temp1 (
category VARCHAR(5),
catalog VARCHAR(5),
number VARCHAR(5),
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
) CHARACTER SET UTF8 COLLATE utf8_general_ci;

SELECT COUNT(*) FROM temp1

/*UPDATE AGENTS NAMES AS TOURNAMENT EXCEL*/
UPDATE temp1 SET NAME = REPLACE(NAME, '?', 'Ñ'), mom_name = REPLACE(mom_name, '?', 'Ñ'), dad_name = REPLACE(dad_name, '?', 'Ñ'),
breeder = REPLACE(breeder, '?', 'Ñ'), OWNER = REPLACE(OWNER, '?', 'Ñ');

UPDATE agents SET NAMES = 'JUAN MANUEL RIZO PATRON BARUA' WHERE NAMES = 'JUAN MANUEL RIZO PATRON B. E HIJAS';
UPDATE agents SET NAMES = 'EDUARDO EDGARDO CHAMAN COMOTTO' WHERE NAMES = 'EDUARDO EDGARDO CHAMAN COMMOTTO';

UPDATE temp1 SET breeder = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A.' WHERE breeder = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A';
UPDATE temp1 SET OWNER = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A.' WHERE OWNER = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A';
/**********************************/


/*INSERT AGENTS*/
INSERT INTO agents (prefix, NAMES, created_at, updated_at)
SELECT a.*, CURRENT_TIMESTAMP AS created_at, CURRENT_TIMESTAMP AS updated_at FROM (
SELECT b.prefix, a.name FROM (
SELECT DISTINCT NAME FROM (
SELECT breeder AS NAME
FROM temp1
UNION ALL
SELECT OWNER AS NAME
FROM temp1) a ) a
LEFT JOIN (SELECT prefix, breeder
FROM temp1
WHERE prefix <> ''
GROUP BY breeder) b ON b.breeder = a.name) a
WHERE a.name NOT IN(
    SELECT TRIM(NAMES) FROM agents
);

/*UPDATE AGENTS WITHOUT NAMES*/
UPDATE agents a SET NAMES = (
	SELECT b.breeder 
	FROM temp1 b
	WHERE b.prefix = a.prefix AND b.breeder IS NOT NULL
	GROUP BY prefix
) WHERE a.prefix = a.names;

/*UPDATE AGENTS WITHOUT PREFIX*/
UPDATE agents a SET prefix = (
	SELECT b.prefix
	FROM temp1 b
	WHERE b.breeder = a.names
	GROUP BY prefix
) WHERE prefix IS NULL OR prefix = '';

/*ADD PREFIX COLUMNN TO ANIMALS*/
ALTER TABLE animals ADD prefix VARCHAR(20);

/*ADD PREFIX TO ANIMAL TABLE*/
CREATE TABLE temp2 AS
SELECT a.id, a.code, a.name, c.prefix, c.names
FROM animals a
INNER JOIN animal_agent b ON b.animal_id = a.id AND b.type='breeder'
INNER JOIN agents c ON c.id = b.agent_id;

UPDATE animals a SET prefix = (
	SELECT prefix
	FROM temp2 b
	WHERE b.id = a.id
);

DROP TABLE temp2;

/*INSERT ANIMALS*/
INSERT INTO animals(prefix, CODE, NAME, birthdate, created_at, updated_at)
SELECT a.prefix, IF(a.code = '', NULL, a.code) AS CODE, 
a.name, a.birthdate, CURRENT_TIMESTAMP AS created_at, CURRENT_TIMESTAMP AS updated_at 
FROM (
SELECT prefix, CODE, NAME, IF(birthdate <> '', STR_TO_DATE(birthdate, '%d/%m/%Y'), NULL) AS birthdate
FROM temp1
UNION ALL
SELECT dad_prefix, NULL AS CODE, dad_name, NULL AS birthdate
FROM temp1
UNION ALL
SELECT mom_prefix, NULL AS CODE, mom_name, NULL AS birthdate
FROM temp1) a
WHERE a.name <> '' AND CONCAT(prefix, NAME) NOT IN (SELECT CONCAT(prefix, NAME) FROM animals WHERE prefix IS NOT NULL)
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
);

/*UPDATE GENDER*/
UPDATE animals a SET a.gender = (
    SELECT gender
    FROM (
        SELECT id, 'female' AS gender
	FROM animals
	WHERE id IN (SELECT mom FROM animals WHERE mom IS NOT NULL)
	UNION ALL
	SELECT id, 'male' AS gender
	FROM animals
	WHERE id IN (SELECT dad FROM animals WHERE dad IS NOT NULL)) b
	WHERE b.id = a.id
);

/*VALIDATE BREEDER WITH DIFF NAME IN TEMP1*/
SELECT a.* FROM (
SELECT * FROM (
SELECT b.id AS animal_id, c.id AS agent_id, 'breeder' AS TYPE
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.breeder AND c.prefix = a.prefix
GROUP BY b.id
) c WHERE CONCAT(animal_id, agent_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, agent_id, TYPE) FROM animal_agent)
) a
WHERE animal_id IN (SELECT animal_id FROM animal_agent WHERE `type`='breeder');

/*INSERT BREEDERS*/
INSERT INTO animal_agent(animal_id, agent_id, TYPE)
SELECT * FROM (
SELECT b.id AS animal_id, c.id AS agent_id, 'breeder' AS TYPE
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.breeder AND c.prefix = a.prefix
GROUP BY b.id
) c WHERE CONCAT(animal_id, agent_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, agent_id, TYPE) FROM animal_agent);

/*INSERT OWNERS*/
INSERT INTO animal_agent(animal_id, agent_id, TYPE)
SELECT * FROM (
SELECT b.id AS animal_id, c.id AS agent_id, 'owner' AS TYPE
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.owner
GROUP BY b.id
) c WHERE CONCAT(animal_id, agent_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, agent_id, TYPE) FROM animal_agent);


/*VALIDATE BREEDERS OF PARENTS*/
SELECT a.* FROM (
SELECT * FROM (
SELECT a.id AS animal_id, b.id AS agent_id, 'breeder' AS TYPE
FROM (
SELECT a.id, a.prefix 
FROM animals a
WHERE id IN (
	SELECT DISTINCT(mom) FROM animals
	UNION ALL
	SELECT DISTINCT(dad) FROM animals
)) a INNER JOIN agents b ON b.prefix = a.prefix
) c WHERE CONCAT(animal_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, TYPE) FROM animal_agent)
GROUP BY animal_id
) a
WHERE animal_id IN (SELECT animal_id FROM animal_agent WHERE TYPE='breeder');


/*INSERT BREEDERS OF PARENTS*/
INSERT INTO animal_agent(animal_id, agent_id, TYPE)
SELECT * FROM (
SELECT a.id AS animal_id, b.id AS agent_id, 'breeder' AS TYPE
FROM (
SELECT a.id, a.prefix 
FROM animals a
WHERE id IN (
	SELECT DISTINCT(mom) FROM animals
	UNION ALL
	SELECT DISTINCT(dad) FROM animals
)) a INNER JOIN agents b ON b.prefix = a.prefix
) c WHERE CONCAT(animal_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, TYPE) FROM animal_agent)
GROUP BY animal_id;

/*ANIMALS WITH MULTIPLES BREEDERS AND OWNERS*/
SELECT a.id, a.animal_id, c.code, c.name, a.type, a.agent_id, b.prefix, b.names
FROM animal_agent a
INNER JOIN agents b ON b.id = a.agent_id
INNER JOIN animals c ON c.id = a.animal_id
WHERE a.animal_id IN (
	SELECT animal_id
	FROM animal_agent
	WHERE TYPE = 'owner'
	GROUP BY animal_id
	HAVING COUNT(*) > 1
) AND TYPE = 'owner'
ORDER BY animal_id, TYPE;

/*DELETE OLD OWNERS RELATIONSHIP*/
DELETE FROM animal_agent WHERE id IN (4438,6428,6056);
DELETE FROM animal_agent WHERE id IN (3522,3533,6685,3682,6695,5438,5482,5490);

/*INSERT CATALOGS*/
INSERT INTO catalogs(`group`, number, category_id, tournament_id, animal_id)
SELECT number AS 'group', catalog AS number, category AS category_id, 5 AS tournament_id, b.id AS animal_id
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix;

/*UPDATE COUNT COMPETITORS*/
UPDATE categories c SET count_competitors = (
	SELECT COUNT(*) AS total 
	FROM catalogs
	WHERE category_id = c.id
	GROUP BY category_id
) WHERE tournament_id = 5;

/*VERIFY AGENTS*/
SELECT * FROM (
SELECT b.id
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.owner
GROUP BY b.id) a
WHERE id NOT IN (
SELECT b.id
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.breeder AND c.prefix = a.prefix
GROUP BY b.id
)

/*DROP PREFIX COLUMNN TO ANIMALS*/
ALTER TABLE animals DROP prefix;

/*UPDATE CODE AND BIRTHDATE FOR ANIMALS WITHOUT IT*/
CREATE TABLE temp3 AS
SELECT a.id, a.name, b.code, IF(b.birthdate <> '', STR_TO_DATE(b.birthdate, '%d/%m/%Y'), NULL) AS birthdate
FROM animals a
INNER JOIN temp1 b ON b.name = a.name
WHERE id IN (
SELECT animal_id
FROM catalog_report
WHERE tournament_id = 5
AND NAME IS NOT NULL
AND CODE IS NULL
) GROUP BY a.id;
	
UPDATE animals a SET a.code = (
	SELECT b.code
	FROM temp3 b
	WHERE b.id = a.id
), a.birthdate = (
	SELECT c.birthdate
	FROM temp3 c
	WHERE c.id = a.id
) WHERE a.id IN (SELECT id FROM temp3);

DROP TABLE temp3;

/*INSERT MENORES*/
INSERT INTO animals(NAME) VALUES ('CONSTANZA DEMICHELLI GUIULFO');

INSERT INTO catalogs (category_id, tournament_id, animal_id)
VALUES (259,5,5425),(259,5,6201),(259,5,5428);
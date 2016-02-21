SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM animal_agent;
DELETE FROM agents;
DELETE FROM catalogs;
DELETE FROM animals;

DELETE FROM category_users WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
DELETE FROM stages WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
DELETE FROM competitors WHERE category_id IN (SELECT id FROM categories WHERE tournament_id = 3);
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

SELECT COUNT(*) FROM temp1

SELECT * FROM temp1 WHERE dad_name LIKE '%?%'
UPDATE temp1 SET NAME = REPLACE(NAME, '?', 'Ñ');
UPDATE temp1 SET mom_name = REPLACE(mom_name, '?', 'Ñ');
UPDATE temp1 SET dad_name = REPLACE(dad_name, '?', 'Ñ');
UPDATE temp1 SET breeder = REPLACE(breeder, '?', 'Ñ');
UPDATE temp1 SET OWNER = REPLACE(OWNER, '?', 'Ñ');

SELECT * FROM temp1 WHERE NAME LIKE '%?%'


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
    SELECT TRIM(CONCAT(NAMES, ' ' , lastnames)) FROM agents
);

/*ADD PREFIX COLUMNN TO ANIMALS*/
ALTER TABLE animals ADD prefix VARCHAR(20);

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
WHERE a.name <> '' AND CONCAT(prefix, NAME) NOT IN (SELECT CONCAT(prefix, NAME) FROM animals)
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
) c WHERE CONCAT(animal_id, agent_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, agent_id, TYPE) FROM animal_agent);

/*INSERT CATALOGS*/
INSERT INTO catalogs(number, category_id, tournament_id, animal_id)
SELECT catalog AS number, category AS category_id, 2 AS tournament_id, b.id AS animal_id
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix;

/*UPDATE COUNT COMPETITORS*/
UPDATE categories c SET count_competitors = (
	SELECT COUNT(*) AS total 
	FROM catalogs
	WHERE category_id = c.id
	GROUP BY category_id
) WHERE tournament_id = 2;

/*DROP PREFIX COLUMNN TO ANIMALS*/
ALTER TABLE animals DROP prefix;


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

SELECT * FROM animals WHERE id IN (1856,1946)

SELECT * FROM animal_agent WHERE animal_id IN (1856,1946)

SELECT * FROM temp1 WHERE NAME IN ('II CALAMBUCO','MULATO')

SELECT * FROM agents WHERE NAMES IN ('WILFREDO MONTALVO BERNILLA','CARLOS RAMIREZ CHUPICA')



SELECT * FROM animal_tournament WHERE tournament_id = 2 AND prefix IS NULL

INSERT INTO agents (NAMES, prefix, created_at, updated_at)
SELECT a.*, b.prefix, CURRENT_TIMESTAMP AS created_at, CURRENT_TIMESTAMP AS updated_at
FROM (SELECT NAMES FROM (
SELECT breeder AS NAMES FROM temp1
UNION ALL 
SELECT OWNER AS NAMES FROM temp1) a
WHERE NAMES NOT IN (
SELECT NAMES FROM agents
) GROUP BY NAMES) a
INNER JOIN temp1 b ON b.breeder = a.names
GROUP BY a.names;

SELECT * FROM temp1 WHERE breeder='CARLOS CORNO YORI'

DELETE FROM animal_agent
WHERE animal_id IN (
SELECT animal_id FROM temp2)

INSERT INTO animal_agent (animal_id, agent_id, TYPE)
SELECT a.animal_id, b.id, 'owner' AS TYPE FROM (
SELECT a.animal_id, b.owner FROM (
SELECT * 
FROM animal_tournament 
WHERE tournament_id = 2 AND prefix IS NULL
AND NAME IN (
SELECT NAME FROM temp1
)) a
INNER JOIN temp1 b ON b.code = a.code
GROUP BY a.name) a
INNER JOIN agents b WHERE b.names = a.owner;

UPDATE users SET login = 0

INSERT INTO animal_agent (animal_id, agent_id, TYPE)
SELECT a.animal_id, b.id, 'breeder' AS TYPE FROM (
SELECT a.animal_id, b.breeder FROM (
SELECT * 
FROM animal_tournament 
WHERE tournament_id = 2 AND prefix IS NULL
AND NAME IN (
SELECT NAME FROM temp1
)) a
INNER JOIN temp1 b ON b.code = a.code
GROUP BY a.name) a
INNER JOIN agents b WHERE b.names = a.breeder;

SELECT * FROM animal_tournament WHERE tournament_id = 2

SELECT * FROM catalogs WHERE tournament_id = 2 AND number = 183
SELECT * FROM animal_agent WHERE animal_id = 4150
SELECT * FROM agents WHERE NAMES LIKE '%CORNO%'

SELECT * FROM temp1 WHERE breeder = 'CARLOS CORNO YORI'


SELECT * FROM categories WHERE id = 102

SELECT * FROM competitors WHERE category_id=112
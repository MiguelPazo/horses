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
);

SELECT COUNT(*) FROM temp1

/*ONLY FOR TOURNAMENT 3*/
UPDATE agents SET NAMES = 'HACIENDA HUAMANI' WHERE NAMES = 'SALVADOR GUTIERREZ BENAVIDES';
UPDATE temp1 SET breeder = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A.' WHERE breeder = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A';
UPDATE temp1 SET OWNER = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A.' WHERE OWNER = 'CRIADERO JOSE ANTONIO ONRUBIA ROMERO S.A';
UPDATE temp1 SET breeder = 'LUIS JOSE SAENZ RAEZ' WHERE breeder = 'LUIS JOSE  SAENZ RAEZ';
UPDATE temp1 SET OWNER = 'LUIS JOSE SAENZ RAEZ' WHERE OWNER = 'LUIS JOSE  SAENZ RAEZ';
UPDATE temp1 SET breeder = 'EDUARDO EDGARDO CHAMAN COMMOTTO' WHERE breeder = 'EDUARDO EDGARDO CHAMAN COMOTTO';
UPDATE temp1 SET OWNER = 'EDUARDO EDGARDO CHAMAN COMMOTTO' WHERE OWNER = 'EDUARDO EDGARDO CHAMAN COMOTTO';
UPDATE temp1 SET breeder = 'JUAN LUIS KRUGER CARRION' WHERE breeder = 'JUAN LUIS KRUGER';
UPDATE temp1 SET OWNER = 'JUAN LUIS KRUGER CARRION' WHERE OWNER = 'JUAN LUIS KRUGER';
UPDATE temp1 SET OWNER = 'MANUEL ANTONIO MANRIQUE FIGUEROA' WHERE OWNER = 'MANUEL MANRIQUE FIGUEROA';
UPDATE temp1 SET breeder = 'MANUEL ANTONIO MANRIQUE FIGUEROA' WHERE breeder = 'MANUEL MANRIQUE FIGUEROA';
UPDATE temp1 SET OWNER = 'CARLOS CORNEJO BUSTILLO' WHERE OWNER = 'CARLOS CORNEJO BUSTILLO.';
UPDATE temp1 SET breeder = 'CARLOS CORNEJO BUSTILLO' WHERE breeder = 'CARLOS CORNEJO BUSTILLO.';
UPDATE agents SET NAMES = 'CARLOS CORNEJO BUSTILLO' WHERE NAMES = 'CARLOS CORNEJO BUSTILLO.';
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


/*VALIDATE BREEDER WITH DIFF NAME IN TEMP1*/
SELECT a.* FROM (
SELECT * FROM (
SELECT b.id AS animal_id, c.id AS agent_id, 'owner' AS TYPE
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
INNER JOIN agents c ON c.names = a.owner
GROUP BY b.id
) c WHERE CONCAT(animal_id, agent_id, TYPE) NOT IN (SELECT 
CONCAT(animal_id, agent_id, TYPE) FROM animal_agent)
) a
WHERE animal_id IN (SELECT animal_id FROM animal_agent WHERE `type`='owner');

/*HARDCODE - NO TOCAR HASTA ANALIZAR*/
3991-1728, 4208-2188, 4286-1989, 4208-2187

DELETE FROM animal_agent WHERE animal_id IN (3991, 4208, 4286) AND TYPE = 'owner';
INSERT INTO animal_agent (animal_id, agent_id, TYPE) VALUES (3991, 1728, 'owner');
INSERT INTO animal_agent (animal_id, agent_id, TYPE) VALUES (4208, 2188, 'owner');
INSERT INTO animal_agent (animal_id, agent_id, TYPE) VALUES (4286, 1989, 'owner');


SELECT * FROM animal_agent WHERE animal_id = 4208
SELECT * FROM animal_report WHERE id = 4208
SELECT * FROM temp1 WHERE NAME='CINEASTA - TE'

SELECT * FROM agents WHERE NAMES='FLAVIO ALBERTO CARRILLO NARANJO'

2193

FLAVIO ALBERTO CARRILLO NARANJO



KVC CONTRATISTAS S.A.C.
FLAVIO ALBERTO CARRILLO NARANJO

/*********************/

/*PENDIENTE ANALZIZAR  - CAMBIO DE OWNER*/
SELECT a.prefix, a.name, a.owner, b.id, b.name, c.names, c.prefix
FROM temp1 a
LEFT JOIN animals b ON b.name = a.name AND b.prefix = a.prefix
LEFT JOIN agents c ON c.names = a.owner
WHERE a.owner <> c.names
GROUP BY b.id;
/*****************/

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

/*INSERT CATALOGS*/
INSERT INTO catalogs(`group`, number, category_id, tournament_id, animal_id)
SELECT number AS 'group', catalog AS number, category AS category_id, 3 AS tournament_id, b.id AS animal_id
FROM temp1 a
INNER JOIN animals b ON b.name = a.name AND b.prefix = a.prefix;

/*UPDATE COUNT COMPETITORS*/
UPDATE categories c SET count_competitors = (
	SELECT COUNT(*) AS total 
	FROM catalogs
	WHERE category_id = c.id
	GROUP BY category_id
) WHERE tournament_id = 3;


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
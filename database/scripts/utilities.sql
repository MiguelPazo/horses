/*************************************************************/
/**********************RESETEAR CATEGORIA*********************/
SELECT * FROM categories WHERE tournament_id = 3
AND description LIKE '%MULET%';

DELETE FROM stages WHERE category_id = 122;

-- CON SELECCION => 'assistance'
-- SIN SELECCION => 'selection'
UPDATE categories SET STATUS = 'active', actual_stage = 'assistance'
WHERE id = 122;

UPDATE category_users SET actual_stage = 'assistance' 
WHERE category_id = 122;

-- CON SELECCION => POSITION = null
-- SIN SELECCION => POSITION = 0 
UPDATE competitors SET points = NULL, STATUS = IF(STATUS = 'limp','present', STATUS), POSITION = NULL
WHERE category_id = 122;


/*************************************************************/
/******************VER ESTADO DE CADA JURADO******************/
SELECT * FROM category_users WHERE category_id = 123


SELECT * FROM agents WHERE NAMES LIKE 'ARTURO GILMER%' 2146
4708
SELECT * FROM animal_agent WHERE animal_id = 4708 OWNER
SELECT * FROM catalogs WHERE category_id = 122

INSERT INTO animal_agent (animal_id,agent_id,TYPE ) VALUES (4708,2146,'owner')

SELECT * FROM competitors WHERE category_id=124
SELECT * FROM categories WHERE id=122


122
catalog
1 - 5
2 - 6
3- 7
4- 8
5 - 9


 UPDATE categories SET STATUS ='inactive' WHERE id=122
 
 

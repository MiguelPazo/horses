/*************************************************************/
/**********************RESETEAR CATEGORIA*********************/
SELECT * FROM categories WHERE tournament_id = 4
AND description LIKE '%PREMIO PISOS PARA CAPONES%';

DELETE FROM stages WHERE category_id = 211;

-- CON SELECCION => null
-- SIN SELECCION => 'selection'
UPDATE categories SET STATUS = 'inactive', actual_stage = NULL
WHERE id = 211;

UPDATE category_users SET actual_stage = NULL 
WHERE category_id = 211;

-- CON SELECCION => POSITION = null
-- SIN SELECCION => POSITION = 0 
UPDATE competitors SET points = NULL, STATUS = IF(STATUS = 'limp','present', STATUS), POSITION = NULL
WHERE category_id = 211;

DELETE FROM competitors WHERE category_id = 211;
DELETE FROM stages WHERE category_id = 211;


/*************************************************************/
/******************VER ESTADO DE CADA JURADO******************/
SELECT a.dirimente, a.actual_stage, b.names, b.lastname
FROM category_users a
INNER JOIN users b ON b.id = a.user_id
WHERE category_id = 206



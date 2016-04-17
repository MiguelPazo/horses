/*************************************************************/
/**********************RESETEAR CATEGORIA*********************/
SELECT * FROM categories WHERE tournament_id = 3
AND description LIKE '%POTRANCAS AL CABESTRO%';

DELETE FROM stages WHERE category_id = 127;

-- CON SELECCION => 'assistance'
-- SIN SELECCION => 'selection'
UPDATE categories SET STATUS = 'active', actual_stage = 'assistance'
WHERE id = 127;

UPDATE category_users SET actual_stage = 'assistance' 
WHERE category_id = 127;

-- CON SELECCION => POSITION = null
-- SIN SELECCION => POSITION = 0 
UPDATE competitors SET points = NULL, STATUS = IF(STATUS = 'limp','present', STATUS), POSITION = NULL
WHERE category_id = 127;


/*************************************************************/
/******************VER ESTADO DE CADA JURADO******************/
SELECT * FROM category_users WHERE category_id = 127

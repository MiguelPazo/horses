SELECT s.*, c.number
FROM stages s
INNER JOIN competitors c ON c.id = s.competitor_id
WHERE s.category_id = 8
AND s.stage = 'classify_1'
AND s.user_id = 11;

SELECT s.competitor_id, c.number, SUM(s.position) POSITION
FROM stages s
INNER JOIN competitors c ON c.id = s.competitor_id
WHERE s.category_id = 8
AND s.stage = 'classify_1'
GROUP BY s.competitor_id
ORDER BY SUM(s.position);

SELECT * FROM competitors
WHERE id = 42;

SELECT * FROM competitors WHERE category_id = 8;

SELECT s.*, c.number 
FROM stages s
INNER JOIN competitors c ON c.id = s.competitor_id
WHERE s.category_id = 8 AND s.stage = 'classify_1'
ORDER BY s.competitor_id;

/**
Script para resetear un paso
**/
UPDATE categories SET STATUS = 'in_progress', actual_stage = 'selection' WHERE id = 8;
UPDATE category_users SET actual_stage = 'selection' WHERE id = 52;
UPDATE stages SET STATUS = 'active' WHERE stage ='classify_1' AND category_id = 8;
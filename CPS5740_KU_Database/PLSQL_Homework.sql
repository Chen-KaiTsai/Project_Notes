USE 2021F_tsaiche;

DELIMITER //
CREATE PROCEDURE pHW2_tsaiche(IN sid_in VARCHAR(10))
	BEGIN
		DECLARE check_exist INT DEFAULT NULL;
        DECLARE finished INT DEFAULT 0;
        DECLARE current_sid INT DEFAULT NULL;
        DECLARE student_count INT DEFAULT 0;
        DECLARE total_amount FLOAT DEFAULT 0.0;
        DECLARE cur_sid CURSOR FOR  
			SELECT sid FROM dreamhome.Students;
		DECLARE CONTINUE HANDLER
		FOR NOT FOUND SET finished = 1; 
    
		-- CREATE TEMPORARY TABLE HERE
        CREATE TEMPORARY TABLE students_transcript (
			year INT, 
			semester VARCHAR(255), 
			cid VARCHAR(255), 
			name VARCHAR(255), 
			credits INT, 
			grade CHAR(5), 
			expense_per_course VARCHAR(255)
		);
    
		IF (sid_in IS NULL OR sid_in = '') THEN
			SELECT 'Please input a vaild student id' AS Report;
		ELSEIF (sid_in = 'All') THEN
			OPEN cur_sid;
            
            get_sid: LOOP
				FETCH cur_sid INTO current_sid;
				
                IF finished = 1 THEN 
					LEAVE get_sid;
				END IF;
                
				SET student_count = student_count + 1;
				CALL generate_student_transcript(student_count, current_sid);
                INSERT INTO students_transcript VALUE (NULL, NULL, NULL, NULL, NULL, NULL, NULL);
            END LOOP get_sid;
            
            SELECT sum(expense_per_course) INTO total_amount FROM students_transcript WHERE semester LIKE 'SID:%';
            INSERT INTO students_transcript VALUE (NULL, "  ", concat(student_count, " students"), 'total_amount', NULL, NULL, total_amount);
            SELECT * FROM students_transcript;
            
		ELSE
			SET sid_in = CAST(sid_in AS SIGNED);
            SELECT sid INTO check_exist FROM dreamhome.Students WHERE sid = sid_in;
            IF (check_exist IS NULL) THEN
				SELECT concat("No such student ", sid_in, " in the system!") AS Report;
			ELSE
				-- Call procedure to generate transcript for one student
				CALL generate_student_transcript(1, sid_in);
				SELECT * FROM students_transcript;
			END IF;
		END IF;
        DROP TABLE students_transcript;
	END //
DELIMITER ;

DROP PROCEDURE pHW2_tsaiche;

-- SELECT STATEMENT
/*
SELECT sc.year, semester, sc.cid, name, credits, CAST(grade AS CHAR(5)) AS grade, fee_per_credit * credits AS expense_per_course
FROM dreamhome.Students_Courses sc, dreamhome.Courses c, dreamhome.Tuitions t
WHERE sc.cid = c.cid AND sc.year = t.year AND sid = 1001
ORDER BY sc.year ASC, semester DESC;
*/
CALL pHW2_tsaiche('1003');

DELIMITER //
CREATE PROCEDURE generate_student_transcript(IN num_student INT, IN sid_in INT)
	BEGIN
		DECLARE student_id VARCHAR(255);
		DECLARE student_name VARCHAR(255);
        DECLARE credit_sum INT;
        DECLARE GPA FLOAT;
        DECLARE expense_sum FLOAT;
		
        -- Insert courses into transcript table
		INSERT INTO students_transcript 
        (year, semester, cid, name, credits, grade, expense_per_course)
        (SELECT sc.year, semester, sc.cid, name, credits, grade, fee_per_credit * credits AS expense_per_course
		FROM dreamhome.Students_Courses sc, dreamhome.Courses c, dreamhome.Tuitions t
		WHERE sc.cid = c.cid AND sc.year = t.year AND sid = sid_in
		ORDER BY sc.year ASC, semester DESC);
        
        -- Calculate ID
        SELECT concat("SID: ", sid) INTO student_id FROM dreamhome.Students WHERE sid = sid_in;
        
        -- Calculate name
        SELECT concat("name: ", first_name, " " , last_name) INTO student_name FROM dreamhome.Students WHERE sid = sid_in;
        
        -- Calculate credit_sum
        SELECT sum(credits) INTO credit_sum FROM dreamhome.Courses, dreamhome.Students_Courses WHERE dreamhome.Courses.cid = dreamhome.Students_Courses.cid AND sid = sid_in;
        
        -- Calculate GPA
        SELECT sum(CASE Grade 
				WHEN 'A' Then 4
				WHEN 'A-' THEN 3.7
				WHEN 'B+' THEN 3.3
				WHEN 'B' THEN 3
				WHEN 'C+' THEN 2.3
				WHEN 'C' THEN 2
				WHEN 'D' THEN 1
				WHEN 'F' THEN 0 END * credits) / credit_sum INTO GPA
		FROM dreamhome.Students_Courses, dreamhome.Courses
		WHERE dreamhome.Courses.cid = dreamhome.Students_Courses.cid AND sid = sid_in;
        
        -- Calculate expense_sum
        SELECT sum(fee_per_credit * credits) INTO expense_sum
		FROM dreamhome.Students_Courses sc, dreamhome.Courses c, dreamhome.Tuitions t
		WHERE sc.cid = c.cid AND sc.year = t.year AND sid = sid_in; 
        
        INSERT INTO students_transcript (year, semester, cid, name, credits, grade, expense_per_course) VALUE
        (num_student, student_id, student_name, 'Amount' ,credit_sum, GPA, expense_sum);
	END //
DELIMITER ;

DROP PROCEDURE generate_student_transcript;
CALL generate_student_transcript(1, 1001);

CREATE TEMPORARY TABLE students_transcript (
	year INT, 
    semester VARCHAR(255), 
    cid VARCHAR(255), 
    name VARCHAR(255), 
    credits INT, 
    grade CHAR(5), 
    expense_per_course VARCHAR(255)
);

/*
SELECT concat("ID: ", sid) FROM Students WHERE sid = 1001;

SELECT sum(credits) FROM Courses, Students_Courses WHERE Courses.cid = Students_Courses.cid AND sid = 1001;

SELECT concat("name: ", first_name, " " , last_name) FROM Students WHERE sid = 1001;

SELECT sc.year, semester, sc.cid, name, credits, grade, fee_per_credit * credits AS expense_per_course
FROM dreamhome.Students_Courses sc, dreamhome.Courses c, dreamhome.Tuitions t
WHERE sc.cid = c.cid AND sc.year = t.year AND sid = 1001
ORDER BY sc.year ASC, semester DESC;
        
SELECT sum(fee_per_credit * credits) AS expense_per_course
FROM dreamhome.Students_Courses sc, dreamhome.Courses c, dreamhome.Tuitions t
WHERE sc.cid = c.cid AND sc.year = t.year AND sid = 1001;

SELECT * FROM Students_Courses WHERE sid = 1001;

SELECT sum(CASE Grade 
		WHEN 'A' Then 4
		WHEN 'A-' THEN 3.7
		WHEN 'B+' THEN 3.3
		WHEN 'B' THEN 3
		WHEN 'C+' THEN 2.3
		WHEN 'C' THEN 2
		WHEN 'D' THEN 1
		WHEN 'F' THEN 0 END) / COUNT(*) AS GPA
FROM Students_Courses
WHERE sid = 1001;
*/
SELECT * FROM students_transcript;

DROP TABLE students_transcript;

CREATE TABLE HW2_test1(
	id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    price FLOAT,
    qty INT
);

DROP TABLE HW2_test1;

CREATE TABLE HW2_test_audit(
	id INT PRIMARY KEY AUTO_INCREMENT,
    tid INT,
    user VARCHAR(255),
    access_time TIMESTAMP,
    old_price FLOAT,
    new_price FLOAT,
    note VARCHAR(255)
) ENGINE = MYISAM;

DESC HW2_test_audit;

DROP TABLE HW2_test_audit;

SELECT SUBSTRING_INDEX(USER(), '@', 1);
SELECT CURRENT_TIMESTAMP;

DELIMITER //
CREATE TRIGGER audit_insert 
AFTER INSERT ON HW2_test1 FOR EACH ROW
BEGIN
	INSERT INTO HW2_test_audit (tid, user, access_time, old_price, new_price, note)
    VALUES (
		NEW.id, SUBSTRING_INDEX(USER(), '@', 1), CURRENT_TIMESTAMP, NULL, NEW.price, 'after insert operation'
    );
END //
DELIMITER ;

DROP TRIGGER audit_insert;

INSERT INTO HW2_test1 (name, price, qty)
VALUES (
	'Fisrt item', 100, 5
);


DELIMITER //
CREATE TRIGGER audit_delete BEFORE DELETE ON HW2_test1 FOR EACH ROW
BEGIN
    DECLARE message VARCHAR(255) DEFAULT concat('Cannot delete the item ', OLD.name);
	SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = message;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER audit_update
BEFORE UPDATE ON HW2_test1 FOR EACH ROW
BEGIN
	DECLARE message VARCHAR(255) DEFAULT concat('Cannot update the name');
	IF(NEW.name = OLD.name) THEN
		INSERT INTO HW2_test_audit (tid, user, access_time, old_price, new_price, note)
		VALUES (
			NEW.id, SUBSTRING_INDEX(USER(), '@', 1), CURRENT_TIMESTAMP, OLD.price, NEW.price, concat('update item ', OLD.name)
		);
	ELSE
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = message;
    END IF;
END //
DELIMITER ;

CREATE TABLE Shape (
	id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    g geometry NOT NULL
);

DROP TABLE Shape;

/*

SELECT ST_PolygonFromText('polygon((0 0, 10 0, 10 10, 0 10, 0 0))');

SELECT name FROM Shape
WHERE ST_CONTAINS(g, POINT(1, 1));

SELECT group_concat(name) FROM Shape
WHERE ST_CONTAINS(g, POINT(1, 1));

INSERT INTO Shape (name, g) VALUES ('Test1', ST_PolygonFromText('polygon((0 0, 10 0, 10 10, 0 10, 0 0))'));
INSERT INTO Shape (name, g) VALUES ('Test2', ST_PolygonFromText('polygon((0 0, 100 0, 100 100, 0 100, 0 0))'));

SELECT * FROM Shape;

*/

DELIMITER //
CREATE FUNCTION fHW2_Inside_Polygon(x INT, y INT) RETURNS VARCHAR(125)
	BEGIN
		DECLARE return_str VARCHAR(125);
        DECLARE temp_str VARCHAR(125);
		
        SELECT group_concat(name) INTO temp_str FROM Shape
		WHERE ST_CONTAINS(g, POINT(x, y));
        
        IF (temp_str IS NULL) THEN
			SET return_str = 'No polygon found.';
		ELSE
			SET return_str = temp_str;
		END IF;
        RETURN return_str;
	END //
DELIMITER ;

DROP FUNCTION fHW2_Inside_Polygon;

SELECT fHW2_Inside_Polygon(500, 50);

CREATE TABLE HW2_test2 (
	id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(10),
    salary FLOAT
);

INSERT INTO HW2_test2 (name, salary) VALUES ('Some_guy', 100.5);

CREATE TABLE HW2_Concurrency (
	Time_id INT,
    T1 VARCHAR(255),
    T2 VARCHAR(255)
);

DROP TABLE HW2_Concurrency_tsaiche;

INSERT INTO HW2_Concurrency (Time_id, T1, T2) 
VALUES
(1, "INSERT INTO HW2_test2 (name, salary) VALUES ('Some_guy', 100.5);", NULL),
(2, "START TRANSACTION;", NULL),
(3, "SELECT * FROM HW2_test2 WHERE id=1 LOCK IN SHARE MODE;", NULL),
(4, NULL, "START TRANSACTION"),
(5, NULL, "DELETE FROM HW2_test2 WHERE id = 1;"),
(6, "DELETE FROM HW2_test2 WHERE id = 1 Query OK, 1 row affected (0.006 sec);", NULL),
(7, NULL, "ERROR 1213 (40001): Deadlock found when trying to get lock; try restarting transaction");
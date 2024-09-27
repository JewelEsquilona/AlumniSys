//Database Creation
CREATE TABLE `2024_2025` (
    `ID` INT AUTO_INCREMENT,                      -- Auto-incrementing ID
    `Alumni_ID_Number` VARCHAR(20),               -- Primary key
    `Student_Number` VARCHAR(20) NOT NULL,
    `Last_Name` VARCHAR(50) NOT NULL,
    `First_Name` VARCHAR(50) NOT NULL,
    `Middle_Name` VARCHAR(50),
    `Course` VARCHAR(100) NOT NULL,               -- New column for Course
    `Department_Program` VARCHAR(100) NOT NULL,   -- Renamed column (avoid using '/' in column names)
    `Section` VARCHAR(50) NOT NULL,               -- New column for Section
    `Year_Graduated` YEAR NOT NULL, 
    `Contact_Number` VARCHAR(15) NOT NULL,
    `Personal_Email` VARCHAR(100),
    PRIMARY KEY (`Alumni_ID_Number`),             -- Alumni_ID_Number as primary key
    UNIQUE (`ID`)                                 -- ID as unique key for auto-increment
);


DELIMITER $$

CREATE TRIGGER `before_insert_alumni_id`
BEFORE INSERT ON `2024_2025`
FOR EACH ROW
BEGIN
  -- Find the maximum existing Alumni_ID_Number and increment it
  DECLARE max_id INT;
  SELECT COALESCE(MAX(CAST(`Alumni_ID_Number` AS UNSIGNED)), 0) INTO max_id FROM `2024_2025`;
  SET NEW.`Alumni_ID_Number` = LPAD(max_id + 1, 5, '0');
END $$

DELIMITER ;


CREATE TABLE `2024_2025_WS` (
    `ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Alumni_ID_Number` VARCHAR(20),
    `Working_Status` VARCHAR(50) DEFAULT NULL,     -- Default value set to NULL
    FOREIGN KEY (`Alumni_ID_Number`) REFERENCES `2024_2025`(`Alumni_ID_Number`) ON DELETE CASCADE ON UPDATE CASCADE
);


DELIMITER $$

CREATE TRIGGER `after_insert_2024_2025`
AFTER INSERT ON `2024_2025`
FOR EACH ROW
BEGIN
    -- Insert a new record into 2024_2025_WS with the new Alumni_ID_Number and default Working_Status as NULL
    INSERT INTO `2024_2025_WS` (`Alumni_ID_Number`, `Working_Status`)
    VALUES (NEW.`Alumni_ID_Number`, NULL);
END $$

DELIMITER ;


INSERT INTO `2024_2025` 
(`Student_Number`, `Last_Name`, `First_Name`, `Middle_Name`, `Course`, `Department_Program`, `Section`, `Year_Graduated`, `Contact_Number`, `Personal_Email`) 
VALUES 
('STU12345', 'Doe', 'John', 'A', 'Computer Science', 'CS Department', 'Section A', 2023, '1234567890', 'john.doe@example.com');

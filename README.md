//Database Creation




CREATE TABLE `2024-2025` (
    ID INT AUTO_INCREMENT,  -- Auto-incrementing ID
    Alumni_ID_Number VARCHAR(20) NOT NULL,  -- Primary key
    Student_Number VARCHAR(20) NOT NULL,
    Last_Name VARCHAR(50) NOT NULL,
    First_Name VARCHAR(50) NOT NULL,
    Middle_Name VARCHAR(50),  -- Nullable middle name
    College VARCHAR(100) NOT NULL,  -- New column for Course
    Department VARCHAR(100) NOT NULL,  -- Renamed column (avoid using '/' in column names)
    Section VARCHAR(50) NOT NULL,  -- New column for Section
    Year_Graduated YEAR NOT NULL,
    Contact_Number VARCHAR(15) NOT NULL,
    Personal_Email VARCHAR(100),
    PRIMARY KEY (Alumni_ID_Number),  -- Alumni_ID_Number as primary key
    UNIQUE (ID)  -- ID as a unique key for auto-increment
);






DELIMITER $$


CREATE TRIGGER before_insert_alumni_id 
BEFORE INSERT ON `2024-2025` 
FOR EACH ROW 
BEGIN
    -- Find the maximum existing Alumni_ID_Number and increment it
    DECLARE max_id INT;
    SELECT COALESCE(MAX(CAST(Alumni_ID_Number AS UNSIGNED)), 0) INTO max_id FROM `2024-2025`;
    SET NEW.Alumni_ID_Number = LPAD(max_id + 1, 5, '0');
END $$


DELIMITER ;



CREATE TABLE `2024-2025_ED` (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Alumni_ID_Number VARCHAR(20),
    Working_Status VARCHAR(50) DEFAULT NULL, -- Default value set to NULL
    Employment_Status VARCHAR(50),
    Present_Occupation VARCHAR(100),
    Name_of_Employer VARCHAR(100),
    Address_of_Employer VARCHAR(255),
    Number_of_Years_in_Present_Employer INT,
    Type_of_Employer VARCHAR(100),
    Major_Line_of_Business VARCHAR(100),
    FOREIGN KEY (Alumni_ID_Number) REFERENCES `2024-2025`(Alumni_ID_Number) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);



DELIMITER $$

CREATE TRIGGER after_insert_2024_2025 
AFTER INSERT ON `2024-2025` 
FOR EACH ROW 
BEGIN
    -- Insert a new record into 2024-2025_ED with the new Alumni_ID_Number and default Working_Status as NULL
    INSERT INTO `2024-2025_ED` (
        Alumni_ID_Number, 
        Working_Status, 
        Employment_Status, 
        Present_Occupation, 
        Name_of_Employer, 
        Address_of_Employer, 
        Number_of_Years_in_Present_Employer, 
        Type_of_Employer, 
        Major_Line_of_Business
    ) 
    VALUES (
        NEW.Alumni_ID_Number, 
        NULL,  -- Default Working_Status
        NULL,  -- Default Employment_Status
        NULL,  -- Default Present_Occupation
        NULL,  -- Default Name_of_Employer
        NULL,  -- Default Address_of_Employer
        NULL,  -- Default Number_of_Years_in_Present_Employer
        NULL,  -- Default Type_of_Employer
        NULL   -- Default Major_Line_of_Business
    );
END $$

DELIMITER ;








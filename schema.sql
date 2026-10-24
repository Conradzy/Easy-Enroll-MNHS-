-- Database schema for EMS (Enrollment Management System)
-- Generated based on PHP code analysis

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS ems;
USE ems;


CREATE TABLE studentaccount (
    studentacc_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    studentacc_username VARCHAR(255) NOT NULL UNIQUE,
    studentacc_password VARCHAR(255) NOT NULL,
    studentacc_email VARCHAR(255) NOT NULL UNIQUE,
    enrollmentstatus VARCHAR(50) DEFAULT 'pending'
);


CREATE TABLE admin (
    admin_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(255) NOT NULL UNIQUE,
    admin_password VARCHAR(255) NOT NULL
);


CREATE TABLE lpi (
    lpi_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    profile_picture VARCHAR(255),
    school_year VARCHAR(50),
    lrn INT(50),
    gradelvl_enroll INT(50),
    psa_no INT(50),
    psa_picture VARCHAR(255),
    birthdate DATE,
    age INT,
    sex VARCHAR(10),
    pob VARCHAR(255),
    religion VARCHAR(100),
    fname VARCHAR(100),
    mname VARCHAR(100),
    lname VARCHAR(100),
    extname VARCHAR(50),
    mother_tongue VARCHAR(100),
    ip VARCHAR(255),
    4ps VARCHAR(255),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);


CREATE TABLE current_address (
    currentadd_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    currenthouse_no INT(100),
    current_streetname VARCHAR(255),
    current_barangay VARCHAR(100),
    current_municipality VARCHAR(100),
    current_province VARCHAR(100),
    current_country VARCHAR(100),
    current_zipcode INT(20),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);


CREATE TABLE permanent_address (
    perm_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    permhouse_no INT(100),
    perm_streetname VARCHAR(255),
    perm_barangay VARCHAR(100),
    perm_municipality VARCHAR(100),
    perm_province VARCHAR(100),
    perm_country VARCHAR(100),
    perm_zipcode INT(20),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);


CREATE TABLE parent_info (
    parent_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    father_fname VARCHAR(100),
    father_mname VARCHAR(100),
    father_lname VARCHAR(100),
    mother_fname VARCHAR(100),
    mother_mname VARCHAR(100),
    mother_lname VARCHAR(100),
    guardian_fname VARCHAR(100),
    guardian_mname VARCHAR(100),
    guardian_lname VARCHAR(100),
    fathercontact_no INT(20),
    mothercontact_no INT(20),
    guardiancontact_no INT(20),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);


CREATE TABLE sne (
    sne_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    type_disability VARCHAR(50),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);

CREATE TABLE transferee (
    trans_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    last_gradelevel INT(50),
    last_sycompleted VARCHAR(50),
    last_schoolattended VARCHAR(255),
    last_schoolid INT(20),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);


CREATE TABLE lshs (
    shs_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    shs_semester VARCHAR(50),
    shs_track VARCHAR(100),
    shs_strand VARCHAR(100),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);

CREATE TABLE learning_modalities (
    lm_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    learning_mod VARCHAR(255),
    studentacc_id INT(10),
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE
);

CREATE TABLE pending_forms (
    pending_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    pending_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    currentadd_id INT(10),
    lm_id INT(10),
    lpi_id INT(10),
    shs_id INT(10),
    parent_id INT(10),
    perm_id INT(10),
    sne_id INT(10),
    studentacc_id INT(10),
    trans_id INT(10),
    FOREIGN KEY (currentadd_id) REFERENCES current_address(currentadd_id) ON DELETE CASCADE,
    FOREIGN KEY (lm_id) REFERENCES learning_modalities(lm_id) ON DELETE CASCADE,
    FOREIGN KEY (lpi_id) REFERENCES lpi(lpi_id) ON DELETE CASCADE,
    FOREIGN KEY (shs_id) REFERENCES lshs(shs_id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES parent_info(parent_id) ON DELETE CASCADE,
    FOREIGN KEY (perm_id) REFERENCES permanent_address(perm_id) ON DELETE CASCADE,
    FOREIGN KEY (sne_id) REFERENCES sne(sne_id) ON DELETE CASCADE,
    FOREIGN KEY (studentacc_id) REFERENCES studentaccount(studentacc_id) ON DELETE CASCADE,
    FOREIGN KEY (trans_id) REFERENCES transferee(trans_id) ON DELETE CASCADE
);

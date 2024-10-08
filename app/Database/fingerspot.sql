CREATE TABLE salary_det (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    day VARCHAR(50) DEFAULT NULL,
    payperhour INT(11) DEFAULT NULL,
    basic_salary INT(11) DEFAULT NULL,
    work_start TIME DEFAULT NULL,
    work_end TIME DEFAULT NULL,
    overtime_start TIME DEFAULT NULL,
    overtime_end TIME DEFAULT NULL,
    work_break TIME DEFAULT NULL,
    overtime_break TIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE employee_category (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE salary_allowance (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) DEFAULT NULL,
    allowance INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE salary_deduction (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) DEFAULT NULL,
    deduction INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE bank_account (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(50) DEFAULT NULL,
    bank VARCHAR(50) DEFAULT NULL,
    account_number VARCHAR(50) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE salary_pattern (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pattern_code VARCHAR(50) DEFAULT NULL,
    employee_cat_id INT(10) DEFAULT NULL,
    pattern_name VARCHAR(50) DEFAULT NULL,
    salary_det_id INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE master_salary (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) DEFAULT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE master_salary_detail (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_master_salary INT(11) DEFAULT NULL,
    id_employee INT(11) DEFAULT NULL,
    id_salary_det INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE salary_pattern_employee (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_salary_pattern INT(11) DEFAULT NULL,
    id_employee INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE employee_allowance_list (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT(11) DEFAULT NULL,
    allowance_id INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

CREATE TABLE employee_deduction_list (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT(11) DEFAULT NULL,
    deduction_id INT(11) DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT NULL
);

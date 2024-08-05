--  Advance Deduction Table
ALTER TABLE `advance_deduction`
ADD `advancededuction_name` VARCHAR(255) NULL
AFTER `no_of_month_to_be_deducted`;
ALTER TABLE `advance_deduction` CHANGE `advance_amount` `advance_amount` DECIMAL(10, 2) NOT NULL,
    CHANGE `deduction_amouth_per_month` `deduction_amouth_per_month` DECIMAL(10, 2) NOT NULL;
--  Employee Table
ALTER TABLE `employee`
ADD `basic_salary` DOUBLE NULL
AFTER `annual_leave`,
    ADD `increment` DOUBLE NULL
AFTER `basic_salary`,
    ADD `housing_allowance` DOUBLE NULL
AFTER `increment`,
    ADD `utility_allowance` DOUBLE NULL
AFTER `housing_allowance`,
    ADD `transport_allowance` FLOAT NULL
AFTER `utility_allowance`,
    ADD `living_allowance` DOUBLE NULL
AFTER `transport_allowance`,
    ADD `mobile_allowance` DOUBLE NULL
AFTER `living_allowance`,
    ADD `special_allowance` DOUBLE NULL
AFTER `mobile_allowance`;
--  Employee Table
ALTER TABLE `employee`
ADD `gross_salary` DOUBLE NULL
AFTER `special_allowance`,
    ADD `social_security` DOUBLE NULL
AFTER `gross_salary`,
    ADD `total_salary` DOUBLE NULL
AFTER `social_security`,
    ADD `net_salary` DOUBLE NULL
AFTER `total_salary`,
    ADD `account_number` VARCHAR(50) NULL
AFTER `net_salary`,
    ADD `ifsc_number` VARCHAR(50) NULL
AFTER `account_number`,
    ADD `name_of_the_bank` VARCHAR(50) NULL
AFTER `ifsc_number`,
    ADD `account_holder` VARCHAR(50) NULL
AFTER `name_of_the_bank`;
--  Employee Table
ALTER TABLE `employee`
ADD `ip_attendance` TINYINT NOT NULL DEFAULT '0'
AFTER `account_holder`;
--- Employee Tbale
ALTER TABLE `employee` ADD `gsm` DOUBLE NULL AFTER `account_holder`, ADD `arrears` DOUBLE NULL AFTER `gsm`;
ALTER TABLE `employee` ADD `prem_others` DOUBLE NULL AFTER `arrears`;

-- Leave Application Table
ALTER TABLE `leave_application` CHANGE `number_of_day` `number_of_day` DOUBLE(5, 2) NOT NULL;
ALTER TABLE `leave_application`
ADD `document` VARCHAR(255) NULL DEFAULT NULL
AFTER `manager_status`;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances`
ADD `branch_id` INT DEFAULT 0;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances`
ADD `department_id` INT DEFAULT 0;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances`
ADD `designation_id` INT DEFAULT 0;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances`
ADD `supervisor_id` INT DEFAULT 0;
-- Employee Leave Balance Table
UPDATE emp_leave_balances AS elb
    JOIN employee AS emp ON elb.finger_id = emp.finger_id
SET elb.department_id = emp.department_id,
    elb.designation_id = emp.designation_id,
    elb.branch_id = emp.branch_id;
-- Employee Leave Balance Table
RENAME TABLE old_table_name TO new_table_name;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances`
ADD `unpaid` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `paternity_leave`;
-- Employee Leave Balance Table
ALTER TABLE `emp_leave_balances` CHANGE `unpaid` `unpaid_leave` DECIMAL(10, 2) NOT NULL DEFAULT '0.00';
-- Menus Table
INSERT INTO `menus` (
        `id`,
        `parent_id`,
        `action`,
        `name`,
        `menu_url`,
        `module_id`,
        `status`
    )
VALUES (
        NULL,
        '0',
        NULL,
        'Leave Balance',
        'leaveBalance.index',
        '3',
        '1'
    );
;
-- Salary Details Table
ALTER TABLE `salary_details`
ADD `increment` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `updated_at`,
    ADD `housing_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `increment`,
    ADD `utility_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `housing_allowance`,
    ADD `transport_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `utility_allowance`,
    ADD `living_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `transport_allowance`,
    ADD `mobile_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `living_allowance`,
    ADD `special_allowance` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `mobile_allowance`,
    ADD `social_security` DECIMAL(10, 2) NOT NULL DEFAULT '0'
AFTER `special_allowance`,
    ADD `account_number` TEXT NOT NULL
AFTER `social_security`,
    ADD `ifsc_number` TEXT NOT NULL
AFTER `account_number`,
    ADD `name_of_the_bank` TEXT NOT NULL
AFTER `ifsc_number`,
    ADD `account_holder` TEXT NOT NULL
AFTER `name_of_the_bank`;

ALTER TABLE `salary_details` ADD `branch_id` INT(11) NULL AFTER `salary_details_id`;



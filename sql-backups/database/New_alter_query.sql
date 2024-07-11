ALTER TABLE `employee`  ADD `education_and_club_allowance` DECIMAL(9,3) NOT NULL DEFAULT '0'  AFTER `ip_attendance`,  ADD `membership_allowance` DECIMAL(9,3) NOT NULL DEFAULT '0'  AFTER `education_and_club_allowance`;
ALTER TABLE `social_security`
ADD COLUMN `employer_contribution` DECIMAL(10,3);

ALTER TABLE `salary_details`  ADD `membership_allowance` DECIMAL(9,3) NOT NULL DEFAULT '0'  AFTER `extra_amount`,  ADD `education_and_club_allowance` DECIMAL(9,3) NOT NULL DEFAULT '0'  AFTER `membership_allowance`;

ALTER TABLE `salary_details`  ADD `employer_contribution` DECIMAL(9,3) NOT NULL DEFAULT '0'  AFTER `education_and_club_allowance`;

INSERT INTO `menus` (`id`, `parent_id`, `action`, `name`, `menu_url`, `module_id`, `status`) VALUES (NULL, '118', NULL, 'Social Security', NULL, '5', '1');
INSERT INTO `menus` (`id`, `parent_id`, `action`, `name`, `menu_url`, `module_id`, `status`) VALUES (NULL, '118', NULL, 'Report', 'socialSecurityReport.index', '5', '1');
INSERT INTO `menus` (`id`, `parent_id`, `action`, `name`, `menu_url`, `module_id`, `status`) VALUES (NULL, '118', NULL, 'Summary', 'socialSecuritySummaryReport.index', '5', '1');

ALTER TABLE `employee` CHANGE `housing_allowance` `housing_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `utility_allowance` `utility_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `transport_allowance` `transport_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `living_allowance` `living_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `mobile_allowance` `mobile_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `special_allowance` `special_allowance` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `gross_salary` `gross_salary` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `total_salary` `total_salary` DECIMAL(10,3) NULL DEFAULT NULL;
ALTER TABLE `employee` CHANGE `increment` `increment` DECIMAL(10,2) NULL DEFAULT NULL;

ALTER TABLE `advance_deduction` CHANGE `advance_amount` `advance_amount` DECIMAL(10,3) NOT NULL;
ALTER TABLE `advance_deduction` CHANGE `deduction_amouth_per_month` `deduction_amouth_per_month` DECIMAL(10,3) NOT NULL;

ALTER TABLE `advance_deduction`  ADD `branch_id` INT NULL  AFTER `updated_at`,  ADD `paid_amount` DECIMAL(10,3) NOT NULL DEFAULT '0'  AFTER `branch_id`,  ADD `pending_amount` DECIMAL(10,3) NOT NULL DEFAULT '0'  AFTER `paid_amount`,  ADD `payment_type` TINYINT NOT NULL DEFAULT '0' COMMENT '0-Bank,1-Cash'  AFTER `pending_amount`,  ADD `created_by` INT NOT NULL  AFTER `payment_type`,  ADD `updated_by` INT NOT NULL  AFTER `created_by`;
ALTER TABLE `advance_deduction` CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '0-Active,1-Hold,2- No due';

CREATE TABLE `advance_deduction_logs` (
  `advance_deduction_log_id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(11) NOT NULL,
  `advance_amount` decimal(10,3) NOT NULL,
  `date_of_advance_given` date COLLATE utf8mb4_unicode_ci NOT NULL,
  `deduction_amouth_per_month` decimal(10,3) NOT NULL,
  `no_of_month_to_be_deducted` int(11) NOT NULL,
  `remaining_month` int(11) DEFAULT NULL,
  `advancededuction_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `paid_amount` decimal(10,3) NOT NULL DEFAULT 0.000,
  `pending_amount` decimal(10,3) NOT NULL DEFAULT 0.000,
  `payment_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0-Bank,1-Cash',
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
);


ALTER TABLE `advance_deduction_logs` CHANGE `advance_deduction_log_id` `advance_deduction_log_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`advance_deduction_log_id`);

ALTER TABLE `advance_deduction_logs` CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '1' COMMENT '0-Active,1-Hold,2- No due';


CREATE TABLE `advance_deduction_transaction` (
  `advance_deduction_transaction_id` int(11) NOT NULL,
  `advance_deduction_log_id` bigint(20) NOT NULL,
  `advance_deduction_id` bigint(20) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  `transaction_date` date NOT NULL,
  `payment_type` int(11) NOT NULL,
  `cash_received` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT NULL,
  `updated_at` timestamp DEFAULT NULL,
);

ALTER TABLE `advance_deduction_transaction`
  ADD PRIMARY KEY (`advance_deduction_transaction_id`);

  ALTER TABLE `advance_deduction_transaction`
  MODIFY `advance_deduction_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

  ALTER TABLE `advance_deduction_transaction` CHANGE `cash_received` `cash_received` DECIMAL(10,3) NOT NULL DEFAULT '0';

CREATE TABLE `otps` (
  `otp_id` int(11) NOT NULL,
  `employee_id` bigint(20) NOT NULL,
  `otp` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);

  ALTER TABLE `otps`
  ADD PRIMARY KEY (`otp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT;

  ALTER TABLE `otps`  ADD `email` TEXT NULL  AFTER `employee_id`;


  ALTER TABLE `approve_over_time` CHANGE `finger_print_id` `finger_print_id` TEXT NULL DEFAULT NULL;
  ALTER TABLE `view_employee_in_out_data`  ADD `approved_over_time` TIME NULL  AFTER `over_time`;

  ALTER TABLE `advance_deduction_logs` ADD `reason` TEXT NULL AFTER `payment_type`;
ALTER TABLE `advance_deduction_logs` ADD `advance_deduction_id` BIGINT NULL AFTER `advance_deduction_log_id`;

INSERT INTO `menus` (`id`, `parent_id`, `action`, `name`, `menu_url`, `module_id`, `status`) VALUES (NULL, '34', NULL, 'Advance Deduction Log', 'advanceDeduction.log', '5', '1');
ALTER TABLE `advance_deduction_logs`  ADD `deleted_by` BIGINT NULL  AFTER `updated_by`;


ALTER TABLE `advance_deduction_logs` CHANGE `created_by` `created_by` TEXT NULL DEFAULT NULL;
ALTER TABLE `advance_deduction_logs` CHANGE `updated_by` `updated_by` TEXT NULL DEFAULT NULL;
ALTER TABLE `advance_deduction_logs` CHANGE `deleted_by` `deleted_by` TEXT NULL DEFAULT NULL;




ALTER TABLE `advance_deduction_transaction` CHANGE `transaction_date` `transaction_date` DATE NOT NULL;

ALTER TABLE `advance_deduction_transaction`  ADD `pending_amount` DECIMAL(10,3) NULL  AFTER `updated_at`,  ADD `remaining_month` INT NULL  AFTER `pending_amount`;



ALTER TABLE `approve_over_time`  ADD `gross_salary` DECIMAL(9,3) NULL  AFTER `updated_at`,  ADD `per_hour_salary` DECIMAL(9,6) NULL  AFTER `gross_salary`,  ADD `over_time_amount` DECIMAL(9,3) NULL  AFTER `per_hour_salary`;
ALTER TABLE `approve_over_time` CHANGE `remark` `remark` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;
ALTER TABLE `approve_over_time` CHANGE `over_time_amount` `over_time_amount` DECIMAL(9,3) NULL DEFAULT '0';


ALTER TABLE `employee`  ADD `status_remark` TEXT NULL  AFTER `membership_allowance`;

ALTER TABLE `employee` ADD `mobile_attendance` INT NULL DEFAULT '0' AFTER `country`;
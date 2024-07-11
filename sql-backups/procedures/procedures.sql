-- Adminer 4.8.1 MySQL 8.4.0 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DELIMITER ;;

DROP PROCEDURE IF EXISTS `SP_calculateEmployeeLeaveBalance`;;
CREATE PROCEDURE `SP_calculateEmployeeLeaveBalance`(IN `employeeId` INT(10), IN `leaveTypeId` INT(10))
BEGIN
           SELECT SUM(number_of_day) AS totalNumberOfDays FROM leave_application WHERE employee_id=employeeId AND leave_type_id=leaveTypeId and status = 2
           AND (approve_date  BETWEEN DATE_FORMAT(NOW(),'%Y-01-01') AND DATE_FORMAT(NOW(),'%Y-12-31'));
          END;;

DROP PROCEDURE IF EXISTS `SP_DailyAttendance`;;
CREATE PROCEDURE `SP_DailyAttendance`(IN `input_date` DATE)
BEGIN
                select employee.employee_id,employee_attendance.uri,employee.photo,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
                view_employee_in_out_data.employee_attendance_id,view_employee_in_out_data.comp_off_details_id,view_employee_in_out_data.approve_over_time_id,view_employee_in_out_data.finger_print_id,view_employee_in_out_data.incentive_details_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,
                DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time,
                TIME_FORMAT( work_shift.late_count_time, '%H:%i:%s' ) as lateCountTime,
                (SELECT CASE WHEN DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:00')  > lateCountTime
                THEN 'Yes'
                ELSE 'No' END) AS  ifLate,
                (SELECT CASE WHEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)  > '0'
                THEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)
                ELSE '00:00:00' END) AS  totalLateTime,
                TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
                from employee
                inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
                inner join department on department.department_id = employee.department_id
                JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
                JOIN employee_attendance on employee_attendance.employee_id= employee.employee_id
                where employee.status=1 AND `date`=input_date GROUP BY view_employee_in_out_data.finger_print_id ORDER BY employee_attendance_id DESC;
            END;;

DROP PROCEDURE IF EXISTS `SP_DailyAttendanceThis`;;
CREATE PROCEDURE `SP_DailyAttendanceThis`(IN `input_date` DATE)
select employee.employee_id,employee.photo,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
                        view_employee_in_out_data.employee_attendance_id,view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,
                        DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time, 
		TIME_FORMAT( work_shift.late_count_time, '%H:%i:%s' ) as lateCountTime,
	(SELECT CASE WHEN DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:00')  > lateCountTime
            THEN 'Yes' 
            ELSE 'No' END) AS  ifLate,
 
            (SELECT CASE WHEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)  > '0'
            THEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time) 
            ELSE '00:00:00' END) AS  totalLateTime,
             TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
                        from employee
                        inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
                        inner join department on department.department_id = employee.department_id
JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
                        where  `date`=input_date GROUP BY view_employee_in_out_data.finger_print_id ORDER BY employee_attendance_id DESC;;

DROP PROCEDURE IF EXISTS `SP_DailyOverTime`;;
CREATE PROCEDURE `SP_DailyOverTime`(IN `input_date` DATE)
BEGIN
                select employee.employee_id,employee.photo,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
                view_employee_in_out_data.employee_attendance_id,view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,
                DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time,
                TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
                from employee
                inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
                inner join department on department.department_id = employee.department_id
                JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
                where `status`=1 AND `date`=input_date GROUP BY view_employee_in_out_data.finger_print_id ORDER BY employee_attendance_id DESC;
            END;;

DROP PROCEDURE IF EXISTS `SP_DepartmentDailyAttendance`;;
CREATE PROCEDURE `SP_DepartmentDailyAttendance`(IN `input_date` DATE, IN `department_id` INT(10), IN `attendance_status` INT(10))
BEGIN

            select employee.employee_id,employee.supervisor_id,designation.designation_name,department.department_name,branch.branch_name,employee.photo,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
            view_employee_in_out_data.employee_attendance_id,view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,view_employee_in_out_data.approve_over_time_id,view_employee_in_out_data.incentive_details_id,
            view_employee_in_out_data.comp_off_details_id,view_employee_in_out_data.incentive_details_id,
            view_employee_in_out_data.device_name, view_employee_in_out_data.shift_name, view_employee_in_out_data.late_by, view_employee_in_out_data.early_by,
            view_employee_in_out_data.over_time, view_employee_in_out_data.in_out_time, view_employee_in_out_data.attendance_status,
            DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time,
		    TIME_FORMAT( work_shift.late_count_time, '%H:%i:%s' ) as lateCountTime,
	        (SELECT CASE WHEN DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:00')  > lateCountTime
            THEN 'Yes'
            ELSE 'No' END) AS  ifLate,
            (SELECT CASE WHEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)  > '0'
            THEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)
            ELSE '00:00:00' END) AS  totalLateTime,
            TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
            from employee
            inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
            inner join department on department.department_id = employee.department_id
            inner join designation on designation.designation_id = employee.designation_id
            inner join branch on branch.branch_id = employee.branch_id
            JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
            where ( `date`=input_date)AND(employee.department_id=department_id OR department_id="")AND(view_employee_in_out_data.attendance_status=attendance_status OR attendance_status="")
            GROUP BY view_employee_in_out_data.finger_print_id ORDER BY view_employee_in_out_data.finger_print_id;

            END;;

DROP PROCEDURE IF EXISTS `SP_getEmployeeInfo`;;
CREATE PROCEDURE `SP_getEmployeeInfo`(IN `employeeId` INT(10))
BEGIN
	       SELECT employee.*,user.`user_name` FROM employee 
            INNER JOIN `user` ON `user`.`user_id` = employee.`user_id`
            WHERE employee_id = employeeId;
        END;;

DROP PROCEDURE IF EXISTS `SP_getHoliday`;;
CREATE PROCEDURE `SP_getHoliday`(IN `fromDate` DATE, IN `toDate` DATE)
BEGIN
        SELECT from_date,to_date FROM holiday_details WHERE from_date >= fromDate AND to_date <=toDate;
        END;;

DROP PROCEDURE IF EXISTS `SP_getWeeklyHoliday`;;
CREATE PROCEDURE `SP_getWeeklyHoliday`(IN `emp_id` INT, IN `from_month` VARCHAR(10))
BEGIN
    SELECT day_name, weekoff_days, employee_id
    FROM weekly_holiday
    WHERE status = 1   
 AND employee_id = emp_id
     AND month >= CONVERT(from_month USING utf8mb4) COLLATE utf8mb4_unicode_ci;
END;;

DROP PROCEDURE IF EXISTS `SP_monthlyAttendance`;;
CREATE PROCEDURE `SP_monthlyAttendance`(IN `employeeId` INT(10), IN `from_date` DATE, IN `to_date` DATE)
BEGIN
                select employee.employee_id,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
                view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,view_employee_in_out_data.over_time,
                view_employee_in_out_data.early_by,view_employee_in_out_data.late_by,
                DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time,
                TIME_FORMAT( work_shift.late_count_time, '%H:%i:%s' ) as lateCountTime,
                (SELECT CASE WHEN DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:00')  > lateCountTime
                THEN 'Yes'
                ELSE 'No' END) AS  ifLate,
                (SELECT CASE WHEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)  > '0'
                THEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),'%H:%i:%s')),work_shift.late_count_time)
                ELSE '00:00:00' END) AS  totalLateTime,
                TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
                from employee
                inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
                inner join department on department.department_id = employee.department_id
                JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
                where `date` between from_date and to_date and employee_id=employeeId
                GROUP BY view_employee_in_out_data.date,view_employee_in_out_data.`finger_print_id`;
            END;;

DROP PROCEDURE IF EXISTS `SP_monthlyOverTime`;;
CREATE PROCEDURE `SP_monthlyOverTime`(IN `employeeId` INT(10), IN `from_date` DATE, IN `to_date` DATE)
BEGIN
                SELECT employee.employee_id,CONCAT(COALESCE(employee.first_name,''),' ',COALESCE(employee.last_name,'')) AS fullName,department_name,
                view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,
                DATE_FORMAT(view_employee_in_out_data.in_time,'%h:%i %p') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,'%h:%i %p') AS out_time,
                TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,'%H:%i:%s')),work_shift.`start_time`) AS workingHour
                from employee
                inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
                inner join department on department.department_id = employee.department_id
                JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
                where `status`=1
                AND `date` between from_date and to_date and employee_id=employeeId
                GROUP BY view_employee_in_out_data.date,view_employee_in_out_data.`finger_print_id`;
            END;;

DELIMITER ;

-- 2024-05-03 03:06:41

-- 002_seed_data.sql : demo data (fake data only)

INSERT INTO departments (name) VALUES ('IT');
INSERT INTO departments (name) VALUES ('Human Resources');
INSERT INTO departments (name) VALUES ('Finance');
INSERT INTO departments (name) VALUES ('Marketing');
INSERT INTO departments (name) VALUES ('Operations');

INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Ahmed Ali', 'ahmed.ali@company.com', '0501111111', 1, 'Developer', DATE '2024-02-10', 'Full-time', 'Active');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Sara Mohammed', 'sara.m@company.com', '0502222222', 2, 'HR Specialist', DATE '2025-03-12', 'Full-time', 'Active');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Khalid Saleh', 'khalid.s@company.com', '0503333333', 3, 'Accountant', DATE '2023-09-01', 'Full-time', 'On Leave');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Noura Hassan', 'noura.h@company.com', '0504444444', 4, 'Marketing Lead', DATE '2022-06-15', 'Full-time', 'Active');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Omar Fahad', 'omar.f@company.com', '0505555555', 5, 'Operations Coordinator', TRUNC(SYSDATE), 'Contract', 'Active');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Lama Abdullah', 'lama.a@company.com', '0506666666', 1, 'Network Engineer', TRUNC(SYSDATE), 'Full-time', 'Active');
INSERT INTO employees (full_name, email, phone, department_id, position, joining_date, employment_type, status)
VALUES ('Faisal Nasser', 'faisal.n@company.com', '0507777777', 3, 'Financial Analyst', DATE '2021-01-25', 'Part-time', 'Inactive');

INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, status)
VALUES (3, 'Annual', DATE '2026-10-01', DATE '2026-10-10', 'Approved');
INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, status)
VALUES (1, 'Sick', DATE '2026-09-28', DATE '2026-09-29', 'Approved');
INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, status)
VALUES (2, 'Annual', DATE '2026-10-20', DATE '2026-10-24', 'Pending');
INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, status)
VALUES (4, 'Annual', DATE '2026-11-02', DATE '2026-11-04', 'Pending');
INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, status)
VALUES (6, 'Sick', DATE '2026-10-12', DATE '2026-10-13', 'Pending');

COMMIT;
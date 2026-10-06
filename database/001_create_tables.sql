-- 001_create_tables.sql : Oracle version of the HR schema

CREATE TABLE users (
  id            NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  full_name     VARCHAR2(100) NOT NULL,
  username      VARCHAR2(50)  NOT NULL UNIQUE,
  password_hash VARCHAR2(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
  id   NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  name VARCHAR2(100) NOT NULL UNIQUE
);

CREATE TABLE employees (
  id              NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  full_name       VARCHAR2(100) NOT NULL,
  email           VARCHAR2(100) NOT NULL UNIQUE,
  phone           VARCHAR2(20),
  department_id   NUMBER NOT NULL,
  position        VARCHAR2(100) NOT NULL,
  joining_date    DATE NOT NULL,
  employment_type VARCHAR2(20) DEFAULT 'Full-time' NOT NULL,
  status          VARCHAR2(20) DEFAULT 'Active' NOT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_emp_dept FOREIGN KEY (department_id) REFERENCES departments(id),
  CONSTRAINT chk_emp_type CHECK (employment_type IN ('Full-time','Part-time','Contract')),
  CONSTRAINT chk_emp_status CHECK (status IN ('Active','On Leave','Inactive'))
);

CREATE TABLE leave_requests (
  id          NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  employee_id NUMBER NOT NULL,
  leave_type  VARCHAR2(10) NOT NULL,
  start_date  DATE NOT NULL,
  end_date    DATE NOT NULL,
  status      VARCHAR2(10) DEFAULT 'Pending' NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_leave_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  CONSTRAINT chk_leave_type CHECK (leave_type IN ('Annual','Sick')),
  CONSTRAINT chk_leave_status CHECK (status IN ('Pending','Approved','Rejected'))
);
-- Membuat tabel mstr_schools
CREATE TABLE IF NOT EXISTS mstr_schools (
    INSTITUTION_CODE TEXT PRIMARY KEY,
    NAME TEXT NOT NULL,
    ADDRESS TEXT,
    CITY TEXT,
    PROVINCE TEXT
);

-- Membuat tabel trans_input_data
CREATE TABLE IF NOT EXISTS trans_input_data (
    Input_Data_Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Input_Data_Type INTEGER,
    Promotion_Name TEXT,
    Event_Start_Date DATE,
    Event_End_Date DATE,
    Note TEXT,
    Created_By TEXT,
    Modified_By TEXT,
    Created_Date DATETIME,
    Modified_Date DATETIME
);

-- Membuat tabel trans_input_data_schools_id
CREATE TABLE IF NOT EXISTS trans_input_data_schools_id (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Input_Data_Id INTEGER,
    School_Id TEXT,
    Created_By TEXT,
    Modified_By TEXT,
    Created_Date DATETIME,
    Modified_Date DATETIME,
    FOREIGN KEY (Input_Data_Id) REFERENCES trans_input_data(Input_Data_Id),
    FOREIGN KEY (School_Id) REFERENCES mstr_schools(INSTITUTION_CODE)
);

-- Membuat tabel trans_input_data_person
CREATE TABLE IF NOT EXISTS trans_input_data_person (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Input_Data_Id INTEGER,
    Name TEXT,
    Created_By TEXT,
    Modified_By TEXT,
    Created_Date DATETIME,
    Modified_Date DATETIME,
    FOREIGN KEY (Input_Data_Id) REFERENCES trans_input_data(Input_Data_Id)
);

-- Membuat tabel trans_input_data_department
CREATE TABLE IF NOT EXISTS trans_input_data_department (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Input_Data_Id INTEGER,
    Department_Id INTEGER,
    Created_By TEXT,
    Modified_By TEXT,
    Created_Date DATETIME,
    Modified_Date DATETIME,
    FOREIGN KEY (Input_Data_Id) REFERENCES trans_input_data(Input_Data_Id)
);

-- Membuat tabel trans_input_data_sponsorship
CREATE TABLE IF NOT EXISTS trans_input_data_sponsorship (
    Id INTEGER PRIMARY KEY AUTOINCREMENT,
    Input_Data_Id INTEGER,
    Sponsorship_Name TEXT,
    Amount INTEGER,
    Description TEXT,
    Created_By TEXT,
    Modified_By TEXT,
    Created_Date DATETIME,
    Modified_Date DATETIME,
    FOREIGN KEY (Input_Data_Id) REFERENCES trans_input_data(Input_Data_Id)
);

-- Membuat tabel manual_entries
CREATE TABLE IF NOT EXISTS manual_entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    input_data_id INTEGER,
    province TEXT,
    city TEXT,
    school_name TEXT,
    school_address TEXT,
    contact_person TEXT,
    phone_number TEXT,
    notes TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (input_data_id) REFERENCES trans_input_data(Input_Data_Id)
);

-- Membuat tabel migrations
CREATE TABLE IF NOT EXISTS migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    migration TEXT NOT NULL,
    batch INTEGER NOT NULL
);

-- Menambahkan entri migrasi yang sudah dijalankan
INSERT OR IGNORE INTO migrations (id, migration, batch) VALUES 
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_04_041633_create_manual_entries_table', 2),
(5, '2026_02_04_041658_add_indexes_to_tables', 2),
(6, '2026_02_04_041737_create_audit_trails_table', 3),
(7, '2026_02_04_042208_add_constraints_and_validations', 3),
(8, '2026_02_04_042255_add_import_functionality', 3),
(9, '2026_02_04_042613_fix_manual_entries_table', 4),
(10, '2026_02_04_042855_skip_manual_entries_migration', 4),
(11, '2026_02_04_042920_create_manual_entries_correct_structure', 5);
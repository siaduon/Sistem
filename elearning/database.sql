CREATE DATABASE IF NOT EXISTS elearning_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE elearning_db;

DROP TABLE IF EXISTS user_quiz_results;
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS user_missions;
DROP TABLE IF EXISTS missions;
DROP TABLE IF EXISTS lesson_progress;
DROP TABLE IF EXISTS lessons;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    thumbnail VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress INT NOT NULL DEFAULT 0,
    status ENUM('in_progress', 'completed') NOT NULL DEFAULT 'in_progress',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    CONSTRAINT fk_enroll_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    video_type ENUM('file', 'youtube') NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    order_number INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lesson_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE lesson_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_lesson (user_id, lesson_id),
    CONSTRAINT fk_lp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_lp_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);

CREATE TABLE missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    CONSTRAINT fk_mission_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE user_missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mission_id INT NOT NULL,
    status ENUM('completed', 'pending') NOT NULL DEFAULT 'completed',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_mission (user_id, mission_id),
    CONSTRAINT fk_um_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_um_mission FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE CASCADE
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    passing_score INT NOT NULL DEFAULT 70,
    CONSTRAINT fk_quiz_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_answer_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

CREATE TABLE user_quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    status ENUM('passed', 'failed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_result_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_result_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin Sistem', 'admin@elearning.test', '$2y$10$qL75Q3G5MgsSWhnVj.9XfOmP2rvjp5my95v5h8YFl8ocIb0z9VSdK', 'admin'),
('Budi Siswa', 'user@elearning.test', '$2y$10$qL75Q3G5MgsSWhnVj.9XfOmP2rvjp5my95v5h8YFl8ocIb0z9VSdK', 'user');

INSERT INTO courses (title, description, thumbnail) VALUES
('PHP Fundamental', 'Belajar dasar PHP native dari nol sampai paham.', NULL),
('MySQL untuk Web', 'Mempelajari query, relasi tabel, dan optimasi dasar.', NULL);

INSERT INTO lessons (course_id, title, video_type, video_url, order_number) VALUES
(1, 'Pengenalan PHP', 'youtube', 'https://www.youtube.com/watch?v=OK_JCtrrv-c', 1),
(1, 'Variabel dan Kondisional', 'youtube', 'https://www.youtube.com/watch?v=Vt0WzE4eA6Y', 2),
(2, 'Pengenalan Database', 'youtube', 'https://www.youtube.com/watch?v=HXV3zeQKqGY', 1);

INSERT INTO missions (course_id, title, description) VALUES
(1, 'Mission 1 - Hello World', 'Buat script PHP sederhana yang menampilkan Hello World.'),
(1, 'Mission 2 - Form Input', 'Buat form input nama dan tampilkan hasilnya dengan aman.'),
(2, 'Mission DB', 'Buat tabel students dan isi minimal 5 data.');

INSERT INTO quizzes (course_id, title, passing_score) VALUES
(1, 'Quiz PHP Dasar', 70),
(2, 'Quiz MySQL Dasar', 70);

INSERT INTO questions (quiz_id, question_text) VALUES
(1, 'Fungsi untuk menampilkan output di PHP adalah?'),
(1, 'Variabel PHP diawali dengan simbol?'),
(2, 'Perintah untuk mengambil data pada SQL adalah?');

INSERT INTO answers (question_id, answer_text, is_correct) VALUES
(1, 'echo', 1), (1, 'print_r', 0), (1, 'var_dump', 0), (1, 'show', 0),
(2, '$', 1), (2, '#', 0), (2, '@', 0), (2, '&', 0),
(3, 'SELECT', 1), (3, 'INSERT', 0), (3, 'UPDATE', 0), (3, 'DELETE', 0);

INSERT INTO enrollments (user_id, course_id, progress, status) VALUES
(2, 1, 0, 'in_progress');

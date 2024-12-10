CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL
);

CREATE TABLE job_posts (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

CREATE TABLE applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    applicant_id INT,
    resume VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES job_posts(job_id),
    FOREIGN KEY (applicant_id) REFERENCES users(user_id)
);


CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,      
    receiver_id INT NOT NULL,    
    message TEXT NOT NULL,          
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
    message_type ENUM('initial', 'reply') DEFAULT 'initial', -- Message type ('initial' or 'reply')
    FOREIGN KEY (sender_id) REFERENCES users(user_id), 
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) 
);

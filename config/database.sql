CREATE TABLE visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    page_visited VARCHAR(255),
    visit_time DATETIME DEFAULT CURRENT_TIMESTAMP
); 
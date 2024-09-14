<h1>Task Manager Web Application</h1>

<p>This is a Task Manager web application built using PHP, MySQL, and JavaScript. It features user authentication, task creation, editing, deletion, and task completion tracking.</p>

<h2>Features</h2>
<ul>
  <li>User registration and login system</li>
  <li>Task management (add, edit, delete tasks)</li>
  <li>Search tasks functionality</li>
  <li>Track completed tasks</li>
  <li>Responsive design</li>
</ul>

<h2>Prerequisites</h2>
<p>To run this project locally, ensure you have the following installed:</p>
<ul>
  <li><strong>XAMPP</strong> (for Apache, MySQL, PHP)</li>
  <li><a href="https://www.apachefriends.org/index.html" target="_blank">Download XAMPP</a></li>
</ul>

<h2>Setup Instructions</h2>

<h3>1. Clone the Repository</h3>
<p>First, clone the repository to your local machine:</p>
<pre>
<code>git clone https://github.com/your-username/task-manager-app.git
cd task-manager-app
</code>
</pre>

<h3>2. Set Up XAMPP</h3>
<ul>
  <li>Download and install XAMPP.</li>
  <li>Start Apache and MySQL from the XAMPP control panel.</li>
</ul>

<h3>3. Create the Database</h3>
<ol>
  <li>Open your browser and go to <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a>.</li>
  <li>Create a new database named <strong>task_manager</strong>.</li>
  <li>Run the following SQL queries to set up the database tables:</li>
</ol>

<pre>
<code>CREATE TABLE `users` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
);

CREATE TABLE `tasks` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `description` text,
  `due_date` date,
  `due_time` time,
  `completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
</code>
</pre>

<h3>4. Configure Database Connection</h3>
<p>Open <code>db_connection.php</code> and update the database credentials:</p>
<pre>
<code>$host = 'localhost';
$db = 'task_manager';
$user = 'root';
$pass = '';  // Leave blank if no password is set for MySQL
</code>
</pre>

<h3>5. Run the Application</h3>
<ol>
  <li>Move the entire project folder (<code>task-manager-app</code>) to the XAMPP <code>htdocs</code> directory.</li>
  <li>In your browser, navigate to <a href="http://localhost/task-manager-app/login.php" target="_blank">http://localhost/task-manager-app/login.php</a>.</li>
</ol>

<h3>6. Register a New User</h3>
<p>Navigate to the registration page: <a href="http://localhost/task-manager-app/register.php" target="_blank">http://localhost/task-manager-app/register.php</a> and create a new user account.</p>

<h3>7. Manage Tasks</h3>
<p>After logging in, you will be able to add, edit, delete, and search for tasks.</p>

<h2>File Structure</h2>
<pre>
<code>task-manager-app/
│
├── db_connection.php       # Database connection script
├── login.php               # Login page
├── register.php            # User registration page
├── tasks.php               # Main task management page
├── logout.php              # Logout functionality
├── search.php              # Search tasks functionality
├── README.md               # Project documentation
├── css/
│   └── styles.css          # Custom styles
└── sql/
    └── db.sql              # SQL file for creating database and tables
</code>
</pre>

<h2>Technologies Used</h2>
<ul>
  <li><strong>Frontend</strong>: HTML, CSS, JavaScript</li>
  <li><strong>Backend</strong>: PHP</li>
  <li><strong>Database</strong>: MySQL (via XAMPP)</li>
  <li><strong>Local Server</strong>: XAMPP</li>
</ul>

<h2>Troubleshooting</h2>
<ul>
  <li><strong>Database Connection Issues</strong>: Ensure that XAMPP's MySQL is running and the database credentials in <code>db_connection.php</code> are correct.</li>
  <li><strong>404 Errors</strong>: Ensure that the project folder is inside the <code>htdocs</code> directory.</li>
  <li><strong>Login Issues</strong>: Ensure you have registered an account through the <code>register.php</code> page.</li>
</ul>

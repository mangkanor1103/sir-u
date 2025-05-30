# Votesys.Online - Electronic Voting System

![Votesys.Online Logo](pics/logo.png)

## Overview

Votesys.Online is a secure electronic voting system designed for educational institutions and organizations. This web-based platform enables seamless election management with real-time results, enhanced security features, and a user-friendly interface.

## Features

- **Secure Authentication**: Voter ID verification system
- **Real-time Results**: Instant vote counting and visualization
- **Mobile-Responsive**: Access from any device with internet connection
- **User-Friendly Interface**: Intuitive design for all technical skill levels
- **Admin Dashboard**: Comprehensive election management tools
- **Data Security**: End-to-end encrypted voting platform

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- TailwindCSS
- Bootstrap
- Chart.js
- Encryption Libraries
- Particles.js

## Installation

### Prerequisites

- XAMPP (or similar local development environment with PHP 7.4+ and MySQL)
- Web browser (Chrome, Firefox, Safari, etc.)

### Setup Instructions

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/votesys-online.git
   ```

2. Move the project to your XAMPP htdocs folder:
   ```
   move votesys-online C:\xampp\htdocs\
   ```

3. Start XAMPP and ensure Apache and MySQL services are running

4. Import the database:
   - Open your web browser and navigate to `http://localhost/phpmyadmin`
   - Create a new database named `votesys`
   - Import the database file from `database/votesys.sql`

5. Configure database connection:
   - Open `includes/conn.php`
   - Update database credentials if necessary

6. Access the application:
   - Navigate to `http://localhost/votesys-online`

## Usage

### Voter Access

1. Enter the provided Voter's ID on the login page
2. Follow the on-screen instructions to cast your vote
3. Submit your ballot to complete the voting process

### Admin Access

1. Navigate to the Admin page via the link on the homepage
2. Log in with administrator credentials
3. Use the dashboard to:
   - Create and manage elections
   - Add/edit/remove voters
   - Add/edit/remove candidates
   - View real-time results and analytics
   - Generate reports

## Development Team

- **Kian A. Rodriguez** - Project Lead & Full-stack Developer
- **Princess Devilla** - Frontend Developer
- **Francis Romero** - Frontend Developer
- **Alpha Mae Valdez** - Backend Developer
- **Joan Manzano** - Backend Developer

## Faculty Advisor

- **Sir Uriel Melendrez** - College of Computer Studies, Minsū Bongabong Campus

## License

© 2025 Votesys.Online - Developed for educational institutions and organizations

## Contact

For inquiries and support, please contact:
[Contact Form](https://www.facebook.com/kianr873)

## Screenshots

![Login Page](screenshots/login.png)
![Admin Dashboard](screenshots/admin-dashboard.png)
![Voting Interface](screenshots/voting-interface.png)
 
 

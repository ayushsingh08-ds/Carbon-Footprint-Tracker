# 🌱 Carbon Footprint Tracker

A comprehensive web application for tracking and visualizing personal carbon emissions across transportation, energy, and food consumption. Built with PHP, MySQL, HTML5, CSS3, and JavaScript.

## 📖 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Technologies Used](#-technologies-used)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🔐 User Authentication

- **User Registration**: Create accounts with email validation and secure password hashing
- **Login System**: Secure session-based authentication
- **Profile Management**: Update user information and view account details
- **State-based Registration**: Select from Indian states for location tracking

### 📊 Carbon Tracking

- **Multi-category Tracking**: Monitor emissions from:
  - 🚗 **Transportation**: Car, bike, cycle, walking, plane travel
  - ⚡ **Energy**: Electricity, natural gas, solar power usage
  - 🍽️ **Food**: Vegan, vegetarian, balanced, and meat-based diets
- **Real-time Calculations**: Automatic CO₂ emission calculations based on activity data
- **Daily Footprint**: View today's total carbon emissions with status messages

### 📈 Data Visualization

- **Interactive Charts**:
  - Weekly, monthly, and yearly emission trends
  - Pie chart breakdown by category (Transportation, Energy, Food)
  - Bar charts with custom styling and animations
- **Responsive Dashboard**: Mobile-first design with desktop optimization
- **Progress Tracking**: Visual progress indicators and achievement badges

### 🎨 User Experience

- **Dark/Light Theme**: Toggle between themes with persistent settings
- **Responsive Design**: Optimized for mobile, tablet, and desktop devices
- **Animated UI**: Smooth transitions and micro-interactions
- **Achievement System**: Earn badges for eco-friendly behavior
- **Activity Icons**: Visual representation of different activity types

### 📱 Mobile-Optimized Interface

- **Touch-friendly Controls**: Large buttons and intuitive navigation
- **Progressive Enhancement**: Works offline with cached data
- **Swipe Gestures**: Enhanced mobile interaction patterns

## 🖼️ Screenshots

The application features a modern, eco-friendly design with:

- Clean login/registration interface with theme switching
- Interactive dashboard with emission visualization
- Activity tracking with category-based icons
- Profile management and emission history

## 🛠️ Technologies Used

### Frontend

- **HTML5**: Semantic markup and accessibility features
- **CSS3**: Advanced styling with Flexbox, Grid, and animations
- **JavaScript (ES6+)**: Interactive features and API communication
- **Chart.js**: Data visualization library for emissions charts
- **Google Fonts**: Poppins and Montserrat typography

### Backend

- **PHP 7.4+**: Server-side logic and API endpoints
- **MySQL**: Relational database for user and emission data
- **Session Management**: Secure user authentication and state management

### Development Tools

- **XAMPP/WAMP**: Local development environment
- **Git**: Version control system
- **Responsive Design**: Mobile-first approach

## 🗄️ Database Schema

### Core Tables

- **Users**: User accounts, credentials, and profile information
- **States**: Indian states for location-based tracking
- **Emissions**: Daily emission records by category and user
- **EmissionActivity**: Activity types (Transportation, Home Energy, Food Consumption)

### Key Relationships

```sql
Users (1) -> (N) Emissions
States (1) -> (N) Users
EmissionActivity (1) -> (N) Emissions
```

## 🚀 Installation

### Prerequisites

- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled

### Setup Instructions

1. **Clone the Repository**

   ```bash
   git clone https://github.com/ayushsingh08-ds/Carbon-Footprint-Tracker.git
   cd Carbon-Footprint-Tracker
   ```

2. **Database Setup**

   ```sql
   -- Create database
   CREATE DATABASE carbon_calc;
   USE carbon_calc;

   -- Create tables
   CREATE TABLE States (
       state_id INT PRIMARY KEY AUTO_INCREMENT,
       state_name VARCHAR(100) NOT NULL UNIQUE
   );

   CREATE TABLE Users (
       user_id INT PRIMARY KEY AUTO_INCREMENT,
       username VARCHAR(50) NOT NULL UNIQUE,
       email VARCHAR(100) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       state_id INT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (state_id) REFERENCES States(state_id)
   );

   CREATE TABLE EmissionActivity (
       activity_id INT PRIMARY KEY AUTO_INCREMENT,
       activity_name VARCHAR(100) NOT NULL
   );

   CREATE TABLE Emissions (
       emission_id INT PRIMARY KEY AUTO_INCREMENT,
       user_id INT NOT NULL,
       state_id INT NOT NULL,
       activity_id INT NOT NULL,
       emission_value DECIMAL(10,2) NOT NULL,
       date DATE NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES Users(user_id),
       FOREIGN KEY (state_id) REFERENCES States(state_id),
       FOREIGN KEY (activity_id) REFERENCES EmissionActivity(activity_id)
   );
   ```

3. **Insert Sample Data**

   ```sql
   -- Insert activity types
   INSERT INTO EmissionActivity (activity_name) VALUES
   ('Transportation'),
   ('Home Energy'),
   ('Food Consumption');

   -- Insert Indian states
   INSERT INTO States (state_name) VALUES
   ('Andhra Pradesh'), ('Arunachal Pradesh'), ('Assam'),
   ('Bihar'), ('Chhattisgarh'), ('Goa'), ('Gujarat'),
   -- ... (add all Indian states)
   ```

4. **Configure Database Connection**
   Update `db.php` with your database credentials:

   ```php
   <?php
   $servername = "localhost";
   $username = "your_db_username";
   $password = "your_db_password";
   $dbname = "carbon_calc";
   ?>
   ```

5. **Deploy to Web Server**
   - Copy files to your web server directory (`htdocs` for XAMPP)
   - Ensure proper file permissions
   - Start Apache and MySQL services

## ⚙️ Configuration

### Environment Variables

Create a `.env` file (optional) for production deployment:

```env
DB_HOST=localhost
DB_NAME=carbon_calc
DB_USER=your_username
DB_PASS=your_password
```

### Security Considerations

- Use HTTPS in production
- Regular database backups
- Strong password policies
- Input validation and sanitization

## 📖 Usage

### Getting Started

1. **Register**: Create an account with email and select your state
2. **Login**: Access your personalized dashboard
3. **Track Activities**: Add daily activities across three categories:
   - Transportation (distance-based calculations)
   - Energy consumption (kWh usage)
   - Food choices (diet-based emissions)
4. **View Analytics**: Monitor your carbon footprint trends
5. **Achieve Goals**: Earn badges for sustainable practices

### Activity Tracking

- **Transportation**: Input distance traveled by different modes
- **Energy**: Record home energy consumption
- **Food**: Select diet types with automatic emission calculations

### Data Export

- View detailed emission records in your profile
- Export data for external analysis
- Track progress over time periods

## 🔌 API Endpoints

### User Management

- `POST /login.php` - User authentication
- `POST /register.php` - User registration
- `GET /fetch_user.php` - Get user profile
- `PUT /update_user.php` - Update profile

### Emission Tracking

- `POST /store_emissions.php` - Record emissions
- `GET /get_daily_footprint.php` - Today's emissions
- `GET /get_emissions.php` - Historical data
- `GET /get_emission_breakdown.php` - Category breakdown

### Utilities

- `GET /fetch_states.php` - Available states
- `POST /logout.php` - End session

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/AmazingFeature`
3. **Commit changes**: `git commit -m 'Add AmazingFeature'`
4. **Push to branch**: `git push origin feature/AmazingFeature`
5. **Open a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards for PHP
- Use semantic commit messages
- Add tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🌟 Acknowledgments

- Carbon emission factors based on EPA and IPCC guidelines
- Icons and graphics from open-source libraries
- Community feedback and contributions

## 📞 Support

For support, please open an issue on GitHub or contact [ayushsingh08.ds@gmail.com](mailto:ayushsingh08.ds@gmail.com)

---

**Made with 💚 for a sustainable future**

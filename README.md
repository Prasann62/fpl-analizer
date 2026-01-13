🏆 FPL Analizer

A simple, beginner-friendly Fantasy Premier League (FPL) web app built with PHP to help users explore basic FPL data, plan teams, and analyze performance. Includes authentication (login/logout), session handling, and starter tools for FPL stats and team planning.

📌 Features

✔️ User login & logout system (PHP + sessions)
✔️ Dashboard after authentication
✔️ FPL-related pages including:

Team Analyzer

Fixtures & Live Scores

Player & Rank pages

Price Change and Prediction pages

Planner and AI helpers
✔️ Basic UI components (navbar/sidebar)
✔️ PHP backend API for dynamic data handling
📁 Repository Structure
├── index.php                 # Main entry / login redirect  
├── loginform.php             # Login form  
├── dashboard.php             # User dashboard  
├── team-analyzer.php         # Team analysis tool  
├── planner.php               # Team planner  
├── fixtures.php              # Fixtures page  
├── live-score.php            # Live scores  
├── price-changes.php         # Price changes  
├── players.php               # Player list & details  
├── rank.php                  # Rankings  
├── compare.php               # Team/player comparison  
├── api.php                   # Backend API endpoints  
├── style.css                 # Site styles  
├── navbar.php / sidebar.php  # UI components  
└── …                        # Other PHP & asset files  
``` :contentReference[oaicite:3]{index=3}

---

## 🚀 Getting Started

These instructions will help you run the project locally.

### 🛠 Prerequisites

Make sure you have:

- PHP 7.4 or higher  
- Apache, Nginx, or any local server that can run PHP  
- A modern web browser

> Optional: a database (MySQL/MariaDB) if you extend authentication or dynamic data storage.

---

### 📥 Installation

1. **Clone the repo**
   ```bash
   git clone https://github.com/Prasann62/fpl-analizer.git
   cd fpl-analizer
2.Start the PHP server

php -S localhost:8000
3.Open in browser

http://localhost:8000
4.Login / Signup

If a signup form exists, create an account

Otherwise, ensure authentication data is seeded

🧠 How to Use

Once logged in, explore the dashboard and available tools:

Team Analyzer: Plan your squad and evaluate strength

Fixtures: See upcoming match schedules

Live Scores: Watch real match outcomes

Price Changes & Rank: Track player value changes or FPL rank

Planner & AI Helpers: Suggest changes, captain picks, predictions

Actual feature behavior depends on implementation of respective PHP files.

🤝 Contributing

Contributions are welcome! Here are ways you can help:

Add deeper FPL analysis (expected goals, xGI metrics)

Improve UI/UX responsiveness

Integrate with the official FPL API for real-time data

Add tests and documentation

📝 License

i have no licence

🧾 About

A beginner-friendly PHP web project to start learning backend development while building useful tools for Fantasy Premier League data exploration.

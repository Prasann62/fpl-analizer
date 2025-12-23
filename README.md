🏆 FPL Analizer

A simple, beginner-friendly Fantasy Premier League (FPL) web app built with PHP.
This project includes basic authentication (login/logout) and some FPL-related functionality (team planning, fixtures, players, price changes, live score pages, etc).

NOTE: The original repository description says it’s a PHP authentication system, so this README focuses on that and the visible file structure.
GitHub

🚀 Features
✔️ Core Functionality

🔐 User Login & Logout

🛡️ PHP Session Handling

💡 Basic form validation

📊 FPL-related pages (team view, fixtures, live score, price changes, etc)

🎨 Simple clean UI

🧠 FPL Tools (based on included PHP pages)

📋 Team Analyzer

📈 FPL Planner

🧠 AI-based team picker & predictor

🏆 Rank & Compare Pages

⚽ Fixtures & Match Details

(Actual features depend on implementation in respective .php files.)
GitHub

🗂️ Repo Structure (important files)
├── index.php                  # Homepage / login redirect
├── loginform.php             # Authentication form
├── dashboard.php             # User dashboard
├── team-analyzer.php         # Team analysis
├── planner.php               # FPL planner
├── fixtures.php              # Fixtures page
├── live-score.php            # Live scores
├── price-changes.php         # Price changes
├── players.php               # Player list/detail page
├── rank.php                  # Rankings
├── compare.php               # Compare teams/players
├── logout.php                # Logout handler
├── style.css                 # Common site styles
├── navbar.php / sidebar.php  # UI components
└── api.php                   # Backend endpoint for data


(Partial list — full directory contains additional UI assets & helpers.)
GitHub

🧑‍💻 Getting Started
Requirements

PHP 7.4+ (or later)

Apache/Nginx server (or built-in PHP server)

MySQL/MariaDB (if database integration is included)

Web browser

📦 Setup

Clone the repository

git clone https://github.com/Prasann62/fpl-analizer.git
cd fpl-analizer


Serve locally

php -S localhost:8000


Open in browser
Go to: http://localhost:8000

Configure database (optional)
If there’s a database used for authentication or FPL data, update config in api.php (or other config file).

💡 Usage

Sign up or log in using the form on loginform.php, then explore features from the dashboard:

View upcoming fixtures

Analyze team performance

Track price changes & ranks

Use team planner and AI helpers

📌 Contributing

Contributions are welcome! Here’s how you can help:

Add more FPL analytics features (xG, xA, form tracking)

Improve UI/UX

Integrate with Fantasy Premier League official API

Add tests & documentation

📄 License

Specify a license here (e.g., MIT) or note if no license is provided.

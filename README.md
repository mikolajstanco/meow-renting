# SaaS Rental & Access Management Platform

Live check: [Meow Renting](https://meowrents.mikolajstanco.pl/)

An automated platform for managing time-based access to a third-party SaaS application. The system handles payments, user authentication, and uses a continuous Python background worker to autonomously rotate vendor API credentials.

*Note: The original third-party API is no longer active. The codebase has been anonymized and serves as a demonstration of API integrations, web security, and background processing.*

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/f8306ecf-15fe-48f0-8d6f-1aef0ceb6bc7" />

## ⚙️ Tech Stack
**PHP 8 | Python 3 | MySQL | Stripe API | Discord API (OAuth2)**

## 🚀 Key Features
* **Automated Payments:** Stripe Checkout sessions with asynchronous Webhook handling.
* **Identity Management:** Discord OAuth2 integration for user verification and automated Server Role assignment.
* **Python Daemon:** A robust background worker that monitors the database and safely rotates vendor API passwords using `requests.Session()`.
* **Security:** Strict Monorepo separation (Web vs. Worker), environment variables (`.env`) for secrets, and Prepared Statements (SQLi protection) across the entire stack.

## 📂 Project Structure
* `/WEB` - PHP application, landing page, and Stripe Webhook listeners.
* `/DB_menager` - Python background worker and dependency list.

## 💻 Quick Setup
1. Copy `.env.example` to `.env` and insert your API keys.
2. Host the `/WEB` directory using a local PHP server (set Document Root here).
3. Install dependencies (`composer install` for PHP, `pip install -r requirements.txt` for Python).
4. Run `python main.py` inside `/DB_menager` to start the autonomous worker.

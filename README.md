# hcl_guvi_internship_project
Project: Register → Login → Profile

Description

A simple web app where users can:
Register with email, password, and basic info
Login with their credentials
View & update profile (age, DOB, contact)

Tech Stack:

Frontend: HTML, CSS, Bootstrap, JS, jQuery
Backend: PHP
Database: MySQL (users data)
Session: Redis (token-based, stored in localStorage)
Optional: MongoDB (for user data backup)

Folder Structure
hcl_guvi/
│
├─ public/          -> HTML pages (register.html, login.html, profile.html)
├─ css/             -> style.css
├─ js/              -> register.js, login.js, profile.js
├─ php/             -> db.php, redis_helper.php, register.php, login.php, profile_api.php
├─ sql/             -> schema.sql
└─ .gitignore

Setup Instructions

Import sql/schema.sql into MySQL.
Update php/db.php with your MySQL credentials.
Update php/redis_helper.php if Redis host/port differs.
Make public/ the web root.
Open register.html in a browser to start.

How It Works

Register: Saves user info in MySQL.
Login: Generates a token stored in Redis, saved in browser localStorage.
Profile: Fetch & update profile via AJAX using the token.
Logout: Deletes token from localStorage and Redis.

Notes

All SQL queries use prepared statements.
Passwords are hashed using password_hash().
No PHP sessions are used; session handled entirely via Redis + localStorage.

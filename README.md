# Lost & Found Bangladesh

A web application that helps people report and recover lost items and missing persons in Bangladesh. Users can post about lost belongings, report missing persons, browse recent posts, interact with a chatbot assistant, and support the platform via online payment.

## Features

- **User Authentication** – Sign up and log in to access the full platform
- **Report Lost Items** – Submit details and images of lost belongings
- **Report Found Items** – Post about items you have found
- **Missing Persons** – Report and search for missing individuals
- **Recent Posts** – Browse recently submitted reports
- **Live Search** – Search through posts in real time
- **Chatbot Assistant** – Get instant answers to common queries via the built-in chatbot
- **User Profile** – View and manage your account details
- **Admin Panel** – Manage posts, users, and platform content
- **Payment Integration** – Support the platform using Stripe payment gateway

## Tech Stack

| Layer      | Technology                     |
|------------|--------------------------------|
| Frontend   | HTML, CSS, Bootstrap 5, JavaScript |
| Backend    | PHP                            |
| Database   | MySQL / MariaDB                |
| Payment    | Stripe (via stripe-php SDK)    |
| Local Server | XAMPP (Apache + MySQL)       |

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) (includes Apache and MySQL/MariaDB)
- PHP 8.x
- A modern web browser

## Getting Started

1. **Download the project**
   - Clone this repository or download and extract the ZIP file.

2. **Copy to XAMPP**
   - Place the project folder inside `xampp/htdocs/`.

3. **Start XAMPP**
   - Open the XAMPP Control Panel and start both **Apache** and **MySQL**.

4. **Create the database**
   - Open your browser and go to `http://localhost/phpmyadmin`.
   - Create a new database named `l&fbd`.

5. **Import the SQL file**
   - In phpMyAdmin, select the `l&fbd` database.
   - Click **Import** and choose `l_fbd.sql` from the project folder.
   - Click **Go** to import the tables and seed data.

6. **Run the application**
   - Open your browser and navigate to `http://localhost/<project-folder-name>/`.

## Project Structure

```
lost_found_website/
├── admin panel/          # Admin dashboard and management pages
├── images/               # Static image assets
├── stripe-php/           # Stripe PHP SDK
├── index.php             # Home page
├── login.html            # Login page
├── signup.html           # Registration page
├── login.php             # Login handler
├── signup.php            # Sign-up handler
├── logout.php            # Logout handler
├── submit_lostitems.php  # Submit a lost item report
├── found_item.php        # Submit a found item report
├── missing_person.php    # Report a missing person
├── recentposts.php       # View recent posts
├── livesearch.php        # Live search functionality
├── chatbot.php           # Chatbot UI
├── get_bot_message.php   # Chatbot message handler
├── payment.php           # Donation / payment page
├── stripe_payment.php    # Stripe payment processing
├── User_profile.html     # User profile page
├── database.inc.php      # Database connection
├── style.css             # Global styles
└── l_fbd.sql             # Database schema and seed data
```

## Database

The application uses a MySQL database named `l&fbd` with the following tables:

| Table            | Description                              |
|------------------|------------------------------------------|
| `signup`         | Registered user accounts                 |
| `founditem`      | Found item reports                       |
| `missingperson`  | Missing person reports                   |
| `chatbot_hints`  | Chatbot question/answer pairs            |
| `message`        | Chatbot conversation history             |
| `registration`   | Payment/registration records             |

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

This project was developed by **Group-06** as part of a university course project. © 2023 Lost & Found Bangladesh. All rights reserved.


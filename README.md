# UrbanLoom - Video Game Store Website

## Overview
UrbanLoom is an online video game store that allows users to browse, purchase, and manage video games efficiently. Built with HTML, CSS, JavaScript, and PHP, the website provides a seamless shopping experience with an interactive UI and secure database integration.

## Features
- **User Authentication**: Sign up, log in, and manage user accounts.
- **Game Catalog**: Browse a collection of video games with filtering and search functionality.
- **Shopping Cart**: Add and remove games before checkout.
- **Secure Checkout**: Purchase games securely using payment integration.
- **Admin Panel**: Manage game listings, user orders, and other site content.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL (Database name: `urbanloom`)
- **Version Control**: Git

## Installation
1. Clone the repository:
   ```sh
   git clone https://github.com/Orange-Cat-99/UrbanLoom.git
   ```
2. Set up a local server using XAMPP or any other PHP server.
3. Create a MySQL database named `urbanloom` and import the provided SQL file (`urbanloom.sql`).
4. Update the database connection details in `config.php`:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "urbanloom";
   ```
5. Start the local server and access the site via `http://localhost/urbanloom`.

## File Structure
```
GAMESTORE/
│── admin/
│   ├── admin_front_end/    # Admin panel frontend files
│   ├── admin_style/        # Admin panel styles
│
│── user/
│   ├── elements/           # Reusable UI components
│   ├── front_end/          # User-facing frontend files
│   ├── games_posters/      # Game images and assets
│   ├── style/              # User-specific styles
│
│── config.txt              # Database connection setup

```

## Contribution
1. Fork the repository.
2. Create a feature branch: `git checkout -b feature-name`.
3. Commit your changes: `git commit -m "Added new feature"`.
4. Push to the branch: `git push origin feature-name`.
5. Submit a pull request.

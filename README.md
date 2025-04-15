# 🛒 FoodMart

**FoodMart** is a modern and responsive PHP-based e-commerce platform tailored for food products. It includes a full admin dashboard, category & product management, cart and wishlist systems, user authentication, and third-party login support (Google, Facebook, LinkedIn).

## 🌟 Features

- User registration & login (custom and social login)
- User profile with image upload
- Dynamic homepage with best-selling products carousel
- Cart and wishlist functionality with live counters
- Product categories (Men, Women, Accessories, etc.)
- Admin panel:
  - User management (view, edit, delete)
  - Product management (CRUD with image upload)
  - Category management
  - Order tracking
  - Message center
- Responsive design using Bootstrap 5
- Clean UI based on **Kaira Fashion Store Template**

## 🧰 Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript, Swiper.js
- **Backend**: PHP (MySQLi)
- **Database**: MySQL
- **Auth APIs**: Facebook, Google, LinkedIn (via Developer SDKs)
- **Hosting**: Compatible with localhost (XAMPP) and InfinityFree

## 📁 Project Structure
foodmart/ ├── admin/ │ ├── add_product.php │ ├── category.php │ ├── update_product.php │ ├── ... ├── php-files/ │ ├── product-details.php │ ├── add_to_cart.php │ ├── wishlist.php │ └── ... ├── assets/ │ ├── css/ │ ├── js/ │ └── images/ ├── index.php ├── login.php ├── register.php └──



## 🛠️ Setup Instructions

1. **Clone the repo or download ZIP**
[text](https://github.com/Mohit-QL/foodmart)



2. **Import the database**
- Open `phpMyAdmin`
- Create a new database named `foodmart`
- Import the `foodmart.sql` file

3. **Set up your environment**
- Use XAMPP or any local server
- Place the project inside `htdocs`
- Start Apache and MySQL

4. **Update credentials**
- In `config.php` or wherever DB connection is made:
  ```php
  $conn = new mysqli("localhost", "root", "", "foodmart");
  ```

5. **Test it**
- Open `http://localhost/foodmart/` in your browser

## 🔐 Social Login Setup

To enable Facebook, Google, and LinkedIn login:
- Create developer credentials on each platform
- Add respective SDKs
- Update your redirect URLs and App IDs in the integration files

## 📸 Screenshots

*Coming soon...*

## 🙌 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## 📄 License

This project is for educational purposes. Feel free to use and modify it for learning and portfolio use.

---

Made with 💚 by the FoodMart Team.

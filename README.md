# LeaveSys - Online Employee Leave Management System (OELMS) 
This Online Employee Leave Management System (OELMS) is a full-stack web application designed to handle all employee time-off requests. It was built using the Fat-Free (F3) Framework for efficient performance and deployment, utilizing a MySQL database and various supporting libraries.

# Team Developer: Malabanan, Mark Kevin D. 
# Design Analytics: Abelong, Mark Jayson S. 
# Document Analytics: David, Lance Angle N. 

# Section: BSIT 3-B 
# Subject: SAD-101

# Deployed Live System: `http://leavesys.x10.mx`


# - Installation Guide

This system is built using the **Fat-Free Framework (F3)**. Follow the steps below to configure your XAMPP environment, set up the database, and run the application using a local domain (`employee.local`).

---

## 1. Project Directory Setup

1.  Locate your XAMPP installation directory (e.g., `C:\Users\XAMPP`).
2.  Navigate to the `htdocs` folder.
3.  Create a folder named **`Employee_System`**.
4.  Extract all project files into this folder:
    ```
    C:\Users\XAMPP\htdocs\Employee_System\
    ```
5.  **Important:** Ensure the Fat-Free Framework core files are present. You should have a `lib/` folder containing `base.php` inside the `Employee_System` directory.

---

## 2. Database Configuration

1.  Start the **Apache** and **MySQL** modules in your XAMPP Control Panel.
2.  Open your browser and go to [http://localhost/phpmyadmin](e.g., http://192.168.0.100/phpmyadmin).
3.  Click **New** to create a database.
4.  Name the database:
    ```
    leave_system_db
    ```
5.  Select the newly created database.
6.  Click the **Import** tab.
7.  Choose the `leave_system_db.sql` file included in this project.
8.  Click **Import** at the bottom to create the tables and seed default users.

---

## 3. Apache Virtual Host Setup

You need to tell Apache to recognize the specific local domain and point it to your project folder.

1.  Navigate to your Apache configuration directory:
    ```
    C:\Users\XAMPP\apache\conf\extra\
    ```
2.  Open the file **`httpd-vhosts.conf`** in a text editor (Notepad, VS Code, etc.).
3.  Add the following configuration to the very bottom of the file:

    ```apache
    <VirtualHost *:80>
        DocumentRoot "C:/Users/XAMPP/htdocs/Employee_System"
        ServerName employee.local
        <Directory "C:/Users/XAMPP/htdocs/Employee_System">
            Require all granted
            AllowOverride All  
        </Directory>
    </VirtualHost>
    ```
4.  Save and close the file.

---

## 4. Windows Hosts File Configuration

You need to map the custom domain to your local machine (localhost).

1.  Open **Notepad** as **Administrator** (Right-click Notepad icon > Run as Administrator).
2.  Open the following file:
    ```
    C:\Windows\System32\drivers\etc\hosts
    ```
3.  Add the following line to the bottom of the file:
    ```
    127.0.0.1       employee.local
    ```
4.  Save the file (Ctrl+S) and close it.

---

## 5. Enable Mod_Rewrite (Optional but Recommended)

The Fat-Free Framework relies on URL routing. Ensure Apache's rewrite module is enabled.

1.  Open `C:\Users\XAMPP\apache\conf\httpd.conf`.
2.  Search for `mod_rewrite.so`.
3.  Ensure the line is **uncommented** (remove the `#` symbol if present):
    ```apache
    LoadModule rewrite_module modules/mod_rewrite.so
    ```
4.  Save the file.

---

## 6. Final Step

1.  **Restart Apache** in the XAMPP Control Panel (Stop, then Start) to apply the Virtual Host changes.
2.  Open your web browser.
3.  Access the system via:
    [http://employee.local](e.g., http://192.168.0.100)

---

## 7. Default Login Credentials

The database comes pre-seeded with the following accounts:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `12345` |
| **Manager** | `manager` | `12345` |
| **Employee** | `Jayson` | `12345` |
| **Employee** | `Kevin` | `12345` |
| **Employee** | `Lance` | `12345` |

---

## Troubleshooting

* **"Access Forbidden" or 403 Error:** Ensure the `<Directory>` path in `httpd-vhosts.conf` matches your actual folder path exactly.
* **"Server Not Found":** Double-check that you saved the `hosts` file as Administrator and restarted your browser.
* **Database Error:** Open `app/config/config.ini` and ensure the settings match your MySQL credentials (default XAMPP user is usually `root` with no password) create a password `root` or depending on your setup (This will not run without a MySQL user and password).

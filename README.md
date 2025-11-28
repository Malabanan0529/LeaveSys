# Online Employee Leave Management System (OELMS)

The **Online Employee Leave Management System (OELMS)** is a full-stack web application designed to streamline and handle employee time-off requests. It is built using the **Fat-Free (F3) Framework** for efficient performance, utilizing a MySQL database and various supporting libraries.

---

### Project Information

* **Subject:** SAD-101 (BSIT 3-B)
* **Deployed Live System:** [http://leavesys.x10.mx](http://leavesys.x10.mx)

### Development Team
* **Lead Developer:** Malabanan, Mark Kevin D.
* **System Designer:** Abelong, Mark Jayson S.
* **Technical Writer:** David, Lance Angle N.

---

# Installation Guide

Follow the steps below to configure your XAMPP environment, set up the database, and run the application using a local domain (`employee.local`).

## Prerequisites
* **XAMPP** (Apache & MySQL)
* **Web Browser** (Chrome, Firefox, or Edge)
* **Text Editor** (VS Code, Notepad++, or Notepad)

---

## 1. Project Directory Setup

1.  Locate your XAMPP installation directory (Default is usually `C:\xampp`).
2.  Navigate to the `htdocs` folder.
3.  Create a folder named **`Employee_System`**.
4.  Extract all project files into this folder so the path looks like this:
    ```
    C:\xampp\htdocs\Employee_System\
    ```
5.  **Important:** Ensure the Fat-Free Framework core files are present. You must have a `lib/` folder containing `base.php` inside the `Employee_System` directory.

---

## 2. Database Configuration

1.  Start the **Apache** and **MySQL** modules in your XAMPP Control Panel.
2.  Open your browser and navigate to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3.  Click **New** to create a database.
4.  Name the database:
    ```
    leave_system_db
    ```
5.  Select the newly created database.
6.  Click the **Import** tab.
7.  Choose the `leave_system_db.sql` file included in this project folder.
8.  Click **Import** to create the tables and seed the default users.

---

## 3. Application Connection Setup

You must ensure the application can talk to your database.

1.  Navigate to the project configuration file:
    ```
    Employee_System/app/config/config.ini
    ```
2.  Open `config.ini` in your text editor.
3.  Locate the database settings. Update the **username** and **password** to match your XAMPP MySQL settings.
    * *Note: Default XAMPP installs usually have user `root` and an empty password.*
    * **If the application requires a password:** You may need to set a password for your root user in phpMyAdmin or create a new user matching the credentials in `config.ini`.

---

## 4. Apache Virtual Host Setup

This step configures Apache to recognize the custom domain.

1.  Navigate to your Apache configuration directory:
    ```
    C:\xampp\apache\conf\extra\
    ```
2.  Open the file **`httpd-vhosts.conf`** in a text editor.
3.  Add the following configuration to the very bottom of the file:

    ```apache
    <VirtualHost *:80>
        DocumentRoot "C:/xampp/htdocs/Employee_System"
        ServerName employee.local
        <Directory "C:/xampp/htdocs/Employee_System">
            Require all granted
            AllowOverride All  
        </Directory>
    </VirtualHost>
    ```
    *(Note: If you installed XAMPP in a different location, adjust the paths above accordingly)*.

---

## 5. Windows Hosts File Configuration

This step maps the custom domain to your local machine.

1.  Open **Notepad** as **Administrator** (Right-click Notepad icon > Run as Administrator).
2.  Open the following file:
    ```
    C:\Windows\System32\drivers\etc\hosts
    ```
3.  Add the following line to the bottom of the file:
    ```
    127.0.0.1       employee.local
    ```
4.  Save the file (**Ctrl+S**) and close it.

---

## 6. Enable Mod_Rewrite

The Fat-Free Framework relies on URL routing. Ensure Apache's rewrite module is enabled.

1.  Open `C:\xampp\apache\conf\httpd.conf`.
2.  Search for `mod_rewrite.so`.
3.  Ensure the line is **uncommented** (remove the `#` symbol if present at the start of the line):
    ```apache
    LoadModule rewrite_module modules/mod_rewrite.so
    ```
4.  Save the file.

---

## 7. Run the System

1.  **Restart Apache** in the XAMPP Control Panel (Stop, then Start) to apply the Virtual Host and Config changes.
2.  Open your web browser.
3.  Access the system via: [http://employee.local](http://employee.local)

---

## Default Login Credentials

The database is pre-seeded with the following accounts:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `12345` |
| **Manager** | `manager` | `12345` |
| **Employee** | `Jayson` | `12345` |
| **Employee** | `Kevin` | `12345` |
| **Employee** | `Lance` | `12345` |

---

## Troubleshooting

* **"Access Forbidden" or 403 Error:** Ensure the `<Directory>` path in `httpd-vhosts.conf` matches your actual project folder path exactly.
* **"Server Not Found":** Double-check that you saved the `hosts` file as Administrator and restarted your browser.
* **Database Connection Error:** Double-check `app/config/config.ini`. The username and password there **must** match your local MySQL credentials.

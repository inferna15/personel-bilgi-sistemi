> 🇹🇷 [Click here for the Turkish version.](README.tr.md)

# Personnel Information System (PBS)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vue.js&logoColor=4FC08D)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

**Personnel Information System (PBS)** is a modern and comprehensive Human Resources (HR) management application developed using Laravel and Vue.js.  
This platform aims to digitize and centralize core HR processes such as personnel, department, leave, payroll, and announcements within an organization.

The project offers **role-based access control**, supporting user profiles with different privileges (Admin, Manager, Employee), and allows data exchange with external services via a RESTful API.

## Screenshots

<table>
  <tr>
    <td align="center"><strong>Dashboard</strong></td>
    <td align="center"><strong>Personnel List</strong></td>
  </tr>
  <tr>
    <td><img width="1999" height="1333" alt="image2" src="https://github.com/user-attachments/assets/3f8138b6-5e41-41d2-b597-34c2545feb65" /></td>
    <td><img width="1999" height="1333" alt="image5" src="https://github.com/user-attachments/assets/fbf75f83-1fc9-4c2e-a831-dfb157bc3aa4" /></td>
  </tr>
  <tr>
    <td align="center"><strong>Card & Balance Management</strong></td>
    <td align="center"><strong>Announcement Management</strong></td>
  </tr>
  <tr>
    <td><img width="1999" height="1333" alt="image10" src="https://github.com/user-attachments/assets/4db3899c-f3ae-42d9-b919-6e2f3099321c" /></td>
    <td><img width="1999" height="1333" alt="image18" src="https://github.com/user-attachments/assets/b8fb8b84-c4d4-4208-a0d3-a8177d3047f9" /></td>
  </tr>
  <tr>
    <td align="center" colspan="2"><strong>Personnel Detail Screen</strong></td>
  </tr>
  <tr>
    <td colspan="2"><img width="1999" height="1333" alt="image7" src="https://github.com/user-attachments/assets/a81f367b-5d3e-4812-b95a-44b5bb6ab51f" /></td>
  </tr>
</table>

## Features

### Role-Based Access Control
The system provides a secure and authorized structure with three distinct user roles:
-   **Admin:** Has full control over the entire system.
-   **Manager:** Manages employees within their own department and approves leave requests.
-   **Employee:** Can view personal information and submit leave requests.

### Modules
-   **Personnel Management:** Add, edit, delete, and search for personnel records.
-   **Department Management:** Create and manage company departments.
-   **Leave Management:** Create and approve leave requests with workflow support.
-   **Payroll Management:** View salary and payslip details.
-   **Announcement System:** Publish company-wide announcements.
-   **External Integration:** Retrieve real-time vehicle and card data via RESTful API.

## Technologies Used

-   **Backend:** PHP (8.x), Laravel (10.x)
-   **Frontend:** Vue.js (3.x), Vite
-   **Database:** MySQL / PostgreSQL (configurable)
-   **API:** RESTful

## Key Packages Used

The backend leverages several essential Laravel packages that enhance functionality and speed up development. You can adjust this list by checking your `composer.json` file.

-   **`laravel/sanctum`**: Provides simple and secure API authentication for frontends like Vue.js Single Page Applications (SPA).
-   **`spatie/laravel-permission`**: Simplifies dynamic management of roles (Admin, Manager, Employee) and their permissions directly through the database.

## Installation

### ⚠️ Important Note: External API Dependency

For full functionality, this project requires another API that provides **vehicle and card information** to be running locally.  
However, that external API project is currently unavailable due to a data loss issue.  
Once it is rewritten, the installation instructions will be updated here.

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.

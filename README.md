# Air Ticket Booking System

## Introduction

The Air Ticket Booking System is an application that allows users to book flights easily, providing advanced features such as booking management, payments, and email notifications.

## Main Features

### **User Roles**

#### **1. Admin:**
- Manage flights (Create, Edit, Delete, Restore "Soft Delete").
- Delete booking in the following cases:
  - booking with `Pending` status one day before the flight.
  - Canceled booking.


#### **2. Registered User:**
- Create new booking.
- Modify booking, provided they are at least one day before the flight.
- Possibility of payment using Strip.
- Cancel booking and receive a refund if canceled at least one day before the flight.
- Receive an email notification when:
  - A reservation is confirmed after payment.
  - A reservation is canceled and a refund is issued.

#### Optimizations
-Admin or Manager Receive a daily report via email that includes:
  - List of payments.
  - List of reservations.
- Cash & Rate Limiting 
- Protect the system from cyber attacks.


## **Technologies Used**
- **Service Design Pattern** for structured code.
- **Queues & Jobs** for asynchronous email processing.
- **Form Request & Resources** for input validation and responses.

## **System Requirements**

- PHP >= 8.0
- Composer
- Laravel >= 10.0
- MySQL or any Laravel-supported database
- Postman for API testing

## **How to Run the Project**

### **1. Clone the Repository**
```sh
git clone <https://github.com/KhatoonBadrea/Air_ticket_booking_system>
```

### **2. Navigate to the Project Directory**
```sh
cd Air_ticket_booking_system
```

### **3. Install Dependencies**
```sh
composer install
```

### **4. Create Environment File**
```sh
cp .env.example .env
```



### **5. Generate Application Key**
```sh
php artisan key:generate
```

### **6. Run Migrations**
```sh
php artisan migrate
```

### **7. Seed the Database (Optional)**
```sh
php artisan db:seed
```

### **8. Start the Queue Worker**
```sh
php artisan queue:work
```

### **9. Run the Local Server**
```sh
php artisan serve
```



## **Important Notes**
- Ensure the environment is correctly set up in the `.env` file.
- The system can be tested using Postman or similar tools.
- The system is secured against common attacks like **XSS, SQL Injection, CSRF**.

## Postman:

[Documentation Link](https://documenter.getpostman.com/view/42627461/2sAYdhHUtP)


Thank you for using this system!

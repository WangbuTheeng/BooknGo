# 📚 BOOKNGO – Project Design Review (PDR)

## 📌 1. Project Overview

BOOKNGO is a web-based bus ticketing platform specifically designed to cater to the high travel demand during Nepal’s major festivals. It aims to provide an easy-to-use online booking experience for passengers and robust management tools for bus operators. The system supports real-time seat selection, online payments, cancellations, analytics, and festival-specific features like surge pricing and alerts.

## ⚙️ 2. Technology Stack

*   **Backend:** Laravel 11 (PHP)
*   **Frontend:** Blade templating engine + Tailwind CSS
*   **Database:** MySQL
*   **Authentication:** Laravel Breeze / Jetstream / Fortify
*   **Notifications:** SMS, Email, Telegram bot (optional)
*   **Payment Gateway:** eSewa, Khalti (integration planned)
*   **Export/Reports:** PDF, Excel via Laravel Excel
*   **Charts:** Chart.js / Laravel Charts

## 🧩 3. System Modules

The system is divided into three main panels:

### 3.1. User Panel (Passenger)
*   Sign up / Login (email or phone)
*   Search buses by source, destination, date, operator
*   Real-time seat view and selection
*   Booking confirmation with ticket download (PDF, QR)
*   Payment via eSewa/Khalti (with cash fallback)
*   Booking history, cancellation/refund requests
*   Festival alerts and promotions
*   Multi-channel notifications (SMS/email/Telegram)

### 3.2. Operator Panel
*   Operator registration and dashboard access
*   Management of buses (with seat layout), routes, trip schedules, and fares
*   Management of promotional codes
*   View bookings and passenger manifests
*   Print passenger lists
*   Manage seat availability and ticket prices
*   Festival price modifiers
*   Revenue reporting

### 3.3. Admin Panel
*   Full system access and monitoring
*   Management of users (passengers and operators), buses, operators, routes, cities, bookings, payments, notifications, and promotional campaigns
*   Approve/reject operator registration
*   Push announcements and alerts
*   View advanced analytics (routes, demand, revenue)
*   Export reports (PDF, Excel)
*   Control festival fare logic and system settings

## 🗃️ 4. Database Design

The database schema is designed with 14 core tables to manage users, operators, bus details, routes, trips, bookings, payments, and system configurations.

### Table List:

1.  **`users`**: Stores all users (admin, operator, user).
2.  **`operators`**: Details of bus operators, linked to `users`.
3.  **`cities`**: List of cities for route definitions.
4.  **`routes`**: Bus routes between `cities`.
5.  **`buses`**: Bus details, linked to `operators`.
6.  **`seats`**: Individual seat details for each `bus`.
7.  **`trips`**: Scheduled bus trips, linked to `buses` and `routes`.
8.  **`bookings`**: Booking transactions, linked to `users` and `trips`.
9.  **`booking_seats`**: Mapping of `seats` to `bookings`.
10. **`payments`**: Payment records, linked to `bookings`.
11. **`promotions`**: Promotional codes and discounts, optionally linked to `operators`.
12. **`notifications`**: System notifications, linked to `users`.
13. **`system_settings`**: Global system configurations.
14. **`audit_logs`**: Records of system actions, optionally linked to `users`.

### Relationships Overview:

*   `users` (1)───(1) `operators`
*   `users` (1)───(∞) `bookings`
*   `operators` (1)───(∞) `buses`
*   `cities` (1)───(∞) `routes` (from & to)
*   `routes` (1)───(∞) `trips`
*   `buses` (1)───(∞) `trips`
*   `buses` (1)───(∞) `seats`
*   `trips` (1)───(∞) `bookings`
*   `bookings` (1)───(∞) `booking_seats`
*   `bookings` (1)───(1) `payments`
*   `operators` (1)───(∞) `promotions`
*   `users` (1)───(∞) `notifications`
*   `users` (1)───(∞) `audit_logs`

## 📈 5. Key Features

*   **Festival Fare Management:** Dynamic pricing adjustments during peak festival seasons.
*   **Real-time Seat Availability:** Live updates on seat selection to prevent double bookings.
*   **Multi-channel Notifications:** SMS, Email, and Telegram integration for booking confirmations, cancellations, and alerts.
*   **Comprehensive Reporting:** Detailed analytics for administrators and operators on revenue, popular routes, and booking trends.
*   **Secure Payment Gateway Integration:** Seamless and secure online transactions.
*   **User-friendly Interface:** Intuitive design for both passengers and operators.

## 🚀 6. Future Enhancements

*   **GPS Tracking:** Real-time bus location tracking for passengers.
*   **Driver Module:** Dedicated interface for bus drivers.
*   **Dynamic Route Optimization:** AI-driven route suggestions based on traffic and demand.
*   **Multi-language Support:** Localization for different regional languages.
*   **Mobile App:** Native Android/iOS applications.

# 📘 BOOKNGO – Festival-Optimized Bus Ticketing System

## 📌 Project Title
BOOKNGO – A Smart and Scalable Bus Ticket Booking System for Nepal's Festival Season

---

## 🎯 Objective

BOOKNGO is a web-based bus ticketing platform tailored for the high travel demand during Nepal’s major festivals like Dashain, Tihar, and Chhath. It allows passengers to easily book tickets online while providing operators with tools to manage buses, routes, and trips. The system supports real-time seat selection, online payment, cancellation, analytics, and festival-based features like surge pricing and alerts.

---

## ⚙️ Technology Stack

- **Backend:** Laravel 11 (PHP)
- **Frontend:** Blade templating engine + Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze / Jetstream / Fortify
- **Notifications:** SMS, Email, Telegram bot (optional)
- **Payment Gateway:** eSewa, Khalti (integration planned)
- **Export/Reports:** PDF, Excel via Laravel Excel
- **Charts:** Chart.js / Laravel Charts

---

## 🧩 System Modules

### 1. 👤 User Panel (Passenger)
- Sign up / Login via email or phone
- Search buses by:
  - Source & Destination
  - Travel date
  - Bus operator
- Real-time seat view and selection
- Booking confirmation with ticket download (PDF, QR)
- Payment via eSewa/Khalti (with fallback to cash mode)
- Booking history
- Cancel/refund request system
- Festival alerts and promotions
- Notification by SMS/email/Telegram

---

### 2. 🚌 Operator Panel
- Operator registration and dashboard access
- Add/manage:
  - Buses with seat layout
  - Routes and stops
  - Trip schedules and fares
  - Promotional codes
- View bookings and passenger manifests
- Print passenger lists
- Manage seat availability and ticket prices
- Festival price modifiers
- Revenue reporting

---

### 3. 🛠️ Admin Panel
- Full system access and monitoring
- Manage:
  - Users (passengers and operators)
  - Buses, operators, routes, and cities
  - Bookings and payments
  - Notifications and promotional campaigns
- Approve/reject operator registration
- Push announcements and alerts
- View advanced analytics (routes, demand, revenue)
- Export reports in PDF or Excel
- Control festival fare logic and system settings

---

## 🗃️ Database Tables (with Relationships)

### 1. **users**
Stores all users including admins, passengers, and operators.
```sql
id, name, email, phone, password, role (enum: admin, user, operator), email_verified_at, status, created_at, updated_at

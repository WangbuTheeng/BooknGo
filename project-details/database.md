# 📂 BOOKNGO – Database Schema

Below is the complete **MySQL** schema for BOOKNGO, expressed as `CREATE TABLE` statements. These definitions match the requirements outlined in your main `requirements.md` file and are ready to be converted into Laravel migration files.

---

## 1. `users`

```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(30) UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','operator','user') NOT NULL DEFAULT 'user',
  email_verified_at TIMESTAMP NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

---

## 2. `operators`

```sql
CREATE TABLE operators (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  company_name VARCHAR(150) NOT NULL,
  license_number VARCHAR(100),
  contact_info JSON,
  address TEXT,
  logo_url VARCHAR(255),
  verified BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_operators_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 3. `cities`

```sql
CREATE TABLE cities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  province VARCHAR(100),
  region VARCHAR(100)
) ENGINE=InnoDB;
```

---

## 4. `routes`

```sql
CREATE TABLE routes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_city_id INT UNSIGNED NOT NULL,
  to_city_id INT UNSIGNED NOT NULL,
  estimated_km DECIMAL(8,2),
  estimated_time TIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_routes_from_city FOREIGN KEY (from_city_id)
    REFERENCES cities(id),
  CONSTRAINT fk_routes_to_city FOREIGN KEY (to_city_id)
    REFERENCES cities(id)
) ENGINE=InnoDB;
```

---

## 5. `buses`

```sql
CREATE TABLE buses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  operator_id BIGINT UNSIGNED NOT NULL,
  registration_number VARCHAR(50) UNIQUE NOT NULL,
  name VARCHAR(100),
  type ENUM('AC','Deluxe','Normal','Sleeper') DEFAULT 'Normal',
  total_seats SMALLINT UNSIGNED,
  layout_config JSON,
  features JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_buses_operator FOREIGN KEY (operator_id)
    REFERENCES operators(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 6. `seats`

```sql
CREATE TABLE seats (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bus_id BIGINT UNSIGNED NOT NULL,
  seat_number VARCHAR(10) NOT NULL,
  position VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_bus_seat (bus_id, seat_number),
  CONSTRAINT fk_seats_bus FOREIGN KEY (bus_id)
    REFERENCES buses(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 7. `trips`

```sql
CREATE TABLE trips (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bus_id BIGINT UNSIGNED NOT NULL,
  route_id BIGINT UNSIGNED NOT NULL,
  departure_datetime DATETIME NOT NULL,
  arrival_time DATETIME,
  price DECIMAL(10,2) NOT NULL,
  is_festival_fare BOOLEAN DEFAULT FALSE,
  status ENUM('active','cancelled') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_trips_bus FOREIGN KEY (bus_id)
    REFERENCES buses(id) ON DELETE CASCADE,
  CONSTRAINT fk_trips_route FOREIGN KEY (route_id)
    REFERENCES routes(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 8. `bookings`

```sql
CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  trip_id BIGINT UNSIGNED NOT NULL,
  booking_code CHAR(10) UNIQUE,
  status ENUM('booked','cancelled') DEFAULT 'booked',
  total_amount DECIMAL(10,2),
  payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
  cancellation_reason VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_user FOREIGN KEY (user_id)
    REFERENCES users(id),
  CONSTRAINT fk_bookings_trip FOREIGN KEY (trip_id)
    REFERENCES trips(id)
) ENGINE=InnoDB;
```

---

## 9. `booking_seats`

```sql
CREATE TABLE booking_seats (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  seat_id BIGINT UNSIGNED NOT NULL,
  seat_number VARCHAR(10),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_seats_booking FOREIGN KEY (booking_id)
    REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_booking_seats_seat FOREIGN KEY (seat_id)
    REFERENCES seats(id) ON DELETE CASCADE,
  UNIQUE KEY uk_booking_seat (booking_id, seat_id)
) ENGINE=InnoDB;
```

---

## 10. `payments`

```sql
CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  method ENUM('eSewa','Khalti','Cash') NOT NULL,
  payment_status ENUM('pending','success','failed','refunded') DEFAULT 'pending',
  transaction_id VARCHAR(100),
  payment_time DATETIME,
  response_log JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id)
    REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 11. `promotions`

```sql
CREATE TABLE promotions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  promo_code VARCHAR(50) UNIQUE NOT NULL,
  operator_id BIGINT UNSIGNED,
  discount_percent DECIMAL(5,2) NOT NULL,
  min_amount DECIMAL(10,2),
  max_uses INT UNSIGNED,
  user_limit INT UNSIGNED,
  valid_from DATE,
  valid_till DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_promotions_operator FOREIGN KEY (operator_id)
    REFERENCES operators(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

---

## 12. `notifications`

```sql
CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  type ENUM('SMS','Email','Telegram') NOT NULL,
  message TEXT NOT NULL,
  status ENUM('pending','sent','failed') DEFAULT 'pending',
  sent_at DATETIME,
  channel_response JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 13. `system_settings`

```sql
CREATE TABLE system_settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  `value` TEXT NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

---

## 14. `audit_logs`

```sql
CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED,
  action VARCHAR(100) NOT NULL,
  module VARCHAR(100),
  details JSON,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

---

## 🔗 Relationship Overview

```
users (1)───(1) operators
users (1)───(∞) bookings
operators (1)───(∞) buses
cities (1)───(∞) routes (from)
cities (1)───(∞) routes (to)
routes (1)───(∞) trips
buses (1)───(∞) trips
buses (1)───(∞) seats
trips (1)───(∞) bookings
bookings (1)───(∞) booking_seats
bookings (1)───(1) payments
operators (1)───(∞) promotions
users (1)───(∞) notifications
```

> **Tip:** Convert each `CREATE TABLE` block into a Laravel migration by replacing data types with Laravel's fluent syntax (`$table->bigIncrements('id')`, `$table->string('name')`, etc.) and adding appropriate indexes.

---

**End of Database Schema**

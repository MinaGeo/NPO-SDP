# Non-Profit Organization Management System (NPO-SDP)

The **Non-Profit Organization Management System (NPO-SDP)** is a feature-rich web application designed to streamline the operations of non-profit organizations. It leverages modern software design patterns to ensure scalability, maintainability, and efficiency, enabling effective management of donations, an online shop, events, and user communications.

## Features and Core Components

### 1. User Roles and Permissions
- **Guest**: View-only access to items, donations, and events.
- **User**: Register, log in, donate, purchase items, and register for events.
- **Admin**: Full access to manage donations, events, items, and notifications.

### 2. Event Management
- Create, edit, and delete events with details such as name, description, location, date, and type.
- Users can register for events, track participation, and receive updates.

### 3. Online Shop
- Browse and purchase items for fundraising purposes.
- Sort and filter items by category, price, or other attributes.
- Manage items in a shopping cart with tax and discount calculations.

### 4. Donation Management
- Support for monetary and non-monetary donations.
- Multiple payment methods, including PayPal and credit cards.
- Receipt generation and secure donation record storage.

### 5. Notification System
- Automated notifications for user actions such as event registrations.
- Notify users via email or SMS about important updates.

### 6. User Control Panel (UCP)
- Personalized dashboard for managing user profiles and activities.
- Review transaction history and participation in events.

## Design Patterns

The system architecture incorporates several design patterns for robustness and modularity:

- **MVC Architecture**: Separates data (Model), business logic (Controller), and presentation (View).
- **Strategy Pattern**: Enables flexible sorting, filtering, and payment processing.
- **Decorator Pattern**: Extends cart functionalities like tax and discount calculations.
- **Observer Pattern**: Powers the notification system for event-driven communications.
- **Singleton Pattern**: Ensures a single instance of the database connection for efficient resource management.

## Database Schema

The system uses a well-structured database to manage its core entities:
- **Users**: Stores user details and credentials.
- **Donations**: Tracks all donation records.
- **Events**: Manages event details and registrations.
- **Shop Items**: Catalogs merchandise for sale.
- **Cart**: Tracks user selections for checkout.
- **Notifications**: Logs notifications and communication records.

## Team Contributions

| **Name**               | **Responsibilities**                                                                                     |
|------------------------|---------------------------------------------------------------------------------------------------------|
| Rafik Tamer Magdy      | Sorting & Filtering (Strategy), Volunteer (MVC), DB integration                                         |
| Mark Bassem Heshmat    | Notifications (Observer), Routing                                                                      |
| Ahmed Wael Ibrahim     | User, Login, Register (Strategy, MVC), Homepage and navigation                                         |
| Mina Morgan Mounir     | Shop, Cart (Decorator, MVC), Documentation                                                             |
| Mina George Fawzy      | Database (Singleton), Event (MVC), DB maintenance                                                     |
| Mostafa Ayman Mostafa  | Donations and Payments (Strategy, MVC)                                                                 |

## Installation

1. Clone the repository:  
   ```bash
   git clone https://github.com/MinaGeo/NPO-SDP.git
   ```
2. Install dependencies and configure the database connection.
3. Start the application using your preferred web server or development environment.

## Links
- GitHub Repository: [NPO-SDP](https://github.com/MinaGeo/NPO-SDP)
- [Class Diagram](https://drive.google.com/file/d/1CuZVmnf1R9mgq_YPSInn_DTIrbERaRsS/view?usp=sharing)

---

Feel free to customize further based on the specifics of your repository structure or additional information you'd like to include!

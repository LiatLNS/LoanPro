# LoanPro Full-Stack Inventory Management System

A robust, modern, and high-performance full-stack inventory management system built with a **Laravel 11** RESTful API backend and a **React/Vite** responsive frontend. Designed with clean architecture, strict backend and frontend stock validations, bilingual English standardization, dark mode persistence, and real-time calculation overlays.

---

## Features

### Backend (Laravel 11 API)
- **RESTful Endpoints**: Complete product lifecycle management (`CRUD`) supporting standard resource routing.
- **Strict Validations**: Enforced integer validation for stock levels and float precision validation for pricing and weights.
- **API Key Authentication**: Secure middleware (`VerifyApiKey`) protecting data mutation and access.
- **Bulk CSV Ingestion**: High-efficiency pipeline for importing products via CSV files.
- **CORS & Preflight Handling**: Fully configured `.htaccess` and CORS middleware to seamlessly communicate with frontend clients.

### Frontend (React & Vite)
- **Responsive Dashboard Layout**: Two-column layout featuring a dedicated sidebar form and main inventory data grid.
- **Dark Mode Support**: Persistent light/dark mode theme toggle with custom styling variables.
- **Interactive Data Grid**: 
  - Native row hover tooltips displaying complete product descriptions.
  - Scaled large checkbox selections (`transform: scale(1.55)`) with batch deletion capabilities.
  - Live inventory valuation and total weight summary indicators.
- **Dynamic Modals & Validation**:
  - Modal interfaces for sales and restock operations.
  - Dynamic replacement layout that substitutes subtotal cards with alert warnings (such as insufficient stock or invalid float inputs) without modifying the modal container dimensions.
  - Strict input keydown restrictions to completely block decimal/floating points (`.` and `,`) in quantity fields.
- **Localized Formatting**: Standardized number formatting using comma separators (`toLocaleString()`) across quantities, prices, and weights.

---

## Tech Stack

- **Frontend**: React, Vite, Axios, Modern CSS3 Grid & Flexbox.
- **Backend**: Laravel 11, PHP 8.2+, MySQL / SQLite.
- **Tooling**: Composer, Node.js / npm, Git.

---

## Project Structure

```text
├── app/                  # Laravel application source code (Controllers, Middleware)
├── routes/               # API and web routing definitions
├── src/                  # React frontend components & services
│   ├── services/         # Axios API configuration modules
│   ├── App.jsx           # Main application dashboard
│   ├── ProductForm.jsx   # Product creation & editing form
│   └── App.css           # Global custom styling & dark mode rules
└── ...
```

---

## Getting Started & Installation

Follow these steps to set up and run the project locally on your machine.

### Prerequisites

Ensure you have the following installed on your system:
- PHP `>= 8.2` and Composer
- Node.js and npm
- MySQL or SQLite database server

---

### 1. Backend Setup (Laravel 11 API)

1. Open your terminal and navigate to the backend repository directory.
2. Install PHP dependencies via Composer:
   ```bash
   composer install
   ```
3. Copy the environment configuration file and generate the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure your database credentials inside the `.env` file (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
5. Run the database migrations:
   ```bash
   php artisan migrate
   ```
6. Start the local Laravel development server:
   ```bash
   php artisan serve
   ```
   *(The backend API will run at `http://127.0.0.1:8000`)*

---

### 2. Frontend Setup (React & Vite)

1. Open a new terminal window and navigate to your frontend directory.
2. Install JavaScript dependencies:
   ```bash
   npm install
   ```
3. Configure your Axios base URL and headers inside your API service (`src/services/api.js`) to target your backend API endpoint and pass the required API Key header.
4. Start the Vite development server:
   ```bash
   npm run dev
   ```
5. Open your browser and navigate to the local URL provided by Vite (typically `http://localhost:5173`).

---

## License & Terms of Use

This project is source-available for educational, personal, and evaluation purposes only. 

- **Non-Commercial Use**: You are free to view, copy, modify, and use this software for non-commercial purposes. 
- **Restrictions**: You may **not** use, distribute, or incorporate this software (or any significant part of it) into any commercial products, services, or revenue-generating operations without explicit written permission from the author.

---

Example CSV downloaded on 12/Oct/2026.
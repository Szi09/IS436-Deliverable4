# Restaurant Online Ordering System

## Project Overview
This repository contains a fully functional restaurant ordering website prototype built for IS436 Deliverable 4. The application allows customers to browse menu items, add products to a cart, select pickup or delivery, complete checkout, and view order confirmation. It also includes an admin interface for managing orders and monitoring store activity.

## Features
- Responsive menu browsing with category selection
- Add-to-cart and cart editing functionality
- Checkout with delivery or pickup selection
- Payment choice between card and cash (cash restricted to pickup only)
- Order confirmation with ETA
- Admin dashboard with statistics and recent orders
- Admin order management with status updates
- Browser localStorage persistence for orders and cart state
- Prototype UI screens for key application areas

## Files and Structure
- `index.html` — Home/menu page
- `product_detail.html` — Product detail view
- `cart.html` — Shopping cart page
- `checkout.html` — Checkout form and order placement
- `order-confirmation.html` — Order summary page
- `admin_dashboard.html` — Admin dashboard analytics
- `admin_orders.html` — Admin order management page
- `admin_products.html`, `admin_categories.html`, `admin_contacts.html`, `admin_settings.html` — Admin management pages
- `products.json` — Menu data exported from the database
- `categories.json` — Category data exported from the database
- `data.js` — Static data loader and helper functions
- `store.js` — Cart, orders, and analytics state manager
- `Dockerfile` — Container build definition
- `.github/workflows/docker-image.yml` — Docker Hub build/push workflow
- `prototypes/` — UI prototype pages for 5 core screens

## Running Locally
1. Open the project folder in a local web server environment.
2. Ensure `index.html`, `products.json`, and `categories.json` are in the same root folder.
3. Open `index.html` in a browser, or use a local server for full fetch support.

### Recommended local server
```bash
python -m http.server 8000
```
Then visit `http://localhost:8000`.

## Docker Deployment
This project is container-ready using NGINX.

### Build locally
```bash
docker build -t yourusername/restaurant-ordering:latest .
```

### Run locally
```bash
docker run -p 8080:80 yourusername/restaurant-ordering:latest
```
Then visit `http://localhost:8080`.

## GitHub Actions Docker Hub Deployment
The workflow file is located at `.github/workflows/docker-image.yml`.

### Required secrets
- `DOCKERHUB_USERNAME` — Docker Hub username
- `DOCKERHUB_TOKEN` — Docker Hub access token or password
- `DOCKERHUB_REPO` — Docker Hub repository, e.g. `yourusername/restaurant-ordering`

### Example image pull command
```bash
docker pull yourusername/restaurant-ordering:latest
```

## User Guide
### Customer flow
1. Visit `index.html`.
2. Use the search field or `Menu` dropdown to filter products.
3. Click `Add to Cart` to add an item.
4. Open `Cart` to update quantities or remove items.
5. Click `Proceed to Checkout` to place your order.
6. Select pickup or delivery and choose payment method.
7. Complete checkout and review your order on the confirmation page.

### Admin flow
1. Open `admin_login.html` and sign in.
2. Visit `Admin Dashboard` for order statistics.
3. Open `Admin Orders` to view and update order statuses.
4. Manage products, categories, and contact messages from the admin pages.

## UI Standards and Design Decisions
- Simple Bootstrap-based layout for responsive behavior
- Consistent navbar across user screens
- Clear call-to-action buttons in blue for primary actions
- Form validation for required checkout fields
- Admin interface uses a dark header and card-based statistics
- Data loaded from JSON files that mirror database tables for prototype compatibility

## Data and Database Mapping
The UI consumes data files derived from the database:
- `products.json` mirrors `t_IS448_F25_products` joined with categories
- `categories.json` mirrors `t_IS448_F25_categories`
- `settings.json` may be used for site theming if added

Order state is stored in browser `localStorage` for this prototype demo.

## Project Plan (Five Phases)
1. **Planning**
   - Define scope, use cases, and UI requirements
   - Identify customer and admin workflows
2. **Analysis**
   - Create ERD and data model
   - Export JSON from MySQL workbench for menu data
3. **Design**
   - Build prototypes for home, menu, cart, checkout, and admin dashboard
   - Define interface standards and navigation structure
4. **Implementation**
   - Develop HTML/CSS/JavaScript frontend
   - Add cart and checkout logic
   - Create admin dashboard and order management pages
   - Containerize the site with Docker
5. **Testing**
   - Validate navigation, cart persistence, and order flow
   - Confirm admin dashboard data loading
   - Test Docker container and deployment workflow

## Prototype Files
The `prototypes/` folder contains HTML prototypes for the five key screens:
- `prototypes/home.html`
- `prototypes/menu.html`
- `prototypes/cart.html`
- `prototypes/checkout.html`
- `prototypes/admin_dashboard.html`

## Notes
- This project is designed as a static containerized prototype.
- Use Docker Hub secrets in GitHub Actions to automatically build and publish the image.

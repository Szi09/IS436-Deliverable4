# Restaurant Store — Static Site (No PHP)

## File map (PHP → HTML/JS)

| Original PHP file           | Converted to             |
|-----------------------------|--------------------------|
| `index.php`                 | `index.html`             |
| `product_detail.php`        | `product_detail.html`    |
| `contact.php`               | `contact.html`           |
| `admin_login.php`           | `admin_login.html`       |
| `admin_logout.php`          | `admin_logout.html`      |
| `admin_dashboard.php`       | `admin_dashboard.html`   |
| `admin_categories.php`      | `admin_categories.html`  |
| `admin_products.php`        | `admin_products.html`    |
| `admin_contacts.php`        | `admin_contacts.html`    |
| `admin_settings.php`        | `admin_settings.html`    |
| `css/styles.php`            | `css/styles.css`         |
| `js/main.php`               | `js/main.js`             |
| `includes/db_connect.php`   | `js/data.js`             |
| `includes/functions.php`    | `js/data.js`             |

---

## How to export real data from MySQL Workbench

### products.json
```sql
SELECT p.id, p.name, p.description, p.price, p.image_name,
       p.category_id, c.name AS category_name
FROM   t_IS448_F25_products p
LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
ORDER BY p.name;
```
Results Grid → Export → JSON → save as **`products.json`** in this folder.

### categories.json
```sql
SELECT id, name, description
FROM   t_IS448_F25_categories
ORDER BY name;
```
Save as **`categories.json`**.

### settings.json  *(optional — site colour customisation)*
```sql
SELECT color_h1, color_h2, color_h3, color_p,
       color_header_bg, color_body_bg, color_footer_bg
FROM   t_IS448_F25_site_settings LIMIT 1;
```
Save as **`settings.json`**. If absent, default colours are used.

---

## Admin login

Credentials are read from **`admin_credentials.json`** (plain-text, no bcrypt).  
Default: `admin` / `admin123` — change before deploying.

---

## Docker (nginx)

```dockerfile
FROM nginx:alpine
COPY . /usr/share/nginx/html
```

```bash
docker build -t restaurant-site .
docker run -p 8080:80 restaurant-site
```
Open `http://localhost:8080`.  No PHP module required.

---

## Where admin edits are stored

Because there is no server, admin CRUD changes (categories, products, site colours) are
persisted to **browser localStorage** on the same origin. They survive page refreshes
but are per-browser. To make edits permanent, export the updated data from the admin
panel back to your JSON files and commit them to GitHub.

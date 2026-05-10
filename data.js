/**
 * data.js  —  replaces includes/db_connect.php + includes/functions.php
 *
 * All data that previously came from MySQL via PDO now comes from
 * two JSON files you export from MySQL Workbench:
 *
 *   products.json   — rows from t_IS448_F25_products JOIN t_IS448_F25_categories
 *   categories.json — rows from t_IS448_F25_categories
 *   settings.json   — single row from t_IS448_F25_site_settings  (optional)
 *
 * HOW TO EXPORT FROM MYSQL WORKBENCH
 * ─────────────────────────────────────────────────────────────────
 * 1. Run each query below in Workbench.
 * 2. In the Results Grid toolbar click the export icon → JSON.
 * 3. Save the file to this project root with the name shown.
 *
 * products.json
 *   SELECT p.id, p.name, p.description, p.price, p.image_name,
 *          p.category_id, c.name AS category_name
 *   FROM   t_IS448_F25_products p
 *   LEFT JOIN t_IS448_F25_categories c ON p.category_id = c.id
 *   ORDER BY p.name;
 *
 * categories.json
 *   SELECT id, name, description
 *   FROM   t_IS448_F25_categories
 *   ORDER BY name;
 *
 * settings.json  (optional – site color customisation)
 *   SELECT color_h1, color_h2, color_h3, color_p,
 *          color_header_bg, color_body_bg, color_footer_bg
 *   FROM   t_IS448_F25_site_settings LIMIT 1;
 *   → Export as a JSON array; the code reads element [0].
 * ─────────────────────────────────────────────────────────────────
 */

// Default site colours — used when settings.json is absent or empty.
// These match the PHP get_site_settings() defaults exactly.
const SITE_SETTINGS_DEFAULTS = {
    color_h1:         '#333333',
    color_h2:         '#444444',
    color_h3:         '#555555',
    color_p:          '#333333',
    color_header_bg:  '#f8f9fa',
    color_body_bg:    '#ffffff',
    color_footer_bg:  '#f8f9fa'
};

/**
 * Load and apply site colour settings.
 * Tries settings.json first; falls back to SITE_SETTINGS_DEFAULTS.
 * Returns the settings object so callers can use it if needed.
 */
async function loadAndApplySiteSettings() {
    let settings = { ...SITE_SETTINGS_DEFAULTS };
    try {
        const res = await fetch('settings.json');
        if (res.ok) {
            const data = await res.json();
            const row  = Array.isArray(data) ? data[0] : data;
            if (row) settings = { ...settings, ...row };
        }
    } catch (_) { /* use defaults */ }

    // Apply colours to <style> tag — mirrors what PHP injected inline
    const style = document.createElement('style');
    style.textContent = `
        body                { background-color: ${settings.color_body_bg}; }
        header.site-header  { background-color: ${settings.color_header_bg}; }
        footer.site-footer  { background-color: ${settings.color_footer_bg}; }
        h1 { color: ${settings.color_h1}; }
        h2 { color: ${settings.color_h2}; }
        h3 { color: ${settings.color_h3}; }
        p  { color: ${settings.color_p};  }
    `;
    document.head.appendChild(style);
    return settings;
}

/**
 * Fetch all categories (replaces get_all_categories()).
 */
async function getAllCategories() {
    try {
        const res = await fetch('categories.json');
        if (!res.ok) return [];
        return await res.json();
    } catch (_) { return []; }
}

/**
 * Fetch all products, with optional search + category filter
 * (replaces get_products()).
 */
async function getProducts(search = null, categoryId = null) {
    try {
        const res = await fetch('products.json');
        if (!res.ok) return [];
        let products = await res.json();
        products.sort((a, b) => a.name.localeCompare(b.name));
        if (search && search.trim() !== '') {
            const q = search.trim().toLowerCase();
            products = products.filter(p =>
                (p.name        && p.name.toLowerCase().includes(q)) ||
                (p.description && p.description.toLowerCase().includes(q))
            );
        }
        if (categoryId && categoryId > 0) {
            products = products.filter(p => p.category_id === categoryId);
        }
        return products;
    } catch (_) { return []; }
}

/**
 * Fetch single product by id (replaces get_product_by_id()).
 */
async function getProductById(id) {
    try {
        const res = await fetch('products.json');
        if (!res.ok) return null;
        const products = await res.json();
        return products.find(p => p.id === id) || null;
    } catch (_) { return null; }
}

/** HTML-escape a string (replaces htmlspecialchars). */
function esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/** Parse URL query params — mirrors $_GET access. */
function getQueryParams() {
    const params = {};
    new URLSearchParams(window.location.search).forEach((v, k) => { params[k] = v; });
    return params;
}

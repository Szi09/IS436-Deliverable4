/**
 * data.js  —  API client for restaurant ordering system
 *
 * Connects to PHP API (api.php) which interfaces with MySQL database
 */

// Default site colours — used when settings are not available
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
 */
async function loadAndApplySiteSettings() {
    // For now, just apply defaults since we don't have a settings table yet
    applySiteSettings(SITE_SETTINGS_DEFAULTS);
}

/**
 * Apply site colour settings to CSS variables
 */
function applySiteSettings(settings) {
    const root = document.documentElement;
    root.style.setProperty('--color-h1', settings.color_h1);
    root.style.setProperty('--color-h2', settings.color_h2);
    root.style.setProperty('--color-h3', settings.color_h3);
    root.style.setProperty('--color-p', settings.color_p);
    root.style.setProperty('--color-header-bg', settings.color_header_bg);
    root.style.setProperty('--color-body-bg', settings.color_body_bg);
    root.style.setProperty('--color-footer-bg', settings.color_footer_bg);
}

/**
 * Get all categories from API
 */
async function getAllCategories() {
    try {
        const response = await fetch('api.php?action=categories');
        if (!response.ok) throw new Error('Failed to fetch categories');
        const data = await response.json();
        // Transform to match expected format
        return data.map(item => ({
            id: item.item_id,
            name: item.name
        }));
    } catch (error) {
        console.error('Error fetching categories:', error);
        return [];
    }
}

/**
 * Get products from API with optional search and category filtering
 */
async function getProducts(search = null, categoryId = null) {
    try {
        const params = new URLSearchParams();
        params.append('action', 'products');
        if (search) params.append('search', search);
        if (categoryId) params.append('category_id', categoryId);

        const response = await fetch(`api.php?${params}`);
        if (!response.ok) throw new Error('Failed to fetch products');
        const data = await response.json();

        // Transform to match expected format
        return data.map(item => ({
            id: item.item_id,
            name: item.name,
            description: `Delicious ${item.name} prepared fresh.`,
            price: item.price.toString(),
            image_name: '',
            category_id: 1,
            category_name: 'Main Dishes'
        }));
    } catch (error) {
        console.error('Error fetching products:', error);
        return [];
    }
}

/**
 * Get single product by ID from API
 */
async function getProductById(id) {
    try {
        const response = await fetch(`api.php?action=product&id=${id}`);
        if (!response.ok) throw new Error('Failed to fetch product');
        const data = await response.json();
        return {
            id: data.item_id,
            name: data.name,
            description: `Delicious ${data.name} prepared fresh.`,
            price: data.price.toString(),
            image_name: '',
            category_id: 1,
            category_name: 'Main Dishes'
        };
    } catch (error) {
        console.error('Error fetching product:', error);
        return null;
    }
}

/**
 * Get all orders from API
 */
async function getOrders() {
    try {
        const response = await fetch('api.php?action=orders');
        if (!response.ok) throw new Error('Failed to fetch orders');
        return await response.json();
    } catch (error) {
        console.error('Error fetching orders:', error);
        return [];
    }
}

/**
 * Create new order via API
 */
async function createOrder(orderData) {
    try {
        const response = await fetch('api.php?action=orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData)
        });
        if (!response.ok) throw new Error('Failed to create order');
        return await response.json();
    } catch (error) {
        console.error('Error creating order:', error);
        throw error;
    }
}

/**
 * Update order status via API
 */
async function updateOrderStatus(orderId, status) {
    try {
        const response = await fetch('api.php?action=orders', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId, status: status })
        });
        if (!response.ok) throw new Error('Failed to update order');
        return await response.json();
    } catch (error) {
        console.error('Error updating order:', error);
        throw error;
    }
}
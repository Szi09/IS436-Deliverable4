/**
 * store.js  —  Central state management
 * Handles: cart, orders, tasks, analytics
 * All state persisted to localStorage so it survives page navigation.
 */

// ─────────────────────────────────────────────
//  CART
// ─────────────────────────────────────────────
const Cart = {
    _key: 'restaurant_cart',

    get() {
        return JSON.parse(localStorage.getItem(this._key) || '[]');
    },
    save(items) {
        localStorage.setItem(this._key, JSON.stringify(items));
        Cart._dispatch();
    },
    _dispatch() {
        window.dispatchEvent(new Event('cart-updated'));
    },

    add(product) {
        const items = this.get();
        const existing = items.find(i => i.id === product.id);
        if (existing) {
            existing.qty += 1;
        } else {
            items.push({ ...product, qty: 1 });
        }
        this.save(items);
    },

    remove(productId) {
        const items = this.get().filter(i => i.id !== productId);
        this.save(items);
    },

    updateQty(productId, qty) {
        const items = this.get();
        const item = items.find(i => i.id === productId);
        if (item) {
            if (qty <= 0) return this.remove(productId);
            item.qty = qty;
        }
        this.save(items);
    },

    clear() {
        localStorage.removeItem(this._key);
        Cart._dispatch();
    },

    count() {
        return this.get().reduce((sum, i) => sum + i.qty, 0);
    },

    subtotal() {
        return this.get().reduce((sum, i) => sum + parseFloat(i.price) * i.qty, 0);
    },

    tax(rate = 0.08) {
        return this.subtotal() * rate;
    },

    total(deliveryFee = 0) {
        return this.subtotal() + this.tax() + deliveryFee;
    }
};

// ─────────────────────────────────────────────
//  ORDERS
// ─────────────────────────────────────────────
const Orders = {
    async get() {
        try {
            return await getOrders();
        } catch (error) {
            console.error('Failed to fetch orders:', error);
            return [];
        }
    },

    async create(orderData) {
        try {
            // Transform cart items to order items format
            const items = Cart.get().map(item => ({
                id: item.id,
                name: item.name,
                price: parseFloat(item.price),
                quantity: item.qty
            }));

            const orderPayload = {
                name: orderData.customer.name,
                email: orderData.customer.email,
                phone: orderData.customer.phone,
                order_type: orderData.delivery.method === 'delivery' ? 'delivery' : 'pickup',
                total_amount: Cart.total(orderData.delivery.method === 'delivery' ? 5.00 : 0),
                items: items,
                address: orderData.customer.address || '',
                city: orderData.customer.city || '',
                state: orderData.customer.state || '',
                zip_code: orderData.customer.zipCode || '',
                estimated_time: Orders.calcETA(orderData.delivery.method)
            };

            const result = await createOrder(orderPayload);

            // Clear cart after successful order
            Cart.clear();

            // Track for analytics (still using localStorage for now)
            Analytics.recordOrder({
                id: result.order_id,
                total: orderPayload.total_amount,
                items: items.length,
                type: orderPayload.order_type
            });

            return {
                id: result.order_id,
                ...orderPayload,
                status: 'pending',
                createdAt: new Date().toISOString()
            };
        } catch (error) {
            console.error('Failed to create order:', error);
            throw error;
        }
    },

    async getById(id) {
        const orders = await this.get();
        return orders.find(o => o.order_id == id) || null;
    },

    async updateStatus(id, status) {
        try {
            await updateOrderStatus(id, status);
            // Dispatch event for UI updates
            window.dispatchEvent(new Event('orders-updated'));
        } catch (error) {
            console.error('Failed to update order status:', error);
            throw error;
        }
    },

    async getByDate(dateStr) {
        const orders = await this.get();
        return orders.filter(o => o.order_date.startsWith(dateStr));
    },

    async todayOrders() {
        const today = new Date().toISOString().slice(0, 10);
        return await this.getByDate(today);
    },

    // ETA calculation (minutes from now)
    calcETA(type) {
        const base = type === 'delivery' ? 35 : 20;
        return base + Math.floor(Math.random() * 10);
    }
};

// ─────────────────────────────────────────────
//  TASKS
// ─────────────────────────────────────────────
const Tasks = {
    _key: 'restaurant_tasks',

    get() {
        return JSON.parse(localStorage.getItem(this._key) || '[]');
    },
    save(tasks) {
        localStorage.setItem(this._key, JSON.stringify(tasks));
    },

    add(task) {
        const tasks = this.get();
        tasks.unshift({
            id: 'TASK-' + Date.now(),
            createdAt: new Date().toISOString(),
            done: false,
            ...task
        });
        this.save(tasks);
    },

    toggle(id) {
        const tasks = this.get().map(t => t.id === id ? { ...t, done: !t.done } : t);
        this.save(tasks);
    },

    delete(id) {
        this.save(this.get().filter(t => t.id !== id));
    },

    todayTasks() {
        const today = new Date().toISOString().slice(0, 10);
        return this.get().filter(t => t.createdAt.startsWith(today) || !t.done);
    }
};

// ─────────────────────────────────────────────
//  ANALYTICS
// ─────────────────────────────────────────────
const Analytics = {
    _key: 'restaurant_analytics',

    get() {
        return JSON.parse(localStorage.getItem(this._key) || '{"items":{},"daily":{}}');
    },
    save(data) {
        localStorage.setItem(this._key, JSON.stringify(data));
    },

    recordOrder(order) {
        const data = this.get();
        const day  = order.createdAt.slice(0, 10);

        // Track item popularity
        (order.items || []).forEach(item => {
            if (!data.items[item.id]) {
                data.items[item.id] = { name: item.name, count: 0, revenue: 0 };
            }
            data.items[item.id].count   += item.qty;
            data.items[item.id].revenue += parseFloat(item.price) * item.qty;
        });

        // Track daily totals
        if (!data.daily[day]) data.daily[day] = { orders: 0, revenue: 0 };
        data.daily[day].orders  += 1;
        data.daily[day].revenue += order.total || 0;

        this.save(data);
    },

    popularItems(limit = 10) {
        const data  = this.get();
        return Object.values(data.items)
            .sort((a, b) => b.count - a.count)
            .slice(0, limit);
    },

    dailyRevenue(days = 7) {
        const data   = this.get();
        const result = [];
        for (let i = days - 1; i >= 0; i--) {
            const d   = new Date();
            d.setDate(d.getDate() - i);
            const key = d.toISOString().slice(0, 10);
            result.push({ date: key, ...(data.daily[key] || { orders: 0, revenue: 0 }) });
        }
        return result;
    }
};

// ─────────────────────────────────────────────
//  CART BADGE  (update any element with id="cart-count")
// ─────────────────────────────────────────────
function updateCartBadge() {
    const badge = document.getElementById('cart-count');
    if (!badge) return;
    const count = Cart.count();
    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline-flex' : 'none';
}

window.addEventListener('cart-updated', updateCartBadge);
document.addEventListener('DOMContentLoaded', updateCartBadge);

// ─────────────────────────────────────────────
//  ADDRESS VALIDATION  (basic)
// ─────────────────────────────────────────────
function validateAddress(addr) {
    if (!addr || addr.trim().length < 10) return false;
    // Must contain a number (street number) and at least one word
    return /\d/.test(addr) && /[a-zA-Z]{2,}/.test(addr);
}

// ─────────────────────────────────────────────
//  STATUS BADGE HELPER
// ─────────────────────────────────────────────
function statusBadge(status) {
    const map = {
        pending:   'bg-warning text-dark',
        preparing: 'bg-info text-dark',
        ready:     'bg-primary',
        delivered: 'bg-success',
        'picked up': 'bg-success',
        completed: 'bg-success',
        cancelled: 'bg-danger'
    };
    const cls = map[status] || 'bg-secondary';
    return `<span class="badge ${cls}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

// ─────────────────────────────────────────────
//  SEED DEMO DATA (first run only)
// ─────────────────────────────────────────────
function seedDemoData() {
    if (localStorage.getItem('demo_seeded')) return;

    // Seed a few demo orders
    const demoItems = [
        { id: 1, name: 'Margherita Pizza', price: '12.99', qty: 2 },
        { id: 3, name: 'Caesar Salad',     price: '9.99',  qty: 1 }
    ];
    Orders.create({ items: demoItems, type: 'delivery', address: '45 Oak Lane, Baltimore, MD 21201', name: 'Jane Smith',   phone: '555-0101', total: 38.80, eta: 30 });
    Orders.create({ items: [{ id: 2, name: 'BBQ Chicken Pizza', price: '14.99', qty: 1 }], type: 'pickup',   address: '', name: 'Mike Johnson', phone: '555-0202', total: 16.19, eta: 20 });

    // Seed a few tasks
    Tasks.add({ title: 'Restock napkins at station 2', priority: 'low' });
    Tasks.add({ title: 'Check refrigerator temperature', priority: 'high' });
    Tasks.add({ title: 'Update daily specials board', priority: 'medium' });

    localStorage.setItem('demo_seeded', '1');
}

document.addEventListener('DOMContentLoaded', seedDemoData);

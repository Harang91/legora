// src/api.js

const API_BASE = '/api';

async function sendRequest(endpoint, method = 'GET', data = null) {
    const options = { 
        method: method,
        headers: {} 
    };

    if (data) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, options);
        const text = await response.text();

        try {
            return JSON.parse(text);
        } catch (err) {
            console.error("Szerver hiba (nem JSON):", text);
            return { status: 'error', message: text || 'Szerver hiba' };
        }
    } catch (error) {
        console.error("Hálózati hiba:", error);
        return { status: 'error', message: 'Hálózati hiba' };
    }
}

export const api = {
 
    login: (username, password) => 
        sendRequest('/admin/admin_login.php', 'POST', { username, password }),
    
    
    getStats: () =>
        sendRequest('/admin/get_stats.php'),

    
    getUsers: () => 
        sendRequest('/admin/admin_get_user_list.php'),
    
    deleteUser: (id) => 
        sendRequest('/admin/admin_delete_user.php', 'POST', { id }),
    
    toggleUser: (id) => 
        sendRequest('/admin/toggle_user.php', 'POST', { id }),

    restoreUser: (id) => 
        sendRequest('/admin/admin_restore_user.php', 'POST', { id }),

   
    getListings: () => 
        sendRequest('/admin/admin_get_listings_list.php'),

    deleteListing: (id) => 
        sendRequest('/admin/admin_delete_listing.php', 'POST', { id }),

    restoreListing: (id) => 
        sendRequest('/admin/admin_restore_listing.php', 'POST', { id }),
};
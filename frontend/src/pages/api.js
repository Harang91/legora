
const API_URL = 'http://localhost/legora/admin'; 

async function sendRequest(endpoint, method = 'GET', data = null) {
    const options = { method: method };

    
    if (data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        options.body = formData;
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, options);
        const text = await response.text();

        try {
           
            const json = JSON.parse(text);
            return json;
        } catch (err) {
            
            console.error("Szerver hiba (nem JSON válasz):", text);
            throw new Error("A szerver hibás választ küldött. Részletek a konzolban (F12).");
        }
    } catch (error) {
        console.error("Hálózati hiba:", error);
        throw error;
    }
}


export const api = {
    login: (username, password) => 
        sendRequest('/admin_login.php', 'POST', { username, password }),
    
    getUsers: () => 
        sendRequest('/admin_get_user_list.php'),
    
    
    deleteUser: (id) => 
        sendRequest('/admin_delete_user.php', 'POST', { id }),
    
    restoreUser: (id) => 
        sendRequest('/admin_restore_user.php', 'POST', { id }),

    getListings: () => 
        sendRequest('/admin_get_listings_list.php'),

    
    deleteListing: (id) => 
        sendRequest('/admin_delete_listing.php', 'POST', { id }),

    restoreListing: (id) => 
        sendRequest('/admin_restore_listing.php', 'POST', { id }),
};
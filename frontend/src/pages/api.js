// src/api.js

// 1. FONTOS: Ez a cím mutasson a XAMPP-ban lévő PHP fájljaidra!
// Ha a htdocs/legora/admin mappában vannak a fájlok, akkor ez a helyes:
const API_URL = 'http://localhost/legora/admin'; 

// Segédfüggvény a kérésekhez
async function sendRequest(endpoint, method = 'GET', data = null) {
    const options = { method: method };

    // Ha van adat (pl. login vagy törlés), POST adatként küldjük
    if (data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        options.body = formData;
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, options);
        const text = await response.text(); // Először szövegként olvassuk be a választ

        try {
            // Megpróbáljuk JSON-ként értelmezni
            const json = JSON.parse(text);
            return json;
        } catch (err) {
            // Ha nem sikerül (pl. PHP hibaüzenet jött), kiírjuk a konzolra a "rossz szöveget"
            console.error("Szerver hiba (nem JSON válasz):", text);
            throw new Error("A szerver hibás választ küldött. Részletek a konzolban (F12).");
        }
    } catch (error) {
        console.error("Hálózati hiba:", error);
        throw error;
    }
}

// Itt vannak a konkrét parancsok, amiket a komponensek használnak
export const api = {
    login: (username, password) => 
        sendRequest('/admin_login.php', 'POST', { username, password }),
    
    getUsers: () => 
        sendRequest('/admin_get_user_list.php'),
    
    // Figyelj: admin_delete_user.php-t használunk!
    deleteUser: (id) => 
        sendRequest('/admin_delete_user.php', 'POST', { id }),
    
    restoreUser: (id) => 
        sendRequest('/admin_restore_user.php', 'POST', { id }),

    getListings: () => 
        sendRequest('/admin_get_listings_list.php'),

    // FONTOS: Itt javítottam a fájlnevet a feltöltött fájljaid alapján!
    deleteListing: (id) => 
        sendRequest('/admin_delete_listing.php', 'POST', { id }),

    restoreListing: (id) => 
        sendRequest('/admin_restore_listing.php', 'POST', { id }),
};
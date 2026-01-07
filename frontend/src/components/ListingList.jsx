import { useState, useEffect } from 'react';
import { api } from '../api'; 


const IMAGE_BASE_URL = 'http://localhost/legora/uploads/'; 

const ListingList = () => {
    const [listings, setListings] = useState([]);

    const fetchListings = async () => {
        try {
            const res = await api.getListings();
            if (res.status === 'success') {
                setListings(res.listings);
            }
        } catch (error) {
            console.error("Hiba:", error);
        }
    };

    useEffect(() => {
        fetchListings();
    }, []);

    const toggleStatus = async (listing) => {
        
        const action = listing.deleted_at === null ? api.deleteListing : api.restoreListing;
        
        try {
            const res = await action(listing.id);
            if (res.status === 'success') {
                fetchListings(); 
            } else {
                alert("Szerver üzenet: " + res.message);
            }
        } catch (error) {
            alert("Hiba történt! Nyomj F12-t és nézd meg a Console fület a részletekért.");
        }
    };

    return (
        <div className="card shadow-sm p-3 mt-4">
            <h4>Hirdetések kezelése</h4>
            <div className="table-responsive">
                <table className="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kép</th>
                            <th>ID</th>
                            <th>Cím</th>
                            <th>Ár</th>
                            <th>Státusz</th>
                            <th>Művelet</th>
                        </tr>
                    </thead>
                    <tbody>
                        {listings.map(item => (
                            <tr key={item.id} style={{ opacity: item.deleted_at ? 0.6 : 1 }}>
                                <td>
                                    {item.image_path ? (
                                        <img 
                                            src={`${IMAGE_BASE_URL}${item.image_path}`} 
                                            alt="termék"
                                            style={{ width: '50px', height: '50px', objectFit: 'cover' }}
                                            onError={(e) => { e.target.style.display = 'none'; }} 
                                        />
                                    ) : (
                                        <span className="text-muted small">Nincs kép</span>
                                    )}
                                </td>
                                <td>{item.id}</td>
                                <td>{item.title}</td>
                                <td>{item.price} Ft</td>
                                <td>
                                    {item.deleted_at === null 
                                        ? <span className="badge bg-success">Aktív</span> 
                                        : <span className="badge bg-secondary">Törölve</span>}
                                </td>
                                <td>
                                    <button 
                                        className={`btn btn-sm ${item.deleted_at === null ? 'btn-danger' : 'btn-success'}`}
                                        onClick={() => toggleStatus(item)}
                                    >
                                        {item.deleted_at === null ? 'Törlés' : 'Visszaállítás'}
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default ListingList;
import { useState, useEffect } from 'react';
import { api } from '../api';

export default function ListingList() {
  const [listings, setListings] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  
  useEffect(() => {
    fetchListings();
  }, []);

  const fetchListings = async () => {
    setLoading(true);
    try {
      const res = await api.getListings();
      if (res.status === 'success') {
      
        setListings(res.data?.listings || res.listings || []);
      } else {
        setError("Nem sikerült betölteni a hirdetéseket.");
      }
    } catch (err) {
      console.error("Hiba:", err);
      setError("Hálózati hiba történt.");
    } finally {
      setLoading(false);
    }
  };

 
  const toggleListingStatus = async (id, isDeleted) => {
    const confirmMessage = isDeleted 
        ? "Biztosan visszaállítod ezt a hirdetést?" 
        : "Biztosan törlöd (archiválod) a hirdetést?";
        
    if (!window.confirm(confirmMessage)) return;

    try {
      let res;
      if (isDeleted) {
        
        res = await api.restoreListing(id);
      } else {
       
        res = await api.deleteListing(id);
      }

      if (res.status === 'success') {
       
        setListings(listings.map(item => 
          item.id === id 
            ? { ...item, deleted_at: isDeleted ? null : new Date().toISOString() } 
            : item
        ));
      } else {
        alert("Hiba: " + res.message);
      }
    } catch (err) {
      console.error(err);
      alert("Hálózati hiba a művelet során.");
    }
  };

  if (loading) return (
    <div className="text-center py-5">
      <div className="spinner-border text-primary" role="status"></div>
      <p className="mt-2 text-muted">Hirdetések betöltése...</p>
    </div>
  );

  if (error) return <div className="alert alert-danger shadow-sm">⚠️ {error}</div>;

  return (
    <div className="card border-0 shadow-sm mt-0">
      <div className="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 className="mb-0 text-secondary">📢 Hirdetések Kezelése</h5>
        <span className="badge bg-primary rounded-pill">{listings.length} db</span>
      </div>
      
      <div className="table-responsive">
        <table className="table table-hover align-middle mb-0">
          <thead className="bg-light">
            <tr className="text-uppercase text-secondary small">
              <th className="py-3 ps-4">ID</th>
              <th>Termék</th>
              <th>Ár</th>
              <th>Hirdető</th>
              <th>Dátum</th>
              <th>Státusz</th>
              <th className="text-end pe-4">Művelet</th>
            </tr>
          </thead>
          <tbody>
            {listings.length > 0 ? listings.map(item => {
             
              const isDeleted = item.deleted_at !== null;

              return (
                <tr 
                  key={item.id} 
                  style={{ 
                    
                    opacity: isDeleted ? 0.6 : 1, 
                    backgroundColor: isDeleted ? '#f8f9fa' : 'transparent',
                    transition: 'all 0.3s'
                  }}
                >
                  <td className="ps-4 text-muted fw-bold">#{item.id}</td>
                  <td>
                    <div className="fw-bold text-dark">{item.title || `${item.item_type} ${item.item_id}`}</div>
                    <small className="text-muted d-block text-truncate" style={{maxWidth: '250px'}}>
                      {item.description}
                    </small>
                  </td>
                  <td className="fw-bold text-primary">
                    {parseInt(item.price).toLocaleString()} Ft
                  </td>
                  <td>
                    <span className="badge bg-light text-dark border">
                      User #{item.user_id}
                    </span>
                  </td>
                  <td className="text-muted small">
                     {new Date(item.created_at).toLocaleDateString()}
                  </td>
                  <td>
                    {isDeleted ? 
                      <span className="badge bg-danger">Törölve</span> : 
                      <span className="badge bg-success">Aktív</span>
                    }
                  </td>
                  <td className="text-end pe-4">
                    <button 
                      className={`btn btn-sm ${isDeleted ? 'btn-success' : 'btn-outline-danger'} shadow-sm`}
                      onClick={() => toggleListingStatus(item.id, isDeleted)}
                      title={isDeleted ? "Visszaállítás" : "Törlés"}
                    >
                      {isDeleted ? (
                        <>
                          <i className="bi bi-arrow-counterclockwise me-1"></i> Vissza
                        </>
                      ) : (
                        <>
                          <i className="bi bi-trash me-1"></i> Törlés
                        </>
                      )}
                    </button>
                  </td>
                </tr>
              );
            }) : (
              <tr>
                <td colSpan="7" className="text-center py-5 text-muted">
                  <i className="bi bi-box-seam fs-1 d-block mb-3 opacity-25"></i>
                  Nincs megjeleníthető hirdetés.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
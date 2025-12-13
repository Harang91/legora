import { useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function AddListing() {
  const navigate = useNavigate();
  // Állapotok
  const [data, setData] = useState({
    item_type: 'set', item_id: '', price: '', quantity: 1, item_condition: 'new', description: ''
  });
  const [file, setFile] = useState(null); // Külön state a fájlnak

  const handleSubmit = async (e) => {
    e.preventDefault();

    // FormData készítése (JSON helyett)
    const formData = new FormData();
    formData.append('item_type', data.item_type);
    formData.append('item_id', data.item_id);
    formData.append('price', data.price);
    formData.append('quantity', data.quantity);
    formData.append('item_condition', data.item_condition);
    formData.append('description', data.description);
    
    // Ha van kiválasztva kép, csatoljuk
    if (file) {
        formData.append('image', file);
    }

    try {
        const res = await fetch('/api/listings/create_listing.php', {
            method: 'POST',
            // FIGYELEM: Content-Type fejlécet NE állíts be manuálisan FormData-nál! 
            // A böngésző automatikusan beállítja a multipart/form-data boundary-t.
            body: formData 
        });
        
        const result = await res.json();
        
        if(result.status === 'success') {
            alert("Sikeres feladás!");
            navigate('/');
        } else {
            alert("Hiba: " + result.message);
        }
    } catch (err) {
        alert("Hálózati hiba");
    }
  };

  return (
    <div className="container mt-4" style={{maxWidth: '600px'}}>
      <div className="card p-4 auth-card">
        <h3 className="mb-4">Új hirdetés</h3>
        <form onSubmit={handleSubmit}>
            {/* Típus, ID, Ár, Mennyiség - ezek maradnak ugyanúgy... */}
            <div className="mb-3">
                <label>Típus</label>
                <select className="form-select" value={data.item_type} onChange={e=>setData({...data, item_type:e.target.value})}>
                    <option value="set">Szett</option>
                    <option value="part">Alkatrész</option>
                    <option value="minifig">Minifigura</option>
                </select>
            </div>
            <div className="mb-3">
                <label>Azonosító</label>
                <input type="text" className="form-control" required value={data.item_id} onChange={e=>setData({...data, item_id:e.target.value})} />
            </div>
            
            {/* ÚJ RÉSZ: FÁJL FELTÖLTÉS */}
            <div className="mb-3">
                <label className="form-label">Saját fotó feltöltése (Opcionális)</label>
                <input 
                    type="file" 
                    className="form-control" 
                    accept="image/*"
                    onChange={e => setFile(e.target.files[0])} 
                />
                <div className="form-text">Ha nem töltesz fel képet, a hivatalos LEGO katalóguskép jelenik meg.</div>
            </div>

            <div className="row">
                <div className="col-md-6 mb-3">
                    <label>Ár (Ft)</label>
                    <input type="number" className="form-control" required value={data.price} onChange={e=>setData({...data, price:parseInt(e.target.value)})} />
                </div>
                <div className="col-md-6 mb-3">
                    <label>Mennyiség</label>
                    <input type="number" className="form-control" required value={data.quantity} onChange={e=>setData({...data, quantity:parseInt(e.target.value)})} />
                </div>
            </div>
            
            <div className="mb-3">
                <label>Állapot</label>
                <select className="form-select" value={data.item_condition} onChange={e=>setData({...data, item_condition:e.target.value})}>
                    <option value="new">Új</option>
                    <option value="used">Használt</option>
                </select>
            </div>
            <div className="mb-3">
                <label>Leírás</label>
                <textarea className="form-control" rows="3" value={data.description} onChange={e=>setData({...data, description:e.target.value})}></textarea>
            </div>
            <button type="submit" className="btn btn-primary w-100">Hirdetés feladása</button>
        </form>
      </div>
    </div>
  );
}
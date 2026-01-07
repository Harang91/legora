import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function ListingDetail() {
  const { id } = useParams();
  const { user } = useAuth();
  const [listing, setListing] = useState(null);
  const [qty, setQty] = useState(1);
  const BASE_UPLOAD_URL = "http://localhost/legora/uploads/";

  useEffect(() => {
   
    fetch('/api/listings/get_listings.php?limit=200')
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const found = data.data.listings.find(l => l.id == id);
          setListing(found);
        }
      });
  }, [id]);

  const getImageUrl = (l) => {
   
    if (l.image_url && l.image_url !== "") {
      return `${BASE_UPLOAD_URL}${l.image_url}`;
    }
    
    if (l.lego_data && l.lego_data.img_url) {
      return l.lego_data.img_url;
    }
    if (l.lego_meta && l.lego_meta.img_url) {
      return l.lego_meta.img_url;
    }
   
    return "/no-image.png";
  };

  const addToCart = async () => {
    if (!user) return alert("Jelentkezz be!");
 
    if (listing.quantity === 0) { return alert("Ez a termék jelenleg nem elérhető."); }
    const res = await fetch('/api/cart/add_to_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ listing_id: listing.id, quantity: qty })
    });
    const data = await res.json();
    if (data.status === 'success') alert("Kosárba téve!");
    else alert("Hiba: " + data.message);
  };

  if (!listing) return <div className="text-center mt-5">Betöltés...</div>;

  return (
    <div className="container mt-5">
      <div className="card p-4">
        <div className="row g-5">
          <div className="col-lg-6">
            <img
              src={getImageUrl(listing)}
              className="img-fluid rounded"
              alt="Termék"
              onError={(e) => {
                e.target.onerror = null;
                e.target.src = "/no-image.png";
              }}
            />
          </div>
          <div className="col-lg-6">
            <h1>{listing.lego_meta?.name || listing.item_name}</h1>
            <span className={`badge bg-${listing.item_condition === 'new' ? 'success' : 'warning'} mb-3`}>
              {listing.item_condition === 'new' ? 'Új' : 'Használt'}
            </span>
            <h3 className="text-primary">{Number(listing.price).toLocaleString()} Ft</h3>
            <p className="lead text-muted">{listing.description}</p>
            <p>Eladó: <strong>{listing.seller}</strong></p>
            {listing.quantity > 0 ? (<span className="badge bg-success">{listing.quantity} db elérhető</span>) : (<span className="badge bg-danger">Elfogyott</span>)}

            <div className="row align-items-end mt-4 border-top pt-3">
              <div className="col-md-4">
                <label>Mennyiség</label>
                <input type="number" className="form-control" value={qty} min="1" max={listing.quantity} onChange={e => setQty(parseInt(e.target.value))} />
              </div>
              <div className="col-md-8">
                <button className="btn btn-primary w-100" onClick={addToCart}>Kosárba teszem</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
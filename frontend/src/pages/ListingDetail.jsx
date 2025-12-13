import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function ListingDetail() {
  const { id } = useParams();
  const { user } = useAuth();
  const [listing, setListing] = useState(null);
  const [qty, setQty] = useState(1);

  useEffect(() => {
    // Mivel a backend listát ad vissza, itt kliens oldalon szűrünk (a lego.js mintájára)
    fetch('/api/listings/get_listings.php')
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const found = data.data.listings.find(l => l.id == id);
          setListing(found);
        }
      });
  }, [id]);

  const addToCart = async () => {
    if (!user) return alert("Jelentkezz be!");
    const res = await fetch('/api/cart/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ listing_id: listing.id, quantity: qty })
    });
    const data = await res.json();
    if(data.status === 'success') alert("Kosárba téve!");
    else alert("Hiba: " + data.message);
  };

  if (!listing) return <div className="text-center mt-5">Betöltés...</div>;

  return (
    <div className="container mt-5">
      <div className="card p-4">
        <div className="row g-5">
          <div className="col-lg-6">
            <img src={listing.lego_data?.img_url || 'https://via.placeholder.com/400'} className="img-fluid rounded" alt="Termék" />
          </div>
          <div className="col-lg-6">
            <h1>{listing.lego_data?.name || `Tétel #${listing.id}`}</h1>
            <span className={`badge bg-${listing.item_condition==='new'?'success':'warning'} mb-3`}>
                {listing.item_condition === 'new' ? 'Új' : 'Használt'}
            </span>
            <h3 className="text-primary">{Number(listing.price).toLocaleString()} Ft</h3>
            <p className="lead text-muted">{listing.description}</p>
            <p>Eladó: <strong>{listing.seller}</strong></p>
            
            <div className="row align-items-end mt-4 border-top pt-3">
                <div className="col-md-4">
                    <label>Mennyiség</label>
                    <input type="number" className="form-control" value={qty} min="1" onChange={e => setQty(parseInt(e.target.value))} />
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
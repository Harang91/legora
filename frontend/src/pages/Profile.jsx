import { useEffect, useState } from 'react';

export default function Profile() {
  const [activeTab, setActiveTab] = useState('data');
  const [profile, setProfile] = useState({ username: '', email: '', phone: '', address: '' });
  const [orders, setOrders] = useState([]);

  useEffect(() => {
    fetch('/api/users/get_user.php').then(r=>r.json()).then(d => { if(d.status==='success') setProfile(d.data); });
  }, []);

  const loadOrders = () => {
    setActiveTab('orders');
    fetch('/api/orders/get_orders.php').then(r=>r.json()).then(d => { if(d.status==='success') setOrders(d.data); });
  };

  const handleUpdate = async (e) => {
    e.preventDefault();
    const res = await fetch('/api/users/update_user.php', {
        method: 'PUT',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(profile)
    });
    const d = await res.json();
    alert(d.status === 'success' ? 'Mentve!' : d.message);
  };

  const rateSeller = async (sellerId) => {
    const rating = prompt("Értékelés (1-5):");
    if(!rating) return;
    await fetch('/api/ratings/add_rating.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ rated_user_id: sellerId, rating: parseInt(rating) })
    });
    alert("Köszönjük!");
  };

  return (
    <div className="container mt-4">
      <div className="card shadow-sm">
        <div className="card-header">
            <ul className="nav nav-tabs card-header-tabs">
                <li className="nav-item"><button className={`nav-link ${activeTab==='data'?'active':''}`} onClick={()=>setActiveTab('data')}>Adataim</button></li>
                <li className="nav-item"><button className={`nav-link ${activeTab==='orders'?'active':''}`} onClick={loadOrders}>Rendeléseim</button></li>
            </ul>
        </div>
        <div className="card-body">
            {activeTab === 'data' ? (
                <form onSubmit={handleUpdate}>
                    <div className="mb-3"><label>Felhasználónév</label><input className="form-control" value={profile.username} disabled /></div>
                    <div className="mb-3"><label>Email</label><input className="form-control" value={profile.email} onChange={e=>setProfile({...profile, email:e.target.value})} /></div>
                    <div className="mb-3"><label>Telefon</label><input className="form-control" value={profile.phone||''} onChange={e=>setProfile({...profile, phone:e.target.value})} /></div>
                    <div className="mb-3"><label>Cím</label><input className="form-control" value={profile.address||''} onChange={e=>setProfile({...profile, address:e.target.value})} /></div>
                    <button className="btn btn-primary">Mentés</button>
                </form>
            ) : (
                <ul className="list-group list-group-flush">
                    {orders.length === 0 ? <p>Nincs rendelés.</p> : orders.map(o => (
                        <li key={o.order_id} className="list-group-item d-flex justify-content-between align-items-center">
                            <div><strong>Rendelés #{o.order_id}</strong><br/><small>{o.seller_name} | {o.ordered_at}</small></div>
                            <div className="text-end">
                                <div className="fw-bold">{Number(o.total_price).toLocaleString()} Ft</div>
                                <span className="badge bg-secondary">{o.status}</span>
                                {o.status === 'completed' && <button className="btn btn-sm btn-outline-warning ms-2" onClick={()=>rateSeller(o.seller_id)}>Értékelés</button>}
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </div>
      </div>
    </div>
  );
}
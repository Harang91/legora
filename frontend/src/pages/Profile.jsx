import { useEffect, useState } from 'react';
import { useAuth } from '../AuthContext'; 

export default function Profile() {
  const { user, loginUser } = useAuth(); 

  
  const [activeTab, setActiveTab] = useState(() => {
    return localStorage.getItem('profile_active_tab') || 'data';
  });

  
  const [profile, setProfile] = useState(() => {
    
     const savedUser = JSON.parse(localStorage.getItem('user') || '{}');
     
     return { 
        username: '', email: '', phone: '', address: '', 
        ...savedUser, 
        ...(user || {}) 
     };
  });

  const [orders, setOrders] = useState([]);
  const [isEditing, setIsEditing] = useState(false);

 
  useEffect(() => {
    localStorage.setItem('profile_active_tab', activeTab);
  }, [activeTab]);

 
  useEffect(() => {
    if (activeTab === 'orders') {
        loadOrders();
    }
    
    fetch('/api/users/get_user.php')
      .then(r => r.json())
      .then(d => { 
        if(d.status === 'success') {
            const serverData = d.data;

            setProfile(prev => {
                
                const mergedData = {
                    ...prev, 
                    ...serverData,                    
                    
                    phone: serverData.phone ? serverData.phone : prev.phone,
                    address: serverData.address ? serverData.address : prev.address
                };

               
                if (JSON.stringify(mergedData) !== JSON.stringify(prev)) {
                    loginUser(mergedData);
                    localStorage.setItem('user', JSON.stringify(mergedData));
                }

                return mergedData;
            });
        }
      })
      .catch(err => console.error("Hiba az adatok frissítésekor:", err));
  }, []);

  const loadOrders = () => {
    if (activeTab !== 'orders') setActiveTab('orders');
    fetch('/api/orders/get_orders.php')
      .then(r => r.json())
      .then(d => { if(d.status === 'success') setOrders(d.data); });
  };

  const handleUpdate = async (e) => {
    e.preventDefault();
    try {
        const res = await fetch('/api/users/update_user.php', {
            method: 'PUT',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(profile)
        });
        const d = await res.json();
        
        if (d.status === 'success') {
            alert('Mentve!');
            setIsEditing(false);
            
          
            const finalData = { ...profile, ...(d.data || {}) };
            
            loginUser(finalData);
            localStorage.setItem('user', JSON.stringify(finalData));
        } else {
            alert(d.message);
        }
    } catch (err) {
        console.error(err);
        alert("Hiba történt a mentéskor.");
    }
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
                <li className="nav-item">
                    <button className={`nav-link ${activeTab==='data'?'active':''}`} onClick={()=>setActiveTab('data')}>Adataim</button>
                </li>
                <li className="nav-item">
                    <button className={`nav-link ${activeTab==='orders'?'active':''}`} onClick={()=>{setActiveTab('orders'); loadOrders();}}>Rendeléseim</button>
                </li>
            </ul>
        </div>
        <div className="card-body">
            {activeTab === 'data' ? (
                isEditing ? (
                    <form onSubmit={handleUpdate}>
                        <div className="mb-3">
                            <label className="form-label text-muted">Felhasználónév (nem módosítható)</label>
                            <input className="form-control" value={profile.username || ''} disabled />
                        </div>
                        <div className="mb-3">
                            <label className="form-label">Email</label>
                            <input className="form-control" value={profile.email || ''} onChange={e=>setProfile({...profile, email:e.target.value})} required />
                        </div>
                        <div className="mb-3">
                            <label className="form-label">Telefon</label>
                            <input className="form-control" value={profile.phone||''} onChange={e=>setProfile({...profile, phone:e.target.value})} />
                        </div>
                        <div className="mb-3">
                            <label className="form-label">Cím</label>
                            <input className="form-control" value={profile.address||''} onChange={e=>setProfile({...profile, address:e.target.value})} />
                        </div>
                        <div className="d-flex gap-2">
                            <button type="submit" className="btn btn-success">Mentés</button>
                            <button type="button" className="btn btn-secondary" onClick={() => setIsEditing(false)}>Mégse</button>
                        </div>
                    </form>
                ) : (
                    <div className="profile-view">
                        <div className="row mb-3 border-bottom pb-2">
                            <div className="col-sm-4 fw-bold">Felhasználónév:</div>
                            <div className="col-sm-8">{profile.username}</div>
                        </div>
                        <div className="row mb-3 border-bottom pb-2">
                            <div className="col-sm-4 fw-bold">Email:</div>
                            <div className="col-sm-8">{profile.email}</div>
                        </div>
                        <div className="row mb-3 border-bottom pb-2">
                            <div className="col-sm-4 fw-bold">Telefon:</div>
                            <div className="col-sm-8">{profile.phone || <span className="text-muted fst-italic">Nincs megadva</span>}</div>
                        </div>
                        <div className="row mb-3 border-bottom pb-2">
                            <div className="col-sm-4 fw-bold">Cím:</div>
                            <div className="col-sm-8">{profile.address || <span className="text-muted fst-italic">Nincs megadva</span>}</div>
                        </div>
                        <button className="btn btn-primary" onClick={() => setIsEditing(true)}>Adatok módosítása</button>
                    </div>
                )
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
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import { api } from '../api';

import ListingList from '../components/ListingList'; 

export default function AdminDashboard() {
  const { admin, logout } = useAuth();
  const navigate = useNavigate();
  
  const [stats, setStats] = useState(null);
  const [users, setUsers] = useState([]);
  
  const [activeTab, setActiveTab] = useState('stats'); 
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

 
  useEffect(() => {
    if (!admin) {
      navigate('/admin-login');
    }
  }, [admin, navigate]);

  
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError('');
      try {
        const statsRes = await api.getStats();
        if (statsRes.status === 'success') {
            setStats(statsRes.data || statsRes); 
        }

        const usersRes = await api.getUsers();
        if (usersRes.status === 'success') {
             const userList = usersRes.data?.users || usersRes.users || [];
             setUsers(userList);
        }
      } catch (err) {
        console.error("Hiba:", err);
        setError("Nem sikerült betölteni az adatokat.");
      } finally {
        setLoading(false);
      }
    };

    if (admin) fetchData();
  }, [admin]);

  const handleToggleUser = async (id) => {
    try {
        const res = await api.toggleUser(id);
        if (res.status === 'success') {
            setUsers(users.map(user => 
                user.id === id ? { ...user, is_active: user.is_active == 1 ? 0 : 1 } : user
            ));
        } else {
            alert("Hiba: " + res.message);
        }
    } catch (err) {
        alert("Hálózati hiba történt.");
    }
  };

  if (loading) return (
    <div className="d-flex justify-content-center align-items-center vh-100 text-white">
        <div className="spinner-border" role="status"><span className="visually-hidden">Betöltés...</span></div>
    </div>
  );

  return (
    <div className="container py-5">
      <div className="card border-0 shadow-lg overflow-hidden" 
           style={{ 
             borderRadius: '1rem', 
             backgroundColor: 'rgba(255, 255, 255, 0.92)', 
             backdropFilter: 'blur(10px)' 
           }}>
        
        <div className="card-header border-0 p-4 text-white d-flex justify-content-between align-items-center"
             style={{ background: 'linear-gradient(90deg, #1e3c72 0%, #2a5298 100%)' }}>
            <div>
                <h2 className="mb-0 fw-bold">🛠️ Admin Vezérlőpult</h2>
                <small className="text-white-50">Rendszer karbantartás és felügyelet</small>
            </div>
            <div className="d-flex align-items-center gap-3">
                <div className="text-end d-none d-sm-block">
                    <div className="fw-bold">{admin?.username}</div>
                    <div className="badge bg-warning text-dark">Adminisztrátor</div>
                </div>
                <button className="btn btn-danger shadow-sm" onClick={logout}>
                    Kilépés 🚪
                </button>
            </div>
        </div>

        <div className="card-body p-4">
            {error && <div className="alert alert-danger shadow-sm rounded-3">⚠️ {error}</div>}

           
            <ul className="nav nav-pills nav-fill mb-5 gap-3">
                <li className="nav-item">
                    <button 
                        className={`nav-link py-3 shadow-sm fw-bold border ${activeTab === 'stats' ? 'active bg-primary' : 'bg-white text-secondary'}`}
                        onClick={() => setActiveTab('stats')}
                        style={{transition: 'all 0.3s'}}
                    >
                        📊 Statisztika
                    </button>
                </li>
                <li className="nav-item">
                    <button 
                        className={`nav-link py-3 shadow-sm fw-bold border ${activeTab === 'users' ? 'active bg-primary' : 'bg-white text-secondary'}`}
                        onClick={() => setActiveTab('users')}
                        style={{transition: 'all 0.3s'}}
                    >
                        👥 Felhasználók
                    </button>
                </li>
               
                <li className="nav-item">
                    <button 
                        className={`nav-link py-3 shadow-sm fw-bold border ${activeTab === 'listings' ? 'active bg-primary' : 'bg-white text-secondary'}`}
                        onClick={() => setActiveTab('listings')}
                        style={{transition: 'all 0.3s'}}
                    >
                        📦 Hirdetések
                    </button>
                </li>
            </ul>

   
            {activeTab === 'stats' && stats && (
                <div className="row g-4">
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm text-white overflow-hidden position-relative"
                             style={{ background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }}>
                            <div className="card-body p-4 text-center position-relative z-1">
                                <div className="display-4 fw-bold mb-2">{stats.active_listings ?? 0}</div>
                                <h5 className="opacity-75 text-uppercase ls-1">Aktív Hirdetés</h5>
                            </div>
                            <i className="bi bi-box-seam position-absolute" style={{fontSize:'8rem', opacity:0.1, right:'-20px', bottom:'-20px'}}></i>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm text-white overflow-hidden position-relative"
                             style={{ background: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)' }}>
                            <div className="card-body p-4 text-center position-relative z-1">
                                <div className="display-4 fw-bold mb-2">{stats.active_users ?? 0}</div>
                                <h5 className="opacity-75 text-uppercase ls-1">Aktív Felhasználó</h5>
                            </div>
                            <i className="bi bi-people position-absolute" style={{fontSize:'8rem', opacity:0.1, right:'-20px', bottom:'-20px'}}></i>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm text-white overflow-hidden position-relative"
                             style={{ background: 'linear-gradient(135deg, #ff9966 0%, #ff5e62 100%)' }}>
                            <div className="card-body p-4 text-center position-relative z-1">
                                <div className="display-4 fw-bold mb-2">{stats.total_users ?? 0}</div>
                                <h5 className="opacity-75 text-uppercase ls-1">Összes Regisztrált</h5>
                            </div>
                            <i className="bi bi-bar-chart position-absolute" style={{fontSize:'8rem', opacity:0.1, right:'-20px', bottom:'-20px'}}></i>
                        </div>
                    </div>
                </div>
            )}

           
            {activeTab === 'users' && (
                <div className="card border-0 shadow-sm">
                    <div className="card-header bg-white py-3">
                        <h5 className="mb-0 text-secondary">Regisztrált tagok kezelése</h5>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="bg-light">
                                <tr className="text-uppercase text-secondary small">
                                    <th className="py-3 ps-4">ID</th>
                                    <th>Felhasználó</th>
                                    <th>Email</th>
                                    <th>Szerepkör</th>
                                    <th>Státusz</th>
                                    <th className="text-end pe-4">Művelet</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.length > 0 ? users.map(user => (
                                    <tr key={user.id} className={user.is_active == 0 ? 'bg-light text-muted' : ''}>
                                        <td className="ps-4 text-muted">#{user.id}</td>
                                        <td><div className="fw-bold">{user.username}</div></td>
                                        <td className="text-muted">{user.email}</td>
                                        <td>
                                            {user.role === 'admin' 
                                                ? <span className="badge bg-warning text-dark">Admin</span>
                                                : <span className="badge bg-primary bg-opacity-75">Felhasználó</span>
                                            }
                                        </td>
                                        <td>
                                            {user.is_active == 1 ? 
                                                <span className="text-success fw-bold"><i className="bi bi-check-circle-fill me-1"></i>Aktív</span> : 
                                                <span className="text-danger fw-bold"><i className="bi bi-slash-circle me-1"></i>Tiltott</span>
                                            }
                                        </td>
                                        <td className="text-end pe-4">
                                            {user.role !== 'admin' && (
                                                <button 
                                                    className={`btn btn-sm ${user.is_active == 1 ? 'btn-outline-danger' : 'btn-outline-success'} rounded-pill px-3 shadow-sm`}
                                                    onClick={() => handleToggleUser(user.id)}
                                                >
                                                    {user.is_active == 1 ? 'Tiltás' : 'Aktiválás'}
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-5 text-muted">
                                            Nincs megjeleníthető felhasználó.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
            
            {activeTab === 'listings' && (
                <ListingList />
            )}

        </div>
      </div>
    </div>
  );
}
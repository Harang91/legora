import { useEffect, useState } from 'react';
import { useAuth } from '../AuthContext';
import { useNavigate } from 'react-router-dom';

export default function AdminDashboard() {
  const { admin, logout } = useAuth();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState('stats');
  const [stats, setStats] = useState(null);
  const [users, setUsers] = useState([]);

  useEffect(() => {
    if (!admin) navigate('/admin-login');
    else {
      loadStats();
      loadUsers();
    }
  }, [admin]);

  const loadStats = async () => {
    const res = await fetch('/api/admin/get_all_stats.php');
    const data = await res.json();
    if (data.status === 'success') setStats(data);
  };

  const loadUsers = async () => {
    const res = await fetch('/api/admin/get_users.php');
    const data = await res.json();
    if (data.status === 'success') setUsers(data.users || []); // Javított array kezelés
  };

  const toggleUser = async (id) => {
    if(!confirm("Biztos?")) return;
    await fetch('/api/admin/toggle_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // A javított JSON header
        body: JSON.stringify({ id })
    });
    loadUsers();
  };

  return (
    <div className="container mt-4">
      <div className="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded">
        <h2 className="text-danger m-0">Admin Pult</h2>
        <button onClick={() => { logout(); navigate('/'); }} className="btn btn-outline-danger">Kilépés</button>
      </div>

      <ul className="nav nav-tabs mb-4">
        <li className="nav-item"><button className={`nav-link ${activeTab==='stats'?'active':''}`} onClick={()=>setActiveTab('stats')}>Statisztika</button></li>
        <li className="nav-item"><button className={`nav-link ${activeTab==='users'?'active':''}`} onClick={()=>setActiveTab('users')}>Felhasználók</button></li>
      </ul>

      {activeTab === 'stats' && stats && (
        <div className="row g-4">
            <div className="col-md-3"><div className="card bg-primary text-white p-3">User: {stats.global_stats.total_users}</div></div>
            <div className="col-md-3"><div className="card bg-success text-white p-3">Aktív Hirdetés: {stats.global_stats.active_listings}</div></div>
        </div>
      )}

      {activeTab === 'users' && (
        <table className="table table-striped">
            <thead><tr><th>ID</th><th>Név</th><th>Email</th><th>Művelet</th></tr></thead>
            <tbody>
                {users.map(u => (
                    <tr key={u.id} className={u.is_active == 0 ? "table-danger" : ""}>
                        <td>{u.id}</td>
                        <td>{u.username}</td>
                        <td>{u.email}</td>
                        <td>
                            <button className="btn btn-sm btn-warning" onClick={() => toggleUser(u.id)}>
                                {u.is_active == 1 ? "Tiltás" : "Aktiválás"}
                            </button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
      )}
    </div>
  );
}
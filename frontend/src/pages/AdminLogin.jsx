import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function AdminLogin() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const { loginAdmin } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    const res = await fetch('/api/admin/admin_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
    });
    const data = await res.json();
    if (data.status === 'success') {
        loginAdmin({ id: data.admin_id, username: data.username });
        navigate('/admin-dashboard');
    } else {
        setError(data.message);
    }
  };

  return (
    <div className="container mt-5" style={{maxWidth:'400px'}}>
      <div className="card p-4 border-danger border-top-5" style={{borderTopWidth: '5px'}}>
        <h3 className="text-center text-danger mb-4">Admin Belépés</h3>
        {error && <div className="alert alert-danger">{error}</div>}
        <form onSubmit={handleSubmit}>
            <div className="mb-3"><label>Admin User</label><input className="form-control" value={username} onChange={e=>setUsername(e.target.value)} required /></div>
            <div className="mb-3"><label>Jelszó</label><input type="password" className="form-control" value={password} onChange={e=>setPassword(e.target.value)} required /></div>
            <button className="btn btn-danger w-100">Belépés</button>
        </form>
        <div className="text-center mt-3"><Link to="/login" className="text-muted small">Vissza a felhasználói belépéshez</Link></div>
      </div>
    </div>
  );
}
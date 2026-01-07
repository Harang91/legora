import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  
  const { loginUser, user } = useAuth();
  const navigate = useNavigate();

  
  useEffect(() => {
    if (user) {
      navigate('/'); 
    }
  }, [user, navigate]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    
    try {
      const res = await fetch('/api/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email_or_username: email, password })
      });
      const data = await res.json();
      
      if (data.status === 'success') {
        
        localStorage.setItem('user', JSON.stringify(data.data));
        

        loginUser(data.data);
        
        
        navigate('/');
      } else {
        setError(data.message);
      }
    } catch (err) {
      console.error(err);
      setError('Hálózati hiba');
    }
  };

  return (
    <div className="container mt-5" style={{maxWidth: '400px'}}>
      <div className="card p-4 auth-card">
        <h3 className="text-center mb-4">Bejelentkezés</h3>
        {error && <div className="alert alert-danger">{error}</div>}
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label className="form-label">Email / User</label>
            <input type="text" className="form-control" value={email} onChange={e => setEmail(e.target.value)} required />
          </div>
          <div className="mb-3">
            <label className="form-label">Jelszó</label>
            <input type="password" className="form-control" value={password} onChange={e => setPassword(e.target.value)} required />
          </div>
          <button type="submit" className="btn btn-primary w-100">Belépés</button>
        </form>
        <div className="text-center mt-3 border-top pt-3">
            <Link to="/admin-login" className="text-secondary small">Adminisztrátor belépés</Link>
        </div>
      </div>
    </div>
  );
}
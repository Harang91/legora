import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

export default function Register() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({ username: '', email: '', password: '' });
  const [msg, setMsg] = useState({ text: '', type: '' });
  const [validity, setValidity] = useState({
    hossz: false, kisbetu: false, nagybetu: false, szam: false, specKar: false
  });

  // Jelszó ellenőrzés (lego.js alapján)
  useEffect(() => {
    const p = formData.password;
    setValidity({
      hossz: p.length >= 8 && p.length <= 30,
      kisbetu: /[a-z]/.test(p),
      nagybetu: /[A-Z]/.test(p),
      szam: /[0-9]/.test(p),
      specKar: /[?!+@#$]/.test(p)
    });
  }, [formData.password]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!Object.values(validity).every(Boolean)) {
      setMsg({ text: 'Gyenge jelszó!', type: 'danger' });
      return;
    }

    setMsg({ text: 'Regisztráció...', type: 'info' });
    try {
      const res = await fetch('/api/auth/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...formData, captcha: '1234' }) // Captcha a JS-ből
      });
      const result = await res.json();
      
      if (res.ok || res.status === 201) {
        setMsg({ text: result.message, type: 'success' });
        setTimeout(() => navigate('/login'), 2000);
      } else {
        setMsg({ text: result.message, type: 'danger' });
      }
    } catch (error) {
      setMsg({ text: 'Hálózati hiba.', type: 'danger' });
    }
  };

  return (
    <div className="container mt-5" style={{maxWidth: '500px'}}>
      <div className="card p-4">
        <h3 className="text-center mb-4">Regisztráció</h3>
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label className="form-label">Felhasználónév</label>
            <input type="text" className="form-control" required
              value={formData.username} onChange={e => setFormData({...formData, username: e.target.value})} />
          </div>
          <div className="mb-3">
            <label className="form-label">Email</label>
            <input type="email" className="form-control" required
              value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})} />
          </div>
          <div className="mb-3">
            <label className="form-label">Jelszó</label>
            <input type="password" className="form-control" required
              value={formData.password} onChange={e => setFormData({...formData, password: e.target.value})} />
          </div>
          
          <ul className="small text-muted mb-3 list-unstyled">
            <li className={validity.hossz ? 'text-success' : 'text-danger'}>Legalább 8, max 30 karakter</li>
            <li className={validity.kisbetu ? 'text-success' : 'text-danger'}>Kisbetű</li>
            <li className={validity.nagybetu ? 'text-success' : 'text-danger'}>Nagybetű</li>
            <li className={validity.szam ? 'text-success' : 'text-danger'}>Szám</li>
            <li className={validity.specKar ? 'text-success' : 'text-danger'}>Spec. karakter [?!+@#$]</li>
          </ul>

          <button type="submit" className="btn btn-primary w-100">Regisztráció</button>
        </form>
        {msg.text && <div className={`alert alert-${msg.type} mt-3 text-center`}>{msg.text}</div>}
      </div>
    </div>
  );
}
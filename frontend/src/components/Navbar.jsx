import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';

export default function Navbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  return (
    // ITT A VÁLTOZÁS: custom-navbar osztály hozzáadása
    <nav className="navbar navbar-expand-lg navbar-light custom-navbar mb-4 sticky-top">
      <div className="container">
        <Link className="navbar-brand d-flex align-items-center" to="/">
          {/* ITT A VÁLTOZÁS: Logó kép beszúrása a public mappából */}
          <img src="/creator-brick-logo.png" alt="Logo" className="me-2" />
          <span className="fw-bold fs-4">LegoBolt Concept</span>
        </Link>
        
        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span className="navbar-toggler-icon"></span>
        </button>

        <div className="collapse navbar-collapse" id="navbarContent">
          <ul className="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
            <li className="nav-item"><Link className="nav-link" to="/">Főoldal</Link></li>
            {user && (
              <>
                <li className="nav-item"><Link className="nav-link" to="/add-listing">Hirdetés feladása</Link></li>
                <li className="nav-item"><Link className="nav-link" to="/cart">Kosár</Link></li>
                <li className="nav-item"><Link className="nav-link" to="/profile">Profilom</Link></li>
              </>
            )}
          </ul>
          <div className="d-flex">
            {user ? (
              <div className="d-flex align-items-center bg-white bg-opacity-75 px-3 py-1 rounded-pill shadow-sm">
                <span className="me-3 text-dark">Üdv, <strong>{user.username}</strong>!</span>
                <button className="btn btn-sm btn-outline-danger rounded-pill" onClick={() => {logout(); navigate('/');}}>Kijelentkezés</button>
              </div>
            ) : (
              <div>
                <Link to="/login" className="btn btn-light me-2 shadow-sm fw-bold">Bejelentkezés</Link>
                <Link to="/register" className="btn btn-warning shadow-sm fw-bold text-dark">Regisztráció</Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
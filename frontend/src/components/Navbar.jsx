import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../AuthContext';
import { useState } from 'react'; // 1. useState importálása

export default function Navbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  // 2. Állapot a menü lenyitásához (alapból igaz, azaz "összecsukott")
  const [isNavCollapsed, setIsNavCollapsed] = useState(true);

  // 3. Függvény, ami váltogatja az állapotot
  const handleNavCollapse = () => setIsNavCollapsed(!isNavCollapsed);

  return (
    <nav className="navbar navbar-expand-lg navbar-light custom-navbar mb-4 sticky-top">
      <div className="container">
        <Link className="navbar-brand d-flex align-items-center" to="/">
          <img src="/creator-brick-logo.png" alt="Logo" className="me-2" />
          <span className="fw-bold fs-4">LegoBolt Concept</span>
        </Link>
        
        {/* 4. A gomb mostantól a React függvényt hívja meg, kivettük a data-bs-toggle részeket */}
        <button 
            className="navbar-toggler" 
            type="button" 
            onClick={handleNavCollapse} // Itt hívjuk meg a váltót
            aria-controls="navbarContent" 
            aria-expanded={!isNavCollapsed} 
            aria-label="Toggle navigation"
        >
            <span className="navbar-toggler-icon"></span>
        </button>

        {/* 5. Itt döntjük el, hogy rajta legyen-e a 'show' osztály */}
        <div className={`${isNavCollapsed ? 'collapse' : ''} navbar-collapse`} id="navbarContent">
          <ul className="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
            {/* Tipp: Kattintáskor be is csukhatod a menüt, ha hozzáadod az onClick-et a linkekhez is */}
            <li className="nav-item">
                <Link className="nav-link" to="/" onClick={() => setIsNavCollapsed(true)}>Főoldal</Link>
            </li>
            {user && (
              <>
                <li className="nav-item">
                    <Link className="nav-link" to="/add-listing" onClick={() => setIsNavCollapsed(true)}>Hirdetés feladása</Link>
                </li>
                <li className="nav-item">
                    <Link className="nav-link" to="/cart" onClick={() => setIsNavCollapsed(true)}>Kosár</Link>
                </li>
                <li className="nav-item">
                    <Link className="nav-link" to="/profile" onClick={() => setIsNavCollapsed(true)}>Profilom</Link>
                </li>
              </>
            )}
          </ul>
          <div className="d-flex">
            {user ? (
              <div className="d-flex align-items-center bg-white bg-opacity-75 px-3 py-1 rounded-pill shadow-sm">
                <span className="me-3 text-dark">Üdv, <strong>{user.username}</strong>!</span>
                <button className="btn btn-sm btn-outline-danger rounded-pill" onClick={() => {logout(); navigate('/'); setIsNavCollapsed(true);}}>Kijelentkezés</button>
              </div>
            ) : (
              <div>
                <Link to="/login" className="btn btn-light me-2 shadow-sm fw-bold" onClick={() => setIsNavCollapsed(true)}>Bejelentkezés</Link>
                <Link to="/register" className="btn btn-warning shadow-sm fw-bold text-dark" onClick={() => setIsNavCollapsed(true)}>Regisztráció</Link>
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
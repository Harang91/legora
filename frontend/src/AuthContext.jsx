import { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null); // currentUser
  const [admin, setAdmin] = useState(null); // currentAdmin

  // --- EZ A RÉSZ HIÁNYZOTT: Visszatöltés frissítéskor ---
  useEffect(() => {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      try {
        setUser(JSON.parse(storedUser));
      } catch (e) {
        console.error("Hiba a mentett user betöltésekor", e);
        localStorage.removeItem('user');
      }
    }
  }, []);
  // -----------------------------------------------------

  // Login függvény
  const loginUser = (userData) => {
    setUser(userData);
    // Biztonsági mentés, ha a login oldalon nem futna le
    localStorage.setItem('user', JSON.stringify(userData));
  };

  const loginAdmin = (adminData) => setAdmin(adminData);

  // Logout (API hívás + state törlés + localStorage törlés)
  const logout = async () => {
    try {
      await fetch('/api/auth/logout.php', { method: 'POST' });
    } catch (error) {
      console.error("Logout error", error);
    }
    
    localStorage.removeItem('user'); // Töröljük a mentést
    setUser(null);
    setAdmin(null);
  };

  return (
    <AuthContext.Provider value={{ user, admin, loginUser, loginAdmin, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
import { createContext, useState, useContext, useEffect } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null); 
  const [admin, setAdmin] = useState(null); 

  
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
  
  const loginUser = (userData) => {
    setUser(userData);
    
    localStorage.setItem('user', JSON.stringify(userData));
  };

  const loginAdmin = (adminData) => setAdmin(adminData);

 
  const logout = async () => {
    try {
      await fetch('/api/auth/logout.php', { method: 'POST' });
    } catch (error) {
      console.error("Logout error", error);
    }
    
    localStorage.removeItem('user'); 
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
import { createContext, useState, useContext } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null); // currentUser
  const [admin, setAdmin] = useState(null); // currentAdmin

  // Login függvény
  const loginUser = (userData) => setUser(userData);
  const loginAdmin = (adminData) => setAdmin(adminData);

  // Logout (API hívás + state törlés)
  const logout = async () => {
    try {
      await fetch('/api/auth/logout.php', { method: 'POST' });
    } catch (error) {
      console.error("Logout error", error);
    }
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
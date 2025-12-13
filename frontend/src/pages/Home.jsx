import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

export default function Home() {
  // Állapotok (State)
  const [listings, setListings] = useState([]);
  const [loading, setLoading] = useState(true);
  
  // Keresési állapotok
  const [searchTerm, setSearchTerm] = useState('');
  const [filterType, setFilterType] = useState(''); // Üres = Minden típus

  // Adatok betöltése
  const fetchListings = () => {
    setLoading(true);
    
    let url = '';
    
    // DÖNTÉSI LOGIKA: Melyik PHP fájlt hívjuk?
    if (searchTerm) {
        // Ha van keresőszó -> search.php
        const params = new URLSearchParams();
        params.append('q', searchTerm); // A search.php 'q' paramétert vár!
        if (filterType) params.append('item_type', filterType);
        
        // search.php hívása
        url = `/api/listings/search.php?${params.toString()}`;
    } else {
        // Ha nincs keresés -> get_listings.php
        const params = new URLSearchParams();
        if (filterType) params.append('item_type', filterType);
        
        // get_listings.php hívása
        url = `/api/listings/get_listings.php?${params.toString()}`;
    }

    console.log("Lekérdezés:", url);

    fetch(url)
      .then(res => res.json())
      .then(resp => {
        if (resp.status === 'success') {
          // NORMALIZÁLÁS: A két fájl más néven adja vissza a listát!
          // get_listings.php -> resp.data.listings
          // search.php       -> resp.data.results
          const items = resp.data.listings || resp.data.results || [];
          setListings(items);
        } else {
            setListings([]);
        }
        setLoading(false);
      })
      .catch(err => {
        console.error("Hiba:", err);
        setListings([]);
        setLoading(false);
      });
  };

  // Frissítés, ha változik a szűrő típus vagy betölt az oldal
  useEffect(() => {
    fetchListings();
  }, [filterType]);

  // Keresés indítása (Enter vagy Gombnyomás)
  const handleSearch = (e) => {
    e.preventDefault();
    fetchListings();
  };

  // Reset gomb
  const handleReset = () => {
      setSearchTerm('');
      setFilterType('');
      // Mivel a searchTerm állapota aszinkron, itt kényszerítjük az újratöltést
      // De a következő renderelésnél a useEffect úgyis megoldja, ha a filterType is változik
      // Ha csak a szöveg változik, akkor a fetchListings-t a következő körben hívjuk meg
      // Vagy egyszerűen reload:
      window.location.reload(); 
  };

  return (
    <div className="container">
      {/* Hero Kártya */}
      <div className="custom-hero-card mb-5 text-center">
        <h2 className="display-4 mb-3">Építs. Ossz meg. Cserélj.</h2>
        <p className="lead fs-3">Fedezz fel új kincseket és váltsd készpénzre a kockáidat!</p>
      </div>
      
      {/* --- KERESŐ SÁV --- */}
      <div className="card p-4 mb-5 shadow-sm bg-light border-0">
        <form onSubmit={handleSearch} className="row g-3 align-items-center">
            {/* Típus szűrő */}
            <div className="col-md-3">
                <select 
                    className="form-select border-primary" 
                    value={filterType} 
                    onChange={(e) => setFilterType(e.target.value)}
                >
                    <option value="">Összes kategória</option>
                    <option value="set">Szettek (Set)</option>
                    <option value="part">Alkatrészek (Part)</option>
                    <option value="minifig">Minifigurák</option>
                </select>
            </div>
            
            {/* Keresőmező */}
            <div className="col-md-7">
                <div className="input-group">
                    <input 
                        type="text" 
                        className="form-control border-primary" 
                        placeholder="Pl. Star Wars, 75301, piros elem..." 
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                    <button className="btn btn-primary" type="submit">
                        <i className="fas fa-search me-2"></i>Keresés
                    </button>
                </div>
            </div>

            {/* Reset gomb */}
            <div className="col-md-2">
                 <button type="button" className="btn btn-outline-secondary w-100" onClick={handleReset}>
                    Mégse
                 </button>
            </div>
        </form>
      </div>

      <h3 className="mb-4 text-white" style={{textShadow: '1px 1px 4px rgba(0,0,0,0.5)'}}>
          {searchTerm ? `Találatok erre: "${searchTerm}"` : "Aktuális Hirdetések"}
      </h3>
      
      {loading ? (
          <div className="text-center text-white fs-4">Keresés...</div>
      ) : (
        <div className="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            {listings.length === 0 ? (
                <div className="col-12">
                    <div className="alert alert-warning text-center">
                        Nincs a keresésnek megfelelő találat. Próbálj másik kulcsszót!
                    </div>
                </div>
            ) : (
                listings.map(l => (
                <div className="col" key={l.id || l.listing_id}> {/* search.php listing_id-t adhat vissza */}
                    <div className="card h-100 listing-card border-0">
                    <div className="position-relative bg-white rounded-top p-3 text-center">
                        <img 
                            src={l.lego_data?.img_url || l.lego_meta?.img_url || 'https://via.placeholder.com/300'} 
                            className="img-fluid" 
                            style={{height: '180px', objectFit: 'contain'}} 
                            alt="lego" 
                        />
                        <span className={`position-absolute top-0 start-0 m-2 badge rounded-pill ${l.item_condition==='new'?'bg-success':'bg-warning text-dark'}`}>
                            {l.item_condition === 'new' ? 'ÚJ' : 'HASZNÁLT'}
                        </span>
                    </div>
                    <div className="card-body d-flex flex-column bg-light">
                        <h5 className="card-title text-truncate" title={l.lego_data?.name || l.lego_meta?.name}>
                            {l.lego_data?.name || l.lego_meta?.name || `Tétel #${l.item_id}`}
                        </h5>
                        <p className="card-text text-muted small mb-1">
                            Azonosító: <strong>{l.item_id}</strong>
                        </p>
                        <p className="card-text text-muted small mb-3">
                            Eladó: {l.seller || l.seller_name}
                        </p>
                        <div className="mt-auto d-flex justify-content-between align-items-center">
                            <span className="fw-bold fs-5 text-primary">{Number(l.price).toLocaleString()} Ft</span>
                            <Link to={`/listing/${l.id || l.listing_id}`} className="btn btn-sm btn-outline-dark rounded-pill">
                                Megnézem
                            </Link>
                        </div>
                    </div>
                    </div>
                </div>
                ))
            )}
        </div>
      )}
    </div>
  );
}
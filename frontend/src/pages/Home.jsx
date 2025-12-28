import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

export default function Home() {
    const [listings, setListings] = useState([]);
    const [loading, setLoading] = useState(true);

    const [searchTerm, setSearchTerm] = useState('');
    const [filterType, setFilterType] = useState('');

    // 1. KONFIGURÁCIÓ: Itt add meg a Backend mappád pontos elérési útját!
    // Ha a XAMPP-ban a mappa neve 'legora', akkor ez így helyes:
    const BASE_UPLOAD_URL = "http://localhost/legora/uploads/";

    const fetchListings = () => {
        setLoading(true);
        let url = '';

        const params = new URLSearchParams();
        if (searchTerm) params.append('q', searchTerm);
        if (filterType) params.append('item_type', filterType);

        // Döntés: search.php vagy get_listings.php
        if (searchTerm) {
            url = `/api/listings/search.php?${params.toString()}`;
        } else {
            url = `/api/listings/get_listings.php?${params.toString()}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(resp => {
                if (resp.status === 'success') {
                    const items = resp.data.listings || resp.data.results || [];
                    console.log("Beérkező adatok:", items); // 2. DEBUG: Nézd meg a konzolon, hogy van-e benne 'lego_data' vagy 'name'!
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

    useEffect(() => {
        fetchListings();
    }, [filterType]);

    const handleSearch = (e) => {
        e.preventDefault();
        fetchListings();
    };

    const handleReset = () => {
        setSearchTerm('');
        setFilterType('');
        window.location.reload();
    };

    // 3. SEGÉDFÜGGVÉNY: Kép URL generálása biztonságosan
    const getImageUrl = (l) => {
        // Ha van feltöltött saját kép
        if (l.image_url && l.image_url !== "") {
            return `${BASE_UPLOAD_URL}${l.image_url}`;
        }
        // Ha nincs, akkor a LEGO API kép (ha létezik a struktúrában)
        if (l.lego_data && l.lego_data.img_url) {
            return l.lego_data.img_url;
        }
        if (l.lego_meta && l.lego_meta.img_url) {
            return l.lego_meta.img_url;
        }
        // Végső esetben placeholder
        return "/no-image.png";
    };

    return (
        <div className="container">
            <div className="custom-hero-card mb-5 text-center">
                <h2 className="display-4 mb-3">Építs. Ossz meg. Cserélj.</h2>
                <p className="lead fs-3">Fedezz fel új kincseket és váltsd készpénzre a kockáidat!</p>
            </div>

            <div className="card p-4 mb-5 shadow-sm bg-light border-0">
                <form onSubmit={handleSearch} className="row g-3 align-items-center">
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
                    <div className="col-md-7">
                        <div className="input-group">
                            <input
                                type="text"
                                className="form-control border-primary"
                                placeholder="Pl. Star Wars, 75301..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                            <button className="btn btn-primary" type="submit">
                                <i className="fas fa-search me-2"></i>Keresés
                            </button>
                        </div>
                    </div>
                    <div className="col-md-2">
                        <button type="button" className="btn btn-outline-secondary w-100" onClick={handleReset}>
                            Mégse
                        </button>
                    </div>
                </form>
            </div>

            <h3 className="mb-4 text-white" style={{ textShadow: '1px 1px 4px rgba(0,0,0,0.5)' }}>
                {searchTerm ? `Találatok erre: "${searchTerm}"` : "Aktuális Hirdetések"}
            </h3>

            {loading ? (
                <div className="text-center text-white fs-4">Keresés...</div>
            ) : (
                <div className="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    {listings.length === 0 ? (
                        <div className="col-12">
                            <div className="alert alert-warning text-center">
                                Nincs találat.
                            </div>
                        </div>
                    ) : (
                        listings.map(l => (
                            <div className="col" key={l.id || l.listing_id}>
                                <div className="card h-100 listing-card border-0">
                                    <div className="position-relative bg-white rounded-top p-3 text-center">

                                        {/* 4. JAVÍTOTT KÉP MEGJELENÍTÉS (Nincs végtelen ciklus) */}
                                        <img
                                            src={getImageUrl(l)}
                                            className="img-fluid"
                                            style={{ height: '180px', objectFit: 'contain' }}
                                            alt="lego item"
                                            onError={(e) => {
                                                // Ha a kép hibás, azonnal a placeholderre váltunk, 
                                                // és letiltjuk a további hibafigyelést a ciklus elkerülése érdekében.
                                                e.target.onerror = null;
                                                e.target.src = "/no-image.png";
                                            }}
                                        />

                                        <span className={`position-absolute top-0 start-0 m-2 badge rounded-pill ${l.item_condition === 'new' ? 'bg-success' : 'bg-warning text-dark'}`}>
                                            {l.item_condition === 'new' ? 'ÚJ' : 'HASZNÁLT'}
                                        </span>
                                    </div>
                                    <div className="card-body d-flex flex-column bg-light">
                                        {/* Itt próbáljuk megjeleníteni a nevet */}
                                        <h5 className="card-title text-truncate" title={l.lego_data?.name || l.lego_meta?.name}>
                                            {l.lego_data?.name || l.lego_meta?.name || l.item_name || `Tétel #${l.item_id}`}
                                        </h5>
                                        <p className="card-text text-muted small mb-1">
                                            Azonosító: <strong>{l.item_id}</strong>
                                        </p>
                                        <p className="card-text text-muted small mb-3">
                                            Eladó: {l.seller || l.seller_name}
                                        </p>
                                        <p className="card-text text-muted small mb-1">
                                            Elérhető:{" "}
                                            {l.quantity > 0 ? (
                                                <span className="badge bg-success">{l.quantity} db</span>
                                            ) : (
                                                <span className="badge bg-danger">Elfogyott</span>
                                            )}
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
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function Cart() {
  const [cart, setCart] = useState({ items: [], summary: { subtotal: 0 } });
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  const loadCart = async () => {
    try {
      const res = await fetch('/api/cart/get_cart.php');
      const data = await res.json();
      if (data.status === 'success') setCart(data.data);
    } catch (err) { console.error(err); }
    setLoading(false);
  };

  useEffect(() => { loadCart(); }, []);

  const updateCart = async (listingId, qty, isAdd) => {
    const endpoint = isAdd ? '/api/cart/add_to_cart.php' : '/api/cart/remove_from_cart.php';
    const method = isAdd ? 'POST' : 'DELETE';
    
    await fetch(endpoint, {
      method: method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ listing_id: listingId, quantity: qty })
    });
    loadCart();
  };

  const checkout = async () => {
    if (!confirm("Biztosan leadod a rendelést?")) return;
    const res = await fetch('/api/orders/checkout.php', { method: 'POST' });
    const data = await res.json();
    if (data.status === 'success') {
      alert('Rendelés sikeresen leadva!');
      navigate('/profile');
    } else {
      alert('Hiba: ' + data.message);
    }
  };

  if (loading) return <div className="text-center mt-5">Betöltés...</div>;
  if (cart.items.length === 0) return <div className="text-center mt-5 alert alert-info">Üres a kosár.</div>;

  return (
    <div className="container mt-4 mb-5">
      <h3>Kosár</h3>
      <table className="table align-middle">
        <thead className="table-light">
          <tr><th>Termék</th><th>Ár</th><th>Mennyiség</th><th>Összesen</th><th></th></tr>
        </thead>
        <tbody>
          {cart.items.map(item => (
            <tr key={item.cart_item_id}>
              <td>
                <div className="d-flex align-items-center">
                  <img src={item.lego_data?.img_url || 'https://via.placeholder.com/50'} style={{width:50}} className="me-2 rounded"/>
                  <div>
                    <div className="fw-bold">{item.lego_data?.name || item.description}</div>
                    <small className="text-muted">{item.seller}</small>
                  </div>
                </div>
              </td>
              <td>{Number(item.price).toLocaleString()} Ft</td>
              <td>
                <button className="btn btn-sm btn-outline-secondary" onClick={() => updateCart(item.listing_id, 1, false)}>-</button>
                <span className="mx-2">{item.cart_quantity}</span>
                <button className="btn btn-sm btn-outline-secondary" onClick={() => updateCart(item.listing_id, 1, true)}>+</button>
              </td>
              <td className="fw-bold">{Number(item.line_total).toLocaleString()} Ft</td>
              <td>
                <button className="btn btn-sm btn-outline-danger" onClick={() => updateCart(item.listing_id, item.cart_quantity, false)}>Törlés</button>
              </td>
            </tr>
          ))}
        </tbody>
        <tfoot>
            <tr>
                <td colSpan="3" className="text-end fw-bold">Végösszeg:</td>
                <td className="fw-bold fs-5 text-primary">{Number(cart.summary.subtotal).toLocaleString()} Ft</td>
                <td></td>
            </tr>
        </tfoot>
      </table>
      <button className="btn btn-success float-end" onClick={checkout}>Megrendelés</button>
    </div>
  );
}
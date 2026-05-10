<script>
  let {
    products,
    orders,
    orderItems,
    addToOrder,
    updateCartItem,
    removeFromOrder,
    clearOrder,
    submitOrder,
    updateOrderStatus
  } = $props();

  let customerName = $state("");
  let distinctiveNotes = $state("");
  let remark = $state("");

  let pendingOrders = $derived(orders.filter(o => o.status === "pending"));
  let cartTotal = $derived(orderItems.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0));

  function handleSubmit() {
    if (orderItems.length === 0) {
      alert("กรุณาเลือกสินค้าอย่างน้อยหนึ่งรายการ");
      return;
    }

    submitOrder(customerName, distinctiveNotes, remark);
    customerName = "";
    distinctiveNotes = "";
    remark = "";
  }

  function cancelOrder(orderId) {
    if (confirm("ต้องการยกเลิกออเดอร์นี้หรือไม่?")) {
      updateOrderStatus(orderId, "cancelled");
    }
  }
</script>

<div class="order-workspace">
  <section class="menu-panel panel">
    <div class="section-head">
      <div>
        <p class="eyebrow">Menu Board</p>
        <h2 class="section-title">เลือกสินค้า</h2>
        <p class="section-subtitle">แตะเมนูเพื่อเพิ่มเข้าตะกร้า เหมาะกับการขายหน้าร้านแบบเร็ว</p>
      </div>
      <div class="menu-count">{products.length} เมนู</div>
    </div>

    <div class="products-grid">
      {#each products as product (product.id)}
        <button class="product-card" onclick={() => addToOrder(product, 1)}>
          <span class="product-initial">{product.name?.slice(0, 1)}</span>
          <span class="product-name">{product.name}</span>
          <span class="product-meta">
            <span>{product.category || "เมนูหลัก"}</span>
            <strong>{Number(product.price).toLocaleString()}฿</strong>
          </span>
        </button>
      {/each}
    </div>
  </section>

  <aside class="cart-panel panel">
    <div class="section-head compact">
      <div>
        <p class="eyebrow">Current Cart</p>
        <h2 class="section-title">ตะกร้า</h2>
      </div>
      <button class="ghost-action" onclick={clearOrder} disabled={orderItems.length === 0}>ล้าง</button>
    </div>

    {#if orderItems.length === 0}
      <div class="empty-state">ยังไม่มีสินค้าในตะกร้า</div>
    {:else}
      <div class="cart-list">
        {#each orderItems as item (item.product_id)}
          <div class="cart-item">
            <div>
              <strong>{item.name}</strong>
              <span>{Number(item.price).toLocaleString()}฿ ต่อรายการ</span>
            </div>
            <div class="qty-control" aria-label="จำนวนสินค้า">
              <button onclick={() => updateCartItem(item.product_id, -1)}>-</button>
              <span>{item.quantity}</span>
              <button onclick={() => updateCartItem(item.product_id, 1)}>+</button>
            </div>
            <button class="remove-btn" aria-label="ลบสินค้า" onclick={() => removeFromOrder(item.product_id)}>ลบ</button>
          </div>
        {/each}
      </div>
    {/if}

    <div class="customer-form">
      <label>
        ชื่อลูกค้า
        <input type="text" placeholder="เช่น คุณต้อม" bind:value={customerName} />
      </label>
      <label>
        จุดสังเกต
        <input type="text" placeholder="เสื้อแดง โต๊ะ 3 หมวกดำ" bind:value={distinctiveNotes} />
      </label>
      <label>
        หมายเหตุ
        <textarea placeholder="ไม่เผ็ด แยกน้ำ เพิ่มข้าว" bind:value={remark} rows="3"></textarea>
      </label>
    </div>

    <div class="checkout-bar">
      <div>
        <span>ยอดรวม</span>
        <strong>{cartTotal.toLocaleString()}฿</strong>
      </div>
      <button class="primary-action" onclick={handleSubmit} disabled={orderItems.length === 0}>ส่งออเดอร์</button>
    </div>
  </aside>

  <section class="queue-panel panel">
    <div class="section-head compact">
      <div>
        <p class="eyebrow">Queue</p>
        <h2 class="section-title">คิวรอทำ</h2>
      </div>
      <div class="menu-count">{pendingOrders.length} คิว</div>
    </div>

    {#if pendingOrders.length === 0}
      <div class="empty-state">ไม่มีคิวค้างอยู่ พร้อมรับออเดอร์ใหม่</div>
    {:else}
      <div class="queue-list">
        {#each pendingOrders as order (order.id)}
          <article class="queue-card">
            <div class="queue-topline">
              <strong>#{order.queue_number}</strong>
              <span>รอทำ</span>
            </div>
            {#if order.customer_name}
              <h3>{order.customer_name}</h3>
            {/if}
            {#if order.distinctive_notes}
              <p class="notes">{order.distinctive_notes}</p>
            {/if}
            <div class="order-items">
              {#each order.items as item (item.id)}
                <span>{item.name} x {item.quantity}</span>
              {/each}
            </div>
            <div class="order-actions">
              <button class="secondary-action" onclick={() => updateOrderStatus(order.id, "completed")}>เสร็จแล้ว</button>
              <button class="danger-action" onclick={() => cancelOrder(order.id)}>ยกเลิก</button>
            </div>
          </article>
        {/each}
      </div>
    {/if}
  </section>
</div>

<style>
  .order-workspace {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 390px;
    gap: 18px;
    align-items: start;
  }

  .menu-panel,
  .cart-panel,
  .queue-panel {
    padding: 22px;
  }

  .cart-panel {
    position: sticky;
    top: 22px;
  }

  .queue-panel {
    grid-column: 1 / -1;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
  }

  .section-head.compact {
    align-items: center;
  }

  .menu-count {
    flex: 0 0 auto;
    border-radius: 999px;
    padding: 9px 13px;
    color: var(--brand-dark);
    font-size: 13px;
    font-weight: 900;
    background: #fff1d2;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
  }

  .product-card {
    display: grid;
    min-height: 152px;
    border: 1px solid rgba(45, 35, 26, 0.1);
    border-radius: 22px;
    padding: 14px;
    color: var(--ink);
    text-align: left;
    background: rgba(255, 255, 255, 0.7);
    box-shadow: 0 10px 24px rgba(45, 35, 26, 0.06);
    transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
  }

  .product-card:hover {
    transform: translateY(-3px);
    border-color: rgba(242, 159, 5, 0.45);
    box-shadow: 0 18px 36px rgba(45, 35, 26, 0.1);
  }

  .product-initial {
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border-radius: 15px;
    color: var(--brand-dark);
    font-weight: 950;
    background: #fff1d2;
  }

  .product-name {
    margin-top: 18px;
    font-size: 18px;
    font-weight: 900;
    line-height: 1.15;
  }

  .product-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .product-meta strong {
    color: var(--ink);
    font-size: 17px;
  }

  .cart-list {
    display: grid;
    gap: 10px;
  }

  .cart-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto auto;
    gap: 10px;
    align-items: center;
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.62);
  }

  .cart-item strong,
  .cart-item span {
    display: block;
  }

  .cart-item strong {
    line-height: 1.2;
  }

  .cart-item span {
    margin-top: 3px;
    color: var(--muted);
    font-size: 12px;
  }

  .qty-control {
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    padding: 4px;
    background: rgba(33, 27, 22, 0.06);
  }

  .qty-control button {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    border: 0;
    border-radius: 999px;
    font-weight: 950;
    background: white;
  }

  .qty-control span {
    min-width: 20px;
    margin: 0;
    color: var(--ink);
    text-align: center;
    font-weight: 900;
  }

  .remove-btn {
    border: 0;
    color: var(--red);
    font-size: 12px;
    font-weight: 900;
    background: transparent;
  }

  .customer-form {
    display: grid;
    gap: 10px;
    margin-top: 16px;
  }

  label {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input,
  textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 12px 14px;
    color: var(--ink);
    background: rgba(255, 255, 255, 0.78);
  }

  textarea {
    resize: vertical;
  }

  .checkout-bar {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 12px;
    align-items: center;
    margin-top: 16px;
    border-top: 1px solid var(--line);
    padding-top: 16px;
  }

  .checkout-bar span,
  .checkout-bar strong {
    display: block;
  }

  .checkout-bar span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  .checkout-bar strong {
    font-size: 30px;
    line-height: 1;
  }

  .queue-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 12px;
  }

  .queue-card {
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.64);
  }

  .queue-topline {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .queue-topline strong {
    font-size: 28px;
  }

  .queue-topline span {
    border-radius: 999px;
    padding: 6px 10px;
    color: var(--brand-dark);
    font-size: 12px;
    font-weight: 900;
    background: #fff1d2;
  }

  .queue-card h3 {
    margin: 10px 0 4px;
    font-size: 20px;
  }

  .notes {
    margin: 6px 0;
    border-radius: 14px;
    padding: 9px 10px;
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
    background: rgba(33, 27, 22, 0.06);
  }

  .order-items {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
  }

  .order-items span {
    border-radius: 999px;
    padding: 6px 9px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    background: white;
  }

  .order-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 14px;
  }

  @media (max-width: 1180px) {
    .order-workspace {
      grid-template-columns: 1fr;
    }

    .cart-panel {
      position: static;
    }
  }

  @media (max-width: 640px) {
    .menu-panel,
    .cart-panel,
    .queue-panel {
      padding: 16px;
      border-radius: 22px;
    }

    .section-head,
    .section-head.compact {
      flex-direction: column;
      align-items: flex-start;
    }

    .products-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .product-card {
      min-height: 138px;
    }

    .cart-item {
      grid-template-columns: 1fr;
    }

    .checkout-bar {
      grid-template-columns: 1fr;
    }
  }
</style>

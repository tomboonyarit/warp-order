<script>
  import { onMount } from "svelte";
  import OrderTab from "./components/OrderTab.svelte";
  import KitchenTab from "./components/KitchenTab.svelte";
  import UserTab from "./components/UserTab.svelte";
  import ReportTab from "./components/ReportTab.svelte";

  let activeTab = $state("order");
  let products = $state([]);
  let orders = $state([]);
  let orderItems = $state([]);

  let pendingCount = $derived(orders.filter(order => order.status === "pending").length);
  let cookingCount = $derived(orders.filter(order => order.status === "cooking").length);
  let completedCount = $derived(orders.filter(order => order.status === "completed").length);

  const tabs = [
    { id: "order", label: "ขายหน้าร้าน", short: "ขาย", icon: "POS" },
    { id: "kitchen", label: "ครัว", short: "ครัว", icon: "KDS" },
    { id: "users", label: "ผู้ใช้", short: "ผู้ใช้", icon: "TEAM" },
    { id: "reports", label: "รายงาน", short: "รายงาน", icon: "SALE" }
  ];

  onMount(async () => {
    await loadProducts();
    await loadOrders();
  });

  async function loadProducts() {
    const res = await fetch("/api/products");
    products = await res.json();
  }

  async function loadOrders() {
    const res = await fetch("/api/orders");
    orders = await res.json();
  }

  function addToOrder(product, quantity = 1) {
    const existing = orderItems.find(item => item.product_id === product.id);
    if (existing) {
      orderItems = orderItems.map(item => item.product_id === product.id
        ? { ...item, quantity: item.quantity + quantity }
        : item
      );
    } else {
      orderItems = [...orderItems, { ...product, product_id: product.id, quantity }];
    }
  }

  function updateCartItem(productId, delta) {
    orderItems = orderItems
      .map(item => item.product_id === productId ? { ...item, quantity: item.quantity + delta } : item)
      .filter(item => item.quantity > 0);
  }

  function removeFromOrder(productId) {
    orderItems = orderItems.filter(item => item.product_id !== productId);
  }

  function clearOrder() {
    orderItems = [];
  }

  async function submitOrder(customerName, distinctiveNotes, remark) {
    const items = orderItems.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      price: item.price
    }));

    await fetch("/api/orders", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        customer_name: customerName,
        distinctive_notes: distinctiveNotes,
        remark,
        items
      })
    });

    orderItems = [];
    await loadOrders();
    alert("Order ถูกส่งเรียบร้อย");
  }

  async function updateOrderStatus(orderId, status) {
    await fetch(`/api/orders/status/${orderId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ status })
    });
    await loadOrders();
  }
</script>

<div class="app-shell">
  <aside class="sidebar panel" aria-label="เมนูหลัก">
    <div class="brand-lockup">
      <div class="brand-mark">W</div>
      <div>
        <p class="eyebrow">Warp Order</p>
        <h1>Counter OS</h1>
      </div>
    </div>

    <nav class="main-nav">
      {#each tabs as tab (tab.id)}
        <button class:active={activeTab === tab.id} onclick={() => activeTab = tab.id}>
          <span>{tab.icon}</span>
          {tab.label}
        </button>
      {/each}
    </nav>

    <div class="queue-snapshot">
      <p class="eyebrow">Live Queue</p>
      <div><strong>{pendingCount}</strong><span>รอรับออเดอร์</span></div>
      <div><strong>{cookingCount}</strong><span>กำลังทำ</span></div>
      <div><strong>{completedCount}</strong><span>เสร็จแล้ว</span></div>
    </div>
  </aside>

  <main class="workspace">
    <header class="topbar panel">
      <div>
        <p class="eyebrow">Today Service</p>
        <h2>{activeTab === "order" ? "หน้าร้านพร้อมรับออเดอร์" : activeTab === "kitchen" ? "จอครัวและสถานะอาหาร" : activeTab === "users" ? "ทีมและสิทธิ์เข้าใช้งาน" : "ยอดขายและภาพรวม"}</h2>
      </div>
      <div class="topbar-stats" aria-label="สรุปสถานะ">
        <span>{products.length} เมนู</span>
        <span>{orders.length} ออเดอร์</span>
        <span>{orderItems.length} ในตะกร้า</span>
      </div>
    </header>

    <section class="tab-content">
    {#if activeTab === "order"}
      <OrderTab {products} {orders} {orderItems} {addToOrder} {updateCartItem} {removeFromOrder} {clearOrder} {submitOrder} {updateOrderStatus} />
    {:else if activeTab === "kitchen"}
      <KitchenTab {orders} {updateOrderStatus} />
    {:else if activeTab === "users"}
      <UserTab />
    {:else if activeTab === "reports"}
      <ReportTab {orders} />
    {/if}
    </section>
  </main>

  <nav class="mobile-nav panel" aria-label="เมนูมือถือ">
    {#each tabs as tab (tab.id)}
      <button class:active={activeTab === tab.id} onclick={() => activeTab = tab.id}>
        <span>{tab.icon}</span>
        {tab.short}
      </button>
    {/each}
  </nav>
</div>

<style>
  .app-shell {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 22px;
    min-height: 100vh;
    padding: 22px;
  }

  .sidebar {
    position: sticky;
    top: 22px;
    display: flex;
    flex-direction: column;
    align-self: start;
    min-height: calc(100vh - 44px);
    padding: 20px;
  }

  .brand-lockup {
    display: flex;
    gap: 14px;
    align-items: center;
    padding-bottom: 22px;
    border-bottom: 1px solid var(--line);
  }

  .brand-mark {
    display: grid;
    width: 54px;
    height: 54px;
    place-items: center;
    border-radius: 18px;
    color: #221707;
    font-weight: 950;
    background: linear-gradient(135deg, #ffe1a3, var(--brand));
    box-shadow: 0 16px 34px rgba(242, 159, 5, 0.28);
  }

  h1,
  h2 {
    margin: 0;
    color: var(--ink);
    letter-spacing: -0.04em;
  }

  h1 {
    font-size: 25px;
    line-height: 1;
  }

  h2 {
    font-size: clamp(24px, 3vw, 38px);
    line-height: 1.02;
  }

  .main-nav {
    display: grid;
    gap: 10px;
    margin-top: 22px;
  }

  .main-nav button,
  .mobile-nav button {
    border: 0;
    color: var(--muted);
    background: transparent;
    font-weight: 800;
    transition: color 160ms ease, background 160ms ease, transform 160ms ease;
  }

  .main-nav button {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 52px;
    border-radius: 18px;
    padding: 0 14px;
    text-align: left;
  }

  .main-nav span,
  .mobile-nav span {
    color: var(--brand-dark);
    font-size: 10px;
    letter-spacing: 0.12em;
  }

  .main-nav button.active,
  .mobile-nav button.active {
    color: var(--ink);
    background: #fff3d8;
  }

  .main-nav button:hover {
    transform: translateX(2px);
    background: rgba(255, 255, 255, 0.52);
  }

  .queue-snapshot {
    display: grid;
    gap: 10px;
    margin-top: auto;
    padding-top: 22px;
  }

  .queue-snapshot div {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    border-radius: 16px;
    padding: 13px 14px;
    background: rgba(255, 255, 255, 0.52);
  }

  .queue-snapshot strong {
    font-size: 28px;
  }

  .queue-snapshot span {
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
  }

  .workspace {
    display: grid;
    gap: 18px;
    min-width: 0;
  }

  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 22px 24px;
  }

  .topbar-stats {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
  }

  .topbar-stats span {
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 8px 12px;
    color: var(--muted);
    font-size: 13px;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.58);
  }

  .tab-content {
    min-width: 0;
  }

  .mobile-nav {
    position: fixed;
    right: 12px;
    bottom: 12px;
    left: 12px;
    z-index: 10;
    display: none;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    padding: 8px;
  }

  .mobile-nav button {
    display: grid;
    gap: 2px;
    min-height: 56px;
    place-items: center;
    border-radius: 18px;
    font-size: 12px;
  }

  @media (max-width: 980px) {
    .app-shell {
      display: block;
      padding: 12px 12px 92px;
    }

    .sidebar {
      display: none;
    }

    .workspace {
      gap: 12px;
    }

    .topbar {
      align-items: flex-start;
      flex-direction: column;
      padding: 18px;
    }

    .topbar-stats {
      justify-content: flex-start;
    }

    .mobile-nav {
      display: grid;
    }
  }
</style>

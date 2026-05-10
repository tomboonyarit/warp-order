<script>
  import { onMount } from "svelte";
  import OrderTab from "./components/OrderTab.svelte";
  import KitchenTab from "./components/KitchenTab.svelte";
  import FoodManagementTab from "./components/FoodManagementTab.svelte";
  import SetupTab from "./components/SetupTab.svelte";
  import UserTab from "./components/UserTab.svelte";
  import ReportTab from "./components/ReportTab.svelte";

  let activeTab = $state("order");
  let products = $state([]);
  let orders = $state([]);
  let orderItems = $state([]);
  let setupData = $state({ menu_categories: [], note_groups: [] });

  const tabs = [
    { id: "order", label: "ขายหน้าร้าน", short: "ขาย", icon: "POS", title: "หน้าร้านพร้อมรับออเดอร์" },
    { id: "kitchen", label: "ครัว", short: "ครัว", icon: "KDS", title: "จอครัวและสถานะอาหาร" },
    { id: "foods", label: "เมนูอาหาร", short: "เมนู", icon: "FOOD", title: "จัดการเมนูอาหาร" },
    { id: "setup", label: "ตั้งค่า", short: "ตั้งค่า", icon: "SET", title: "ตั้งค่าข้อมูลพื้นฐาน" },
    { id: "users", label: "ผู้ใช้", short: "ผู้ใช้", icon: "TEAM", title: "ทีมและสิทธิ์เข้าใช้งาน" },
    { id: "reports", label: "รายงาน", short: "รายงาน", icon: "SALE", title: "ยอดขายและภาพรวม" }
  ];

  let pendingCount = $derived(orders.filter(order => order.status === "pending").length);
  let cookingCount = $derived(orders.filter(order => order.status === "cooking").length);
  let completedCount = $derived(orders.filter(order => order.status === "completed").length);
  let activeTitle = $derived(tabs.find(tab => tab.id === activeTab)?.title ?? "หน้าร้านพร้อมรับออเดอร์");

  onMount(async () => {
    await loadSetup();
    await loadProducts();
    await loadOrders();
  });

  async function loadSetup() {
    const res = await fetch("/api/setup");
    setupData = await res.json();
  }

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

  async function createProduct(product) {
    await fetch("/api/products", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(product)
    });
    await loadProducts();
  }

  async function updateProduct(productId, product) {
    await fetch(`/api/products/${productId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(product)
    });
    await loadProducts();
  }

  async function archiveProduct(productId) {
    await fetch(`/api/products/${productId}`, {
      method: "DELETE"
    });
    await loadProducts();
  }

  async function createMenuCategory(category) {
    await fetch("/api/setup/menu-categories", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(category)
    });
    await loadSetup();
  }

  async function updateMenuCategory(categoryId, category) {
    await fetch(`/api/setup/menu-categories/${categoryId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(category)
    });
    await loadSetup();
  }

  async function archiveMenuCategory(categoryId) {
    await fetch(`/api/setup/menu-categories/${categoryId}`, { method: "DELETE" });
    await loadSetup();
  }

  async function createNoteGroup(group) {
    await fetch("/api/setup/note-groups", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(group)
    });
    await loadSetup();
  }

  async function updateNoteGroup(groupId, group) {
    await fetch(`/api/setup/note-groups/${groupId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(group)
    });
    await loadSetup();
  }

  async function archiveNoteGroup(groupId) {
    await fetch(`/api/setup/note-groups/${groupId}`, { method: "DELETE" });
    await loadSetup();
  }

  async function createNoteOption(option) {
    await fetch("/api/setup/note-options", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(option)
    });
    await loadSetup();
  }

  async function updateNoteOption(optionId, option) {
    await fetch(`/api/setup/note-options/${optionId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(option)
    });
    await loadSetup();
  }

  async function archiveNoteOption(optionId) {
    await fetch(`/api/setup/note-options/${optionId}`, { method: "DELETE" });
    await loadSetup();
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
        <h2>{activeTitle}</h2>
      </div>
      <div class="topbar-stats" aria-label="สรุปสถานะ">
        <span>{products.length} เมนู</span>
        <span>{setupData.note_groups.length} จุดสังเกต</span>
        <span>{orders.length} ออเดอร์</span>
        <span>{orderItems.length} ในตะกร้า</span>
      </div>
    </header>

    <section class="tab-content">
    {#if activeTab === "order"}
      <OrderTab {products} {orders} {orderItems} noteGroups={setupData.note_groups} {addToOrder} {updateCartItem} {removeFromOrder} {clearOrder} {submitOrder} {updateOrderStatus} />
    {:else if activeTab === "kitchen"}
      <KitchenTab {orders} {updateOrderStatus} />
    {:else if activeTab === "foods"}
      <FoodManagementTab {products} menuCategories={setupData.menu_categories} {createProduct} {updateProduct} {archiveProduct} />
    {:else if activeTab === "setup"}
      <SetupTab
        {setupData}
        {createMenuCategory}
        {updateMenuCategory}
        {archiveMenuCategory}
        {createNoteGroup}
        {updateNoteGroup}
        {archiveNoteGroup}
        {createNoteOption}
        {updateNoteOption}
        {archiveNoteOption}
      />
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
    grid-template-columns: repeat(6, minmax(0, 1fr));
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

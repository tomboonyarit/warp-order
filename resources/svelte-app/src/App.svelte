<script>
  import { onMount } from "svelte";
  import OrderTab from "./components/OrderTab.svelte";
  import KitchenTab from "./components/KitchenTab.svelte";
  import FoodManagementTab from "./components/FoodManagementTab.svelte";
  import SetupTab from "./components/SetupTab.svelte";
  import UserTab from "./components/UserTab.svelte";
  import ReportTab from "./components/ReportTab.svelte";
  import { t, setLocale, i18n, locales } from "./lib/i18n.svelte.js";

  let activeTab = $state("order");
  let products = $state([]);
  let orders = $state([]);
  let orderItems = $state([]);
  let setupData = $state({ menu_categories: [], note_groups: [] });

  const tabs = [
    { id: "order", labelKey: "tab.order_label", shortKey: "tab.order_short", icon: "POS", titleKey: "tab.order_title" },
    { id: "kitchen", labelKey: "tab.kitchen_label", shortKey: "tab.kitchen_short", icon: "KDS", titleKey: "tab.kitchen_title" },
    { id: "foods", labelKey: "tab.foods_label", shortKey: "tab.foods_short", icon: "FOOD", titleKey: "tab.foods_title" },
    { id: "setup", labelKey: "tab.setup_label", shortKey: "tab.setup_short", icon: "SET", titleKey: "tab.setup_title" },
    { id: "users", labelKey: "tab.users_label", shortKey: "tab.users_short", icon: "TEAM", titleKey: "tab.users_title" },
    { id: "reports", labelKey: "tab.reports_label", shortKey: "tab.reports_short", icon: "SALE", titleKey: "tab.reports_title" }
  ];

  let pendingCount = $derived(orders.filter(order => order.status === "pending").length);
  let cookingCount = $derived(orders.filter(order => order.status === "cooking").length);
  let completedCount = $derived(orders.filter(order => order.status === "completed").length);
  let activeTitle = $derived(t(tabs.find(tab => tab.id === activeTab)?.titleKey ?? "tab.order_title"));

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
    alert(t("app.alert_order_submitted"));
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
  <aside class="sidebar panel" aria-label={t("app.sidebar_aria")}>
    <div class="brand-lockup">
      <div class="brand-mark">W</div>
      <div>
        <p class="eyebrow">Warp Order</p>
        <h1>{t("app.brand_subtitle")}</h1>
      </div>
    </div>

    <div class="lang-switcher">
      {#each locales as l (l.code)}
        <button
          class:active={i18n.locale === l.code}
          onclick={() => setLocale(l.code)}
          title={l.name}
        >{l.code.toUpperCase()}</button>
      {/each}
    </div>

    <nav class="main-nav">
      {#each tabs as tab (tab.id)}
        <button class:active={activeTab === tab.id} onclick={() => activeTab = tab.id}>
          <span>{tab.icon}</span>
          {t(tab.labelKey)}
        </button>
      {/each}
    </nav>

    <div class="queue-snapshot">
      <p class="eyebrow">{t("app.queue_title")}</p>
      <div><strong>{pendingCount}</strong><span>{t("app.queue_pending")}</span></div>
      <div><strong>{cookingCount}</strong><span>{t("app.queue_cooking")}</span></div>
      <div><strong>{completedCount}</strong><span>{t("app.queue_completed")}</span></div>
    </div>
  </aside>

  <main class="workspace">
    <header class="topbar panel">
      <div>
        <p class="eyebrow">{t("app.topbar_title")}</p>
        <h2>{activeTitle}</h2>
      </div>
      <div class="topbar-stats" aria-label={t("app.topbar_aria")}>
        <span>{t("app.topbar_products", { n: products.length })}</span>
        <span>{t("app.topbar_note_groups", { n: setupData.note_groups.length })}</span>
        <span>{t("app.topbar_orders", { n: orders.length })}</span>
        <span>{t("app.topbar_cart", { n: orderItems.length })}</span>
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

  <nav class="mobile-nav panel" aria-label={t("app.mobile_nav_aria")}>
    {#each tabs as tab (tab.id)}
      <button class:active={activeTab === tab.id} onclick={() => activeTab = tab.id}>
        <span>{tab.icon}</span>
        {t(tab.shortKey)}
      </button>
    {/each}
    <div class="mobile-lang">
      {#each locales as l (l.code)}
        <button
          class:active={i18n.locale === l.code}
          onclick={() => setLocale(l.code)}
          title={l.name}
        >{l.code.toUpperCase()}</button>
      {/each}
    </div>
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
    color: white;
    font-weight: 950;
    background: linear-gradient(135deg, #ff8a8a, var(--brand));
    box-shadow: 0 16px 34px rgba(255, 107, 107, 0.28);
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
    color: var(--brand);
    font-size: 10px;
    letter-spacing: 0.12em;
  }

  .main-nav button.active,
  .mobile-nav button.active {
    color: var(--ink);
    background: var(--orange-soft);
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

  .lang-switcher {
    display: flex;
    gap: 6px;
    padding: 14px 0;
    border-bottom: 1px solid var(--line);
    margin-bottom: 4px;
  }

  .lang-switcher button,
  .mobile-lang button {
    flex: 1;
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 6px 8px;
    color: var(--muted);
    font-size: 11px;
    font-weight: 900;
    background: rgba(255, 255, 255, 0.52);
    cursor: pointer;
    transition: all 160ms ease;
  }

  .lang-switcher button.active,
  .mobile-lang button.active {
    color: var(--brand);
    border-color: rgba(255, 107, 107, 0.5);
    background: var(--orange-soft);
  }

  .mobile-lang {
    display: flex;
    gap: 4px;
    grid-column: 1 / -1;
    margin-top: 4px;
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

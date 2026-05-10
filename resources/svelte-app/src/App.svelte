<script>
  import { onMount } from 'svelte';
  import OrderTab from './components/OrderTab.svelte';
  import KitchenTab from './components/KitchenTab.svelte';
  import UserTab from './components/UserTab.svelte';
  import ReportTab from './components/ReportTab.svelte';

  let activeTab = 'order';
  let products = [];
  let orders = [];
  let customers = [];
  let selectedCustomer = null;
  let orderItems = [];

  onMount(async () => {
    await loadProducts();
    await loadOrders();
  });

  async function loadProducts() {
    const res = await fetch('/api/products');
    products = await res.json();
  }

  async function loadOrders() {
    const res = await fetch('/api/orders');
    orders = await res.json();
  }

  function addToOrder(product, quantity = 1) {
    const existing = orderItems.find(item => item.product_id === product.id);
    if (existing) {
      existing.quantity += quantity;
    } else {
      orderItems.push({ ...product, product_id: product.id, quantity });
    }
    orderItems = orderItems;
  }

  async function submitOrder(customerName, distinctiveNotes, remark) {
    const items = orderItems.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity,
      price: item.price
    }));

    const res = await fetch('/api/orders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ customerName, distinctiveNotes, remark, items })
    });

    orderItems = [];
    await loadOrders();
    alert('Order ถูกส่งเรียบร้อย');
  }

  async function updateOrderStatus(orderId, status) {
    await fetch(`/api/orders/status/${orderId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status })
    });
    await loadOrders();
  }
</script>

<div class="app">
  <div class="tabs">
    <button class={activeTab === 'order' ? 'active'} on:click={() => activeTab = 'order'}>สั่ง-แสดงคิว</button>
    <button class={activeTab === 'kitchen' ? 'active'} on:click={() => activeTab = 'kitchen'}>คิวอาหารในครัว</button>
    <button class={activeTab === 'users' ? 'active'} on:click={() => activeTab = 'users'}>จัดการผู้ใช้</button>
    <button class={activeTab === 'reports' ? 'active'} on:click={() => activeTab = 'reports'}>รายงาน</button>
  </div>

  <div class="tab-content">
    {#if activeTab === 'order'}
      <OrderTab {products} {orders} {orderItems} {addToOrder} {submitOrder} {updateOrderStatus} />
    {:else if activeTab === 'kitchen'}
      <KitchenTab {orders} {updateOrderStatus} />
    {:else if activeTab === 'users'}
      <UserTab />
    {:else if activeTab === 'reports'}
      <ReportTab {orders} />
    {/if}
  </div>
</div>

<style>
  .app {
    max-width: 500px;
    margin: 0 auto;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }
  .tabs {
    display: flex;
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
  }
  .tabs button {
    flex: 1;
    padding: 12px 8px;
    border: none;
    background: transparent;
    font-size: 12px;
    cursor: pointer;
    border-bottom: 3px solid transparent;
  }
  .tabs button.active {
    font-weight: bold;
    border-color: #007bff;
    color: #007bff;
  }
  .tab-content {
    padding: 16px;
    min-height: 500px;
  }
</style>
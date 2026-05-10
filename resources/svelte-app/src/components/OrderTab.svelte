<script>
  export let products;
  export let orders;
  export let orderItems;
  export let addToOrder;
  export let submitOrder;
  export let updateOrderStatus;

  let customerName = '';
  let distinctiveNotes = '';
  let remark = '';
  let activeTab = 'products';
  let activeOrderId = null;

  $: pendingOrders = orders.filter(o => o.status === 'pending');
  $: completedOrders = orders.filter(o => o.status === 'completed');

  function handleSubmit() {
    if (orderItems.length === 0) {
      alert('กรุณาเลือกสินค้าอย่างน้อยหนึ่งรายการ');
      return;
    }
    submitOrder(customerName, distinctiveNotes, remark);
    customerName = '';
    distinctiveNotes = '';
    remark = '';
  }

  function cancelOrder(orderId) {
    if (confirm('ต้องการยกเลิก order นี้หระ?')) {
      updateOrderStatus(orderId, 'cancelled');
    }
  }
</script>

<div class="order-tab">
  <div class="tabs">
    <button class={activeTab === 'products' ? 'active'} on:click={() => activeTab = 'products'}>เลือกสินค้า</button>
    <button class={activeTab === 'cart' ? 'active'} on:click={() => activeTab = 'cart'}>
      ตะกร้า ({orderItems.length})
    </button>
    <button class={activeTab === 'queue' ? 'active'} on:click={() => activeTab = 'queue'}>
      คิว ({pendingOrders.length})
    </button>
  </div>

  {#if activeTab === 'products'}
    <div class="products-grid">
      {#each products as product}
        <button class="product-item" on:click={() => addToOrder(product, 1)}>
          <div class="product-name">{product.name}</div>
          <div class="product-price">{product.price}฿</div>
        </button>
      {/each}
    </div>
  {:else if activeTab === 'cart'}
    <div class="cart">
      {#each orderItems as item}
        <div class="cart-item">
          <span class="item-name">{item.name}</span>
          <span class="item-qty">x {item.quantity}</span>
          <span class="item-price">{item.price * item.quantity}฿</span>
        </div>
      {/each}
      
      <div class="order-form">
        <input type="text" placeholder="ชื่อลูกค้า" bind:value={customerName} />
        <input type="text" placeholder="จุดสังเกต (เช่น สีเสื้อ, แว่น, หมวก)" bind:value={distinctiveNotes} />
        <textarea placeholder="หมายเหตุ" bind:value={remark} rows="2"></textarea>
        <button class="submit-btn" on:click={handleSubmit}>สั่งซื้อ</button>
      </div>
    </div>
  {:else if activeTab === 'queue'}
    <div class="queue">
      <h3>คิวปัจจุบัน ({pendingOrders.length})</h3>
      {#each pendingOrders as order}
        <div class="order-card pending">
          <div class="order-header">
            <span class="queue-number">คิว #{order.queue_number}</span>
          </div>
          {#if order.customer_name}
            <div class="customer-name">{order.customer_name}</div>
          {/if}
          {#if order.distinctive_notes}
            <div class="notes">{order.distinctive_notes}</div>
          {/if}
          <div class="order-items">
            {#each order.items as item}
              <span class="item">{item.name} x {item.quantity}</span>
            {/each}
          </div>
          <div class="order-actions">
            <button class="complete-btn" on:click={() => updateOrderStatus(order.id, 'completed')}>เสร็จ</button>
            <button class="cancel-btn" on:click={() => cancelOrder(order.id)}>ยกเลิก</button>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>

<style>
  .products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  .product-item {
    padding: 12px 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
  }
  .product-name {
    font-size: 12px;
    font-weight: 500;
  }
  .product-price {
    font-size: 11px;
    color: #007bff;
  }
  .cart-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }
  .order-form input, .order-form textarea {
    width: 100%;
    padding: 8px;
    margin: 4px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  .submit-btn {
    width: 100%;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    margin-top: 8px;
  }
  .order-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
    margin: 8px 0;
  }
  .order-header {
    display: flex;
    justify-content: space-between;
  }
  .queue-number {
    font-weight: bold;
  }
  .customer-name {
    font-weight: 500;
    margin: 4px 0;
  }
  .notes {
    font-size: 12px;
    color: #666;
  }
  .order-items {
    font-size: 12px;
    color: #888;
    margin: 4px 0;
  }
  .order-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
  }
  .complete-btn, .cancel-btn {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .complete-btn {
    background: #28a745;
    color: white;
  }
  .cancel-btn {
    background: #dc3545;
    color: white;
  }
</style>
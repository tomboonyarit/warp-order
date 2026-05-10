<script>
  export let orders;
  export let updateOrderStatus;

  $: kitchenOrders = orders.filter(o => o.status === 'pending' || o.status === 'cooking');
</script>

<div class="kitchen-tab">
  <h2>คิวอาหารในครัว</h2>
  
  {#each kitchenOrders as order}
    <div class="order-card {order.status}">
      <div class="order-header">
        <span class="queue-number">คิว #{order.queue_number}</span>
        <span class="status {order.status}">{order.status === 'pending' ? 'รอ' : 'กำลังทำ'}</span>
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
        {#if order.status === 'pending'}
          <button class="start-btn" on:click={() => updateOrderStatus(order.id, 'cooking')}>
            เริ่มทำ
          </button>
        {:else}
          <button class="complete-btn" on:click={() => updateOrderStatus(order.id, 'completed')}>
            เสร็จ
          </button>
        {/if}
      </div>
    </div>
  {/each}
  
  {#if kitchenOrders.length === 0}
    <div class="empty">ไม่มีคิวอาหาร</div>
  {/if}
</div>

<style>
  .order-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
    margin: 8px 0;
    background: white;
  }
  .order-card.pending {
    border-left: 4px solid #ffc107;
  }
  .order-card.cooking {
    border-left: 4px solid #007bff;
  }
  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .status {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 12px;
  }
  .status.pending {
    background: #fff3cd;
    color: #856404;
  }
  .status.cooking {
    background: #cce5ff;
    color: #004085;
  }
  .customer-name {
    font-weight: 500;
    margin: 4px 0;
  }
  .notes {
    font-size: 12px;
    color: #666;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 4px 0;
  }
  .order-items {
    font-size: 12px;
    color: #888;
    margin: 4px 0;
  }
  .start-btn, .complete-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  .start-btn {
    background: #007bff;
    color: white;
  }
  .complete-btn {
    background: #28a745;
    color: white;
  }
  .empty {
    text-align: center;
    padding: 20px;
    color: #888;
  }
</style>
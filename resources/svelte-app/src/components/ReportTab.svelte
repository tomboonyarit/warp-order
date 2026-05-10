<script>
  export let orders;

  $: completedOrders = orders.filter(o => o.status === 'completed');
  $: totalRevenue = completedOrders.reduce((sum, o) => {
    const orderTotal = o.items.reduce((s, i) => s + (i.price * i.quantity), 0);
    return sum + orderTotal;
  }, 0);

  let selectedDate = new Date().toISOString().split('T')[0];
</script>

<div class="report-tab">
  <h2>รายงาน</h2>
  
  <div class="date-filter">
    <label>เลือกวัน:</label>
    <input type="date" bind:value={selectedDate} />
  </div>

  <div class="summary">
    <div class="summary-card">
      <div class="label">ยอดขายรวม</div>
      <div class="value">{totalRevenue.toLocaleString()}฿</div>
    </div>
    
    <div class="summary-card">
      <div class="label">จำนวน order</div>
      <div class="value">{completedOrders.length}</div>
    </div>
  </div>

  <div class="sales-list">
    <h3>รายการขาย</h3>
    {#each completedOrders as order}
      <div class="sales-item">
        <span class="queue">คิว #{order.queue_number}</span>
        <span class="time">{order.created_at?.split(' ')[1]?.substring(0, 5)}</span>
        <span class="total">{order.items.reduce((s, i) => s + (i.price * i.quantity), 0)}฿</span>
      </div>
    {/each}
  </div>
</div>

<style>
  .summary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 16px 0;
  }
  .summary-card {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
  }
  .label {
    font-size: 12px;
    color: #666;
  }
  .value {
    font-size: 20px;
    font-weight: bold;
    color: #007bff;
  }
  .sales-item {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    border-bottom: 1px solid #eee;
  }
  .time {
    color: #888;
  }
  .total {
    font-weight: 500;
  }
</style>
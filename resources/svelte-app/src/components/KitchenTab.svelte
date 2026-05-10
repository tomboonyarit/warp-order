<script>
  let { orders, updateOrderStatus } = $props();

  let kitchenOrders = $derived(orders.filter(o => o.status === "pending" || o.status === "cooking"));
</script>

<section class="kitchen-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">Kitchen Display</p>
      <h2 class="section-title">คิวอาหารในครัว</h2>
      <p class="section-subtitle">แยกคิวรอทำและกำลังทำให้ทีมครัวมองเห็นทันที</p>
    </div>
    <div class="board-count">{kitchenOrders.length} รายการ</div>
  </div>

  {#if kitchenOrders.length === 0}
    <div class="empty-state">ไม่มีคิวอาหารค้างอยู่</div>
  {:else}
    <div class="kitchen-grid">
      {#each kitchenOrders as order (order.id)}
        <article class="ticket {order.status}">
          <div class="ticket-head">
            <div>
              <span>คิว</span>
              <strong>#{order.queue_number}</strong>
            </div>
            <span class="status {order.status}">{order.status === "pending" ? "รอเริ่ม" : "กำลังทำ"}</span>
          </div>

          {#if order.customer_name}
            <h3>{order.customer_name}</h3>
          {/if}

          {#if order.distinctive_notes}
            <p class="notes">{order.distinctive_notes}</p>
          {/if}

          <div class="items-list">
            {#each order.items as item (item.id)}
              <div>
                <span>{item.name}</span>
                <strong>x{item.quantity}</strong>
              </div>
            {/each}
          </div>

          {#if order.status === "pending"}
            <button class="secondary-action" onclick={() => updateOrderStatus(order.id, "cooking")}>เริ่มทำ</button>
          {:else}
            <button class="primary-action" onclick={() => updateOrderStatus(order.id, "completed")}>เสร็จแล้ว</button>
          {/if}
        </article>
      {/each}
    </div>
  {/if}
</section>

<style>
  .kitchen-board {
    padding: 24px;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
  }

  .board-count {
    border-radius: 999px;
    padding: 9px 13px;
    color: var(--blue);
    font-size: 13px;
    font-weight: 900;
    background: var(--blue-soft);
  }

  .kitchen-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
  }

  .ticket {
    display: grid;
    gap: 14px;
    border: 1px solid var(--line);
    border-radius: 26px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.68);
    box-shadow: 0 14px 34px rgba(45, 35, 26, 0.08);
  }

  .ticket.pending {
    border-left: 8px solid var(--brand);
  }

  .ticket.cooking {
    border-left: 8px solid var(--blue);
  }

  .ticket-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .ticket-head span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  .ticket-head strong {
    display: block;
    font-size: 38px;
    line-height: 0.95;
  }

  .status {
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 900;
  }

  .status.pending {
    color: var(--brand-dark);
    background: #fff1d2;
  }

  .status.cooking {
    color: var(--blue);
    background: var(--blue-soft);
  }

  h3 {
    margin: 0;
    font-size: 22px;
    letter-spacing: -0.03em;
  }

  .notes {
    margin: 0;
    border-radius: 16px;
    padding: 10px 12px;
    color: var(--muted);
    font-weight: 800;
    background: rgba(33, 27, 22, 0.06);
  }

  .items-list {
    display: grid;
    gap: 8px;
  }

  .items-list div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 16px;
    padding: 11px 12px;
    background: rgba(255, 255, 255, 0.7);
  }

  .items-list span {
    font-weight: 800;
  }

  @media (max-width: 640px) {
    .kitchen-board {
      padding: 16px;
      border-radius: 22px;
    }

    .section-head {
      flex-direction: column;
    }
  }
</style>

<script>
  import { t } from "../lib/i18n.svelte.js";
  import { formatThaiDateTime } from "../lib/dateUtils.js";

  let { orders } = $props();

  let today = new Date();
  let selectedDate = $state(`${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`);

  function toYYYYMMDash(str) {
    if (!str) return null;
    if (/^\d{4}-\d{2}-\d{2}$/.test(str)) return str;
    const months = { 'ม.ค.' : '01', 'ก.พ.' : '02', 'มี.ค.' : '03', 'เม.ย.' : '04', 'พ.ค.' : '05', 'มิ.ย.' : '06', 'ก.ค.' : '07', 'ส.ค.' : '08', 'ก.ย.' : '09', 'ต.ค.' : '10', 'พ.ย.' : '11', 'ธ.ค.' : '12' };
    const parts = str.split(' ');
    if (parts.length < 3) return null;
    const day = parts[0].padStart(2, '0');
    const month = months[parts[1]] || parts[1];
    const year = parseInt(parts[2]) - 543;
    return `${year}-${month}-${day}`;
  }

  let filteredOrders = $derived((() => {
    if (!selectedDate) return orders.filter(o => o.status === "completed");
    const ymd = toYYYYMMDash(selectedDate);
    if (!ymd) return orders.filter(o => o.status === "completed");
    return orders.filter(o => {
      if (o.status !== "completed") return false;
      if (!o.created_at) return false;
      return o.created_at.startsWith(ymd);
    });
  })());

  let completedOrders = $derived(filteredOrders);

  let totalRevenue = $derived(completedOrders.reduce((sum, o) => {
    const orderTotal = o.items.reduce((s, i) => s + (Number(i.price) * Number(i.quantity)), 0);
    return sum + orderTotal;
  }, 0));
  let averageOrder = $derived(completedOrders.length > 0 ? totalRevenue / completedOrders.length : 0);
</script>

<section class="report-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">{t("report.eyebrow")}</p>
      <h2 class="section-title">{t("report.title")}</h2>
      <p class="section-subtitle">{t("report.subtitle")}</p>
    </div>
    <div class="date-wrapper">
      <label for="report-date">{t("report.date_label")}</label>
      <input id="report-date" type="date" bind:value={selectedDate} />
    </div>
  </div>

  <div class="summary">
    <div class="summary-card hero">
      <span>{t("report.total_sales")}</span>
      <strong>{totalRevenue.toLocaleString()}฿</strong>
    </div>
    <div class="summary-card">
      <span>{t("report.order_count")}</span>
      <strong>{completedOrders.length}</strong>
    </div>
    <div class="summary-card">
      <span>{t("report.average")}</span>
      <strong>{averageOrder.toLocaleString(undefined, { maximumFractionDigits: 0 })}฿</strong>
    </div>
  </div>

  <div class="sales-list">
    <div class="list-head">
      <h3>{t("report.list_title")}</h3>
      <span class="count-badge">{t("report.list_count", { n: completedOrders.length })}</span>
    </div>

    {#if completedOrders.length === 0}
      <div class="empty-state">{t("report.list_empty")}</div>
    {:else}
      <div class="sales-grid">
        {#each completedOrders as order (order.id)}
          {@const orderTotal = order.items.reduce((sum, i) => sum + Number(i.price) * Number(i.quantity), 0)}
          {@const dt = formatThaiDateTime(order.created_at)}
          <div class="sales-card">
            <div class="sales-header">
              <div class="queue-info">
                <span class="queue-label">{t("report.list_queue")}</span>
                <strong>#{order.queue_number}</strong>
              </div>
              <div class="time-info">
                <span class="sales-time">{dt.time}</span>
                <span class="sales-date">{dt.date}</span>
              </div>
            </div>

            {#if order.customer_name}
              <h4>{order.customer_name}</h4>
            {:else}
              <h4 class="anon">{t("report.list_customer_fallback")}</h4>
            {/if}

            {#if order.distinctive_notes}
              <p class="notes">{order.distinctive_notes}</p>
            {/if}

            <div class="sales-items">
              {#each order.items as item (item.id)}
                <span class="item-tag">{item.name} x {item.quantity}</span>
              {/each}
            </div>

            <div class="sales-footer">
              <span class="sales-price">{orderTotal.toLocaleString()}฿</span>
            </div>
          </div>
        {/each}
      </div>
    {/if}
  </div>
</section>

<style>
  .report-board {
    padding: 24px;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
  }

  .date-wrapper {
    display: grid;
    gap: 6px;
    text-align: right;
  }

  .date-wrapper label {
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--muted);
  }

  input[type="date"] {
    border: 1px solid var(--line);
    border-radius: 14px;
    padding: 12px 14px;
    color: var(--ink);
    background: var(--surface-strong);
    font-size: 14px;
    font-weight: 700;
    min-width: 160px;
  }

  .summary {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 14px;
    margin-bottom: 24px;
  }

  .summary-card {
    border-radius: 20px;
    padding: 20px 22px;
    background: var(--surface-strong);
    border: 1px solid var(--line);
  }

  .summary-card.hero {
    background: linear-gradient(135deg, #ff8a8a, var(--brand));
    border-color: transparent;
  }

  .summary-card span {
    display: block;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .summary-card.hero span {
    color: rgba(255, 255, 255, 0.8);
  }

  .summary-card strong {
    display: block;
    margin-top: 8px;
    font-size: clamp(24px, 3vw, 38px);
    font-weight: 950;
    letter-spacing: -0.04em;
    color: var(--ink);
    line-height: 1;
  }

  .summary-card.hero strong {
    color: white;
  }

  .sales-list {
    border-radius: 24px;
    padding: 20px;
    background: var(--surface-strong);
    border: 1px solid var(--line);
  }

  .list-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 950;
    letter-spacing: -0.03em;
    color: var(--ink);
  }

  .count-badge {
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 900;
    background: var(--blue-soft);
    color: var(--blue);
  }

  .empty-state {
    padding: 40px;
    text-align: center;
    color: var(--muted);
    font-size: 14px;
    font-weight: 800;
  }

  .sales-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
  }

  .sales-card {
    border-radius: 20px;
    padding: 18px;
    background: var(--bg);
    border: 1px solid var(--line);
    display: grid;
    gap: 12px;
  }

  .sales-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
  }

  .queue-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .queue-label {
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .queue-info strong {
    font-size: 36px;
    font-weight: 950;
    letter-spacing: -0.04em;
    line-height: 1;
    color: var(--brand);
  }

  .time-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
  }

  .sales-time {
    font-size: 14px;
    font-weight: 800;
    color: var(--ink);
  }

  .sales-date {
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
  }

  h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -0.02em;
    color: var(--ink);
  }

  h4.anon {
    color: var(--muted-2);
    font-weight: 700;
  }

  .notes {
    margin: 0;
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 700;
    background: var(--purple-soft);
    color: var(--purple);
  }

  .sales-items {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .item-tag {
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 800;
    background: var(--blue-soft);
    color: var(--blue);
  }

  .sales-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-top: 8px;
    border-top: 1px solid var(--line);
  }

  .sales-price {
    font-size: 22px;
    font-weight: 950;
    letter-spacing: -0.03em;
    color: var(--green);
  }

  @media (max-width: 820px) {
    .section-head {
      flex-direction: column;
    }

    .date-wrapper {
      text-align: left;
      width: 100%;
    }

    input[type="date"] {
      width: 100%;
    }

    .summary {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 640px) {
    .report-board {
      padding: 16px;
      border-radius: 22px;
    }

    .sales-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
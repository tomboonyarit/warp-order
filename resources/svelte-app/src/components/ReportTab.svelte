<script>
  import { t } from "../lib/i18n.svelte.js";

  let { orders } = $props();

  let completedOrders = $derived(orders.filter(o => o.status === "completed"));
  let totalRevenue = $derived(completedOrders.reduce((sum, o) => {
    const orderTotal = o.items.reduce((s, i) => s + (Number(i.price) * Number(i.quantity)), 0);
    return sum + orderTotal;
  }, 0));
  let averageOrder = $derived(completedOrders.length > 0 ? totalRevenue / completedOrders.length : 0);

  let selectedDate = $state(new Date().toISOString().split("T")[0]);
</script>

<section class="report-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">{t("report.eyebrow")}</p>
      <h2 class="section-title">{t("report.title")}</h2>
      <p class="section-subtitle">{t("report.subtitle")}</p>
    </div>
    <label class="date-filter" for="report-date">
      {t("report.date_label")}
      <input id="report-date" type="date" bind:value={selectedDate} />
    </label>
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
      <span>{t("report.list_count", { n: completedOrders.length })}</span>
    </div>

    {#if completedOrders.length === 0}
      <div class="empty-state">{t("report.list_empty")}</div>
    {:else}
      {#each completedOrders as order (order.id)}
        <div class="sales-item">
          <div>
            <strong>{t("report.list_queue")} #{order.queue_number}</strong>
            <span>{order.customer_name || t("report.list_customer_fallback")}</span>
          </div>
          <span class="items-count">{t("report.list_count", { n: order.items.length })}</span>
          <strong class="total">{order.items.reduce((s, i) => s + (Number(i.price) * Number(i.quantity)), 0).toLocaleString()}฿</strong>
        </div>
      {/each}
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
    margin-bottom: 18px;
  }

  .date-filter {
    display: grid;
    gap: 6px;
    min-width: 180px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input {
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 11px 13px;
    color: var(--ink);
    background: rgba(255, 255, 255, 0.78);
  }

  .summary {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr;
    gap: 14px;
    margin-bottom: 16px;
  }

  .summary-card {
    border: 1px solid var(--line);
    border-radius: 24px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.62);
  }

  .summary-card.hero {
    color: #221707;
    background: linear-gradient(135deg, #ffe0a1, var(--brand));
  }

  .summary-card span,
  .summary-card strong {
    display: block;
  }

  .summary-card span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  .summary-card.hero span {
    color: rgba(34, 23, 7, 0.66);
  }

  .summary-card strong {
    margin-top: 10px;
    font-size: clamp(28px, 4vw, 46px);
    line-height: 1;
    letter-spacing: -0.05em;
  }

  .sales-list {
    border: 1px solid var(--line);
    border-radius: 24px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.58);
  }

  .list-head,
  .sales-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  h3 {
    margin: 0;
    font-size: 22px;
    letter-spacing: -0.03em;
  }

  .list-head {
    margin-bottom: 12px;
  }

  .list-head span,
  .items-count {
    border-radius: 999px;
    padding: 7px 10px;
    color: var(--blue);
    font-size: 12px;
    font-weight: 900;
    background: var(--blue-soft);
  }

  .sales-item {
    border-top: 1px solid var(--line);
    padding: 14px 0;
  }

  .sales-item strong,
  .sales-item span {
    display: block;
  }

  .sales-item span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .total {
    font-size: 20px;
  }

  @media (max-width: 820px) {
    .section-head,
    .list-head,
    .sales-item {
      align-items: flex-start;
      flex-direction: column;
    }

    .summary {
      grid-template-columns: 1fr;
    }

    .date-filter {
      width: 100%;
    }
  }

  @media (max-width: 640px) {
    .report-board {
      padding: 16px;
      border-radius: 22px;
    }
  }
</style>

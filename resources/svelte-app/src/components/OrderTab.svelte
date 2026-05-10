<script>
  import { t } from "../lib/i18n.svelte.js";

  let {
    products,
    orders,
    orderItems,
    addToOrder,
    updateCartItem,
    removeFromOrder,
    clearOrder,
    submitOrder,
    updateOrderStatus,
    noteGroups = []
  } = $props();

  let customerName = $state("");
  let manualNotes = $state("");
  let selectedNotes = $state({});
  let remark = $state("");

  let pendingOrders = $derived(orders.filter(o => o.status === "pending"));
  let cartTotal = $derived(orderItems.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0));
  let selectedNoteText = $derived(buildDistinctiveNotes());

  function toggleNoteGroup(group) {
    if (selectedNotes[group.id]) {
      const nextNotes = { ...selectedNotes };
      delete nextNotes[group.id];
      selectedNotes = nextNotes;
      return;
    }

    selectedNotes = {
      ...selectedNotes,
      [group.id]: {
        group: group.name,
        option: ""
      }
    };
  }

  function selectNoteOption(group, option) {
    selectedNotes = {
      ...selectedNotes,
      [group.id]: {
        group: group.name,
        option: option.label
      }
    };
  }

  function buildDistinctiveNotes() {
    const quickNotes = Object.values(selectedNotes)
      .map(note => note.option ? `${note.group}: ${note.option}` : note.group)
      .filter(Boolean);

    if (manualNotes.trim()) {
      quickNotes.push(manualNotes.trim());
    }

    return quickNotes.join(", ");
  }

  function handleSubmit() {
    if (orderItems.length === 0) {
      alert(t("order.alert_empty_cart"));
      return;
    }

    submitOrder(customerName, selectedNoteText, remark);
    customerName = "";
    manualNotes = "";
    selectedNotes = {};
    remark = "";
  }

  function cancelOrder(orderId) {
    if (confirm(t("order.alert_confirm_cancel"))) {
      updateOrderStatus(orderId, "cancelled");
    }
  }
</script>

<div class="order-workspace">
  <section class="menu-panel panel">
    <div class="section-head">
      <div>
        <p class="eyebrow">{t("order.menu_eyebrow")}</p>
        <h2 class="section-title">{t("order.menu_title")}</h2>
        <p class="section-subtitle">{t("order.menu_subtitle")}</p>
      </div>
      <div class="menu-count">{t("order.menu_count", { n: products.length })}</div>
    </div>

    <div class="products-grid">
      {#each products as product (product.id)}
        <button class="product-card" onclick={() => addToOrder(product, 1)}>
          <span class="product-initial">{product.name?.slice(0, 1)}</span>
          <span class="product-name">{product.name}</span>
          <span class="product-meta">
            <span>{product.category || t("order.menu_category_fallback")}</span>
            <strong>{Number(product.price).toLocaleString()}฿</strong>
          </span>
        </button>
      {/each}
    </div>
  </section>

  <aside class="cart-panel panel">
    <div class="section-head compact">
      <div>
        <p class="eyebrow">{t("order.cart_eyebrow")}</p>
        <h2 class="section-title">{t("order.cart_title")}</h2>
      </div>
      <button class="ghost-action" onclick={clearOrder} disabled={orderItems.length === 0}>{t("order.cart_clear")}</button>
    </div>

    {#if orderItems.length === 0}
      <div class="empty-state">{t("order.cart_empty")}</div>
    {:else}
      <div class="cart-list">
        {#each orderItems as item (item.product_id)}
          <div class="cart-item">
            <div>
              <strong>{item.name}</strong>
              <span>{Number(item.price).toLocaleString()}฿ {t("order.cart_unit")}</span>
            </div>
            <div class="qty-control" aria-label={t("order.cart_aria_qty")}>
              <button onclick={() => updateCartItem(item.product_id, -1)}>-</button>
              <span>{item.quantity}</span>
              <button onclick={() => updateCartItem(item.product_id, 1)}>+</button>
            </div>
            <button class="remove-btn" aria-label={t("order.cart_aria_remove")} onclick={() => removeFromOrder(item.product_id)}>{t("order.cart_remove")}</button>
          </div>
        {/each}
      </div>
    {/if}

    <div class="customer-form">
      <label>
        {t("order.customer_label")}
        <input type="text" placeholder={t("order.customer_placeholder")} bind:value={customerName} />
      </label>
      <div class="quick-notes">
        <div class="quick-notes-head">
          <div>
            <span>{t("order.notes_title")}</span>
            <strong>{selectedNoteText || t("order.notes_not_selected")}</strong>
          </div>
        </div>

        <div class="note-groups" aria-label={t("order.notes_aria_groups")}>
          {#each noteGroups as group (group.id)}
            <button type="button" class:active={Boolean(selectedNotes[group.id])} onclick={() => toggleNoteGroup(group)}>
              {group.name}
            </button>
          {/each}
        </div>

        {#each noteGroups.filter(group => selectedNotes[group.id]) as group (group.id)}
          <div class="note-options">
            <p>{group.prompt || t("order.notes_prompt", { name: group.name })}</p>
            <div>
              {#each group.options as option (option.id)}
                <button type="button" class:active={selectedNotes[group.id]?.option === option.label} onclick={() => selectNoteOption(group, option)}>
                  {option.label}
                </button>
              {/each}
            </div>
          </div>
        {/each}
      </div>

      <label>
        {t("order.manual_notes_label")}
        <input type="text" placeholder={t("order.manual_notes_placeholder")} bind:value={manualNotes} />
      </label>
      <label>
        {t("order.remark_label")}
        <textarea placeholder={t("order.remark_placeholder")} bind:value={remark} rows="3"></textarea>
      </label>
    </div>

    <div class="checkout-bar">
      <div>
        <span>{t("order.cart_total")}</span>
        <strong>{cartTotal.toLocaleString()}฿</strong>
      </div>
      <button class="primary-action" onclick={handleSubmit} disabled={orderItems.length === 0}>{t("order.cart_submit")}</button>
    </div>
  </aside>

  <section class="queue-panel panel">
    <div class="section-head compact">
      <div>
        <p class="eyebrow">{t("order.queue_eyebrow")}</p>
        <h2 class="section-title">{t("order.queue_title")}</h2>
      </div>
      <div class="menu-count">{t("order.queue_count", { n: pendingOrders.length })}</div>
    </div>

    {#if pendingOrders.length === 0}
      <div class="empty-state">{t("order.queue_empty")}</div>
    {:else}
      <div class="queue-list">
        {#each pendingOrders as order (order.id)}
          <article class="queue-card">
            <div class="queue-topline">
              <strong>#{order.queue_number}</strong>
              <span>{t("order.queue_status_waiting")}</span>
            </div>
            {#if order.customer_name}
              <h3>{order.customer_name}</h3>
            {/if}
            {#if order.distinctive_notes}
              <p class="notes">{order.distinctive_notes}</p>
            {/if}
            <div class="order-items">
              {#each order.items as item (item.id)}
                <span>{item.name} x {item.quantity}</span>
              {/each}
            </div>
            <div class="order-actions">
              <button class="secondary-action" onclick={() => updateOrderStatus(order.id, "completed")}>{t("order.queue_complete")}</button>
              <button class="danger-action" onclick={() => cancelOrder(order.id)}>{t("order.queue_cancel")}</button>
            </div>
          </article>
        {/each}
      </div>
    {/if}
  </section>
</div>

<style>
  .order-workspace {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 390px;
    grid-template-areas:
      "menu cart"
      "queue cart";
    gap: 18px;
    align-items: start;
  }

  .menu-panel {
    grid-area: menu;
  }

  .menu-panel,
  .cart-panel,
  .queue-panel {
    padding: 22px;
  }

  .cart-panel {
    grid-area: cart;
    position: sticky;
    top: 22px;
  }

  .queue-panel {
    grid-area: queue;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
  }

  .section-head.compact {
    align-items: center;
  }

  .menu-count {
    flex: 0 0 auto;
    border-radius: 999px;
    padding: 9px 13px;
    color: var(--brand-dark);
    font-size: 13px;
    font-weight: 900;
    background: #fff1d2;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
  }

  .product-card {
    display: grid;
    min-height: 152px;
    border: 1px solid rgba(45, 35, 26, 0.1);
    border-radius: 22px;
    padding: 14px;
    color: var(--ink);
    text-align: left;
    background: rgba(255, 255, 255, 0.7);
    box-shadow: 0 10px 24px rgba(45, 35, 26, 0.06);
    transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
  }

  .product-card:hover {
    transform: translateY(-3px);
    border-color: rgba(242, 159, 5, 0.45);
    box-shadow: 0 18px 36px rgba(45, 35, 26, 0.1);
  }

  .product-initial {
    display: grid;
    width: 42px;
    height: 42px;
    place-items: center;
    border-radius: 15px;
    color: var(--brand-dark);
    font-weight: 950;
    background: #fff1d2;
  }

  .product-name {
    margin-top: 18px;
    font-size: 18px;
    font-weight: 900;
    line-height: 1.15;
  }

  .product-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: auto;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .product-meta strong {
    color: var(--ink);
    font-size: 17px;
  }

  .cart-list {
    display: grid;
    gap: 10px;
  }

  .cart-item {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto auto;
    gap: 10px;
    align-items: center;
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.62);
  }

  .cart-item strong,
  .cart-item span {
    display: block;
  }

  .cart-item strong {
    line-height: 1.2;
  }

  .cart-item span {
    margin-top: 3px;
    color: var(--muted);
    font-size: 12px;
  }

  .qty-control {
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    padding: 4px;
    background: rgba(33, 27, 22, 0.06);
  }

  .qty-control button {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    border: 0;
    border-radius: 999px;
    font-weight: 950;
    background: white;
  }

  .qty-control span {
    min-width: 20px;
    margin: 0;
    color: var(--ink);
    text-align: center;
    font-weight: 900;
  }

  .remove-btn {
    border: 0;
    color: var(--red);
    font-size: 12px;
    font-weight: 900;
    background: transparent;
  }

  .customer-form {
    display: grid;
    gap: 10px;
    margin-top: 16px;
  }

  .quick-notes {
    display: grid;
    gap: 10px;
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.56);
  }

  .quick-notes-head span,
  .quick-notes-head strong {
    display: block;
  }

  .quick-notes-head span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  .quick-notes-head strong {
    margin-top: 4px;
    color: var(--ink);
    font-size: 14px;
    line-height: 1.35;
  }

  .note-groups,
  .note-options div {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .note-groups button,
  .note-options button {
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: 8px 11px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
    background: rgba(255, 255, 255, 0.72);
  }

  .note-groups button.active,
  .note-options button.active {
    color: #221707;
    border-color: rgba(242, 159, 5, 0.55);
    background: #fff1d2;
  }

  .note-options {
    display: grid;
    gap: 7px;
    border-top: 1px solid var(--line);
    padding-top: 10px;
  }

  .note-options p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  label {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input,
  textarea {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 12px 14px;
    color: var(--ink);
    background: rgba(255, 255, 255, 0.78);
  }

  textarea {
    resize: vertical;
  }

  .checkout-bar {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 12px;
    align-items: center;
    margin-top: 16px;
    border-top: 1px solid var(--line);
    padding-top: 16px;
  }

  .checkout-bar span,
  .checkout-bar strong {
    display: block;
  }

  .checkout-bar span {
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  .checkout-bar strong {
    font-size: 30px;
    line-height: 1;
  }

  .queue-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 12px;
  }

  .queue-card {
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.64);
  }

  .queue-topline {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .queue-topline strong {
    font-size: 28px;
  }

  .queue-topline span {
    border-radius: 999px;
    padding: 6px 10px;
    color: var(--brand-dark);
    font-size: 12px;
    font-weight: 900;
    background: #fff1d2;
  }

  .queue-card h3 {
    margin: 10px 0 4px;
    font-size: 20px;
  }

  .notes {
    margin: 6px 0;
    border-radius: 14px;
    padding: 9px 10px;
    color: var(--muted);
    font-size: 13px;
    font-weight: 700;
    background: rgba(33, 27, 22, 0.06);
  }

  .order-items {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
  }

  .order-items span {
    border-radius: 999px;
    padding: 6px 9px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
    background: white;
  }

  .order-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 14px;
  }

  @media (max-width: 1180px) {
    .order-workspace {
      grid-template-columns: 1fr;
      grid-template-areas:
        "menu"
        "cart"
        "queue";
    }

    .cart-panel {
      position: static;
    }
  }

  @media (max-width: 640px) {
    .menu-panel,
    .cart-panel,
    .queue-panel {
      padding: 16px;
      border-radius: 22px;
    }

    .section-head,
    .section-head.compact {
      flex-direction: column;
      align-items: flex-start;
    }

    .products-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .product-card {
      min-height: 138px;
    }

    .cart-item {
      grid-template-columns: 1fr;
    }

    .checkout-bar {
      grid-template-columns: 1fr;
    }
  }
</style>

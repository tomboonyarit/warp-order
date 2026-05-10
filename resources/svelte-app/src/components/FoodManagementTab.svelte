<script>
  let { products, menuCategories = [], createProduct, updateProduct, archiveProduct } = $props();

  let name = $state("");
  let price = $state("");
  let category = $state("เมนูหลัก");
  let editingId = $state(null);
  let search = $state("");

  let filteredProducts = $derived(products.filter(product => {
    const keyword = search.trim().toLowerCase();

    if (!keyword) {
      return true;
    }

    return `${product.name} ${product.category || ""}`.toLowerCase().includes(keyword);
  }));

  let categories = $derived(menuCategories.length > 0
    ? menuCategories.map(item => item.name)
    : [...new Set(products.map(product => product.category || "เมนูหลัก"))]
  );
  let menuValue = $derived(products.reduce((sum, product) => sum + Number(product.price), 0));

  function resetForm() {
    name = "";
    price = "";
    category = "เมนูหลัก";
    editingId = null;
  }

  function editProduct(product) {
    name = product.name;
    price = String(product.price);
    category = product.category || "เมนูหลัก";
    editingId = product.id;
  }

  async function saveProduct() {
    const payload = {
      name: name.trim(),
      price: Number(price),
      category: category.trim() || "เมนูหลัก"
    };

    if (!payload.name || Number.isNaN(payload.price) || payload.price < 0) {
      alert("กรุณากรอกชื่อเมนูและราคาให้ถูกต้อง");
      return;
    }

    if (editingId) {
      await updateProduct(editingId, payload);
    } else {
      await createProduct(payload);
    }

    resetForm();
  }

  async function confirmArchive(product) {
    if (confirm(`ปิดใช้งานเมนู ${product.name} หรือไม่?`)) {
      await archiveProduct(product.id);
    }
  }
</script>

<section class="food-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">Admin Menu Studio</p>
      <h2 class="section-title">จัดการเมนูอาหาร</h2>
      <p class="section-subtitle">เพิ่ม แก้ไขราคา จัดหมวดหมู่ และปิดใช้งานเมนูที่ไม่ขายแล้ว</p>
    </div>
    <div class="menu-stats">
      <div><strong>{products.length}</strong><span>เมนูใช้งาน</span></div>
      <div><strong>{categories.length}</strong><span>หมวดหมู่</span></div>
      <div><strong>{menuValue.toLocaleString()}</strong><span>มูลค่ารวม</span></div>
    </div>
  </div>

  <div class="food-layout">
    <form class="editor-card" onsubmit={(event) => { event.preventDefault(); saveProduct(); }}>
      <div>
        <p class="eyebrow">{editingId ? "Edit Item" : "New Item"}</p>
        <h3>{editingId ? "แก้ไขเมนู" : "เพิ่มเมนูใหม่"}</h3>
      </div>

      <label>
        ชื่อเมนู
        <input type="text" placeholder="เช่น ข้าวมันไก่พิเศษ" bind:value={name} />
      </label>

      <label>
        ราคา
        <input type="number" min="0" step="1" placeholder="65" bind:value={price} />
      </label>

      <label>
        หมวดหมู่
        <input type="text" placeholder="อาหารจานเดียว" bind:value={category} list="category-options" />
      </label>

      <datalist id="category-options">
        {#each categories as itemCategory (itemCategory)}
          <option value={itemCategory}></option>
        {/each}
      </datalist>

      <div class="form-actions">
        <button class="primary-action" type="submit">{editingId ? "บันทึกการแก้ไข" : "เพิ่มเมนู"}</button>
        {#if editingId}
          <button class="ghost-action" type="button" onclick={resetForm}>ยกเลิก</button>
        {/if}
      </div>
    </form>

    <div class="menu-table-card">
      <div class="table-toolbar">
        <div>
          <p class="eyebrow">Menu Inventory</p>
          <h3>รายการเมนูทั้งหมด</h3>
        </div>
        <input class="search" type="search" placeholder="ค้นหาเมนูหรือหมวดหมู่" bind:value={search} />
      </div>

      {#if filteredProducts.length === 0}
        <div class="empty-state">ไม่พบเมนูที่ตรงกับคำค้นหา</div>
      {:else}
        <div class="menu-list">
          {#each filteredProducts as product (product.id)}
            <article class="menu-row">
              <div class="food-token">{product.name?.slice(0, 1)}</div>
              <div class="menu-main">
                <strong>{product.name}</strong>
                <span>{product.category || "เมนูหลัก"}</span>
              </div>
              <div class="price-pill">{Number(product.price).toLocaleString()}฿</div>
              <div class="row-actions">
                <button class="secondary-action" onclick={() => editProduct(product)}>แก้ไข</button>
                <button class="danger-action" onclick={() => confirmArchive(product)}>ปิดใช้งาน</button>
              </div>
            </article>
          {/each}
        </div>
      {/if}
    </div>
  </div>
</section>

<style>
  .food-board {
    padding: 24px;
  }

  .section-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
  }

  .menu-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(94px, 1fr));
    gap: 8px;
  }

  .menu-stats div {
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 13px;
    background: rgba(255, 255, 255, 0.58);
  }

  .menu-stats strong,
  .menu-stats span {
    display: block;
  }

  .menu-stats strong {
    font-size: 28px;
    line-height: 1;
  }

  .menu-stats span {
    margin-top: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .food-layout {
    display: grid;
    grid-template-columns: 360px minmax(0, 1fr);
    gap: 16px;
    align-items: start;
  }

  .editor-card,
  .menu-table-card {
    border: 1px solid var(--line);
    border-radius: 26px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.62);
  }

  .editor-card {
    display: grid;
    gap: 12px;
    position: sticky;
    top: 22px;
  }

  h3 {
    margin: 0;
    color: var(--ink);
    font-size: 24px;
    letter-spacing: -0.04em;
  }

  label {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 12px 14px;
    color: var(--ink);
    background: rgba(255, 255, 255, 0.78);
  }

  .form-actions {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: center;
  }

  .table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 14px;
  }

  .search {
    max-width: 280px;
  }

  .menu-list {
    display: grid;
    gap: 10px;
  }

  .menu-row {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto auto;
    gap: 12px;
    align-items: center;
    border: 1px solid var(--line);
    border-radius: 22px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.66);
  }

  .food-token {
    display: grid;
    width: 46px;
    height: 46px;
    place-items: center;
    border-radius: 16px;
    color: var(--brand-dark);
    font-weight: 950;
    background: #fff1d2;
  }

  .menu-main strong,
  .menu-main span {
    display: block;
  }

  .menu-main strong {
    line-height: 1.2;
  }

  .menu-main span {
    margin-top: 3px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .price-pill {
    border-radius: 999px;
    padding: 9px 12px;
    color: #221707;
    font-weight: 950;
    background: linear-gradient(135deg, #ffdf98, var(--brand));
  }

  .row-actions {
    display: flex;
    gap: 8px;
  }

  @media (max-width: 1120px) {
    .food-layout {
      grid-template-columns: 1fr;
    }

    .editor-card {
      position: static;
    }
  }

  @media (max-width: 740px) {
    .food-board {
      padding: 16px;
      border-radius: 22px;
    }

    .section-head,
    .table-toolbar {
      flex-direction: column;
      align-items: flex-start;
    }

    .menu-stats {
      width: 100%;
      grid-template-columns: 1fr;
    }

    .search {
      max-width: none;
    }

    .menu-row {
      grid-template-columns: auto minmax(0, 1fr);
    }

    .price-pill,
    .row-actions {
      grid-column: 1 / -1;
    }

    .row-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
  }
</style>

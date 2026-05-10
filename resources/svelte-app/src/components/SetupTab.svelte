<script>
  import { t } from "../lib/i18n.svelte.js";

  let {
    setupData,
    createMenuCategory,
    updateMenuCategory,
    archiveMenuCategory,
    createNoteGroup,
    updateNoteGroup,
    archiveNoteGroup,
    createNoteOption,
    updateNoteOption,
    archiveNoteOption
  } = $props();

  let categoryName = $state("");
  let categoryOrder = $state("0");
  let editingCategoryId = $state(null);

  let groupName = $state("");
  let groupPrompt = $state("");
  let groupOrder = $state("0");
  let editingGroupId = $state(null);

  let optionGroupId = $state("");
  let optionLabel = $state("");
  let optionOrder = $state("0");
  let editingOptionId = $state(null);

  let menuCategories = $derived(setupData.menu_categories ?? []);
  let noteGroups = $derived(setupData.note_groups ?? []);

  function resetCategoryForm() {
    categoryName = "";
    categoryOrder = "0";
    editingCategoryId = null;
  }

  function editCategory(category) {
    categoryName = category.name;
    categoryOrder = String(category.sort_order ?? 0);
    editingCategoryId = category.id;
  }

  async function saveCategory() {
    const payload = { name: categoryName.trim(), sort_order: Number(categoryOrder) || 0 };

    if (!payload.name) {
      alert(t("setup.alert_empty_category"));
      return;
    }

    if (editingCategoryId) {
      await updateMenuCategory(editingCategoryId, payload);
    } else {
      await createMenuCategory(payload);
    }

    resetCategoryForm();
  }

  function resetGroupForm() {
    groupName = "";
    groupPrompt = "";
    groupOrder = "0";
    editingGroupId = null;
  }

  function editGroup(group) {
    groupName = group.name;
    groupPrompt = group.prompt ?? "";
    groupOrder = String(group.sort_order ?? 0);
    editingGroupId = group.id;
  }

  async function saveGroup() {
    const payload = { name: groupName.trim(), prompt: groupPrompt.trim(), sort_order: Number(groupOrder) || 0 };

    if (!payload.name) {
      alert(t("setup.alert_empty_group"));
      return;
    }

    if (editingGroupId) {
      await updateNoteGroup(editingGroupId, payload);
    } else {
      await createNoteGroup(payload);
    }

    resetGroupForm();
  }

  function resetOptionForm() {
    optionGroupId = "";
    optionLabel = "";
    optionOrder = "0";
    editingOptionId = null;
  }

  function editOption(group, option) {
    optionGroupId = String(group.id);
    optionLabel = option.label;
    optionOrder = String(option.sort_order ?? 0);
    editingOptionId = option.id;
  }

  async function saveOption() {
    const payload = {
      group_id: Number(optionGroupId),
      label: optionLabel.trim(),
      sort_order: Number(optionOrder) || 0
    };

    if (!payload.group_id || !payload.label) {
      alert(t("setup.alert_empty_option"));
      return;
    }

    if (editingOptionId) {
      await updateNoteOption(editingOptionId, payload);
    } else {
      await createNoteOption(payload);
    }

    resetOptionForm();
  }
</script>

<section class="setup-board panel">
  <div class="section-head">
    <div>
      <p class="eyebrow">{t("setup.eyebrow")}</p>
      <h2 class="section-title">{t("setup.title")}</h2>
      <p class="section-subtitle">{t("setup.subtitle")}</p>
    </div>
    <div class="setup-stats">
      <div><strong>{menuCategories.length}</strong><span>{t("setup.stat_categories")}</span></div>
      <div><strong>{noteGroups.length}</strong><span>{t("setup.stat_note_types")}</span></div>
    </div>
  </div>

  <div class="setup-grid">
    <form class="setup-card" onsubmit={(event) => { event.preventDefault(); saveCategory(); }}>
      <div>
        <p class="eyebrow">{t("setup.cat_eyebrow")}</p>
        <h3>{editingCategoryId ? t("setup.cat_title_edit") : t("setup.cat_title_add")}</h3>
      </div>
      <label>
        {t("setup.cat_name_label")}
        <input type="text" placeholder={t("setup.cat_name_placeholder")} bind:value={categoryName} />
      </label>
      <label>
        {t("setup.cat_sort_label")}
        <input type="number" min="0" step="1" bind:value={categoryOrder} />
      </label>
      <div class="form-actions">
        <button class="primary-action" type="submit">{t("setup.cat_save")}</button>
        {#if editingCategoryId}
          <button class="ghost-action" type="button" onclick={resetCategoryForm}>{t("setup.cat_cancel")}</button>
        {/if}
      </div>

      <div class="list-stack">
        {#each menuCategories as category (category.id)}
          <article class="setup-row">
            <div><strong>{category.name}</strong><span>{t("setup.cat_order", { n: category.sort_order })}</span></div>
            <div class="row-actions">
              <button class="secondary-action" type="button" onclick={() => editCategory(category)}>{t("setup.cat_edit")}</button>
              <button class="danger-action" type="button" onclick={() => archiveMenuCategory(category.id)}>{t("setup.cat_close")}</button>
            </div>
          </article>
        {/each}
      </div>
    </form>

    <form class="setup-card" onsubmit={(event) => { event.preventDefault(); saveGroup(); }}>
      <div>
        <p class="eyebrow">{t("setup.group_eyebrow")}</p>
        <h3>{editingGroupId ? t("setup.group_title_edit") : t("setup.group_title_add")}</h3>
      </div>
      <label>
        {t("setup.group_name_label")}
        <input type="text" placeholder={t("setup.group_name_placeholder")} bind:value={groupName} />
      </label>
      <label>
        {t("setup.group_prompt_label")}
        <input type="text" placeholder={t("setup.group_prompt_placeholder")} bind:value={groupPrompt} />
      </label>
      <label>
        {t("setup.group_sort_label")}
        <input type="number" min="0" step="1" bind:value={groupOrder} />
      </label>
      <div class="form-actions">
        <button class="primary-action" type="submit">{t("setup.group_save")}</button>
        {#if editingGroupId}
          <button class="ghost-action" type="button" onclick={resetGroupForm}>{t("setup.group_cancel")}</button>
        {/if}
      </div>
    </form>

    <form class="setup-card" onsubmit={(event) => { event.preventDefault(); saveOption(); }}>
      <div>
        <p class="eyebrow">{t("setup.opt_eyebrow")}</p>
        <h3>{editingOptionId ? t("setup.opt_title_edit") : t("setup.opt_title_add")}</h3>
      </div>
      <label>
        {t("setup.opt_group_label")}
        <select bind:value={optionGroupId}>
          <option value="">{t("setup.opt_group_default")}</option>
          {#each noteGroups as group (group.id)}
            <option value={group.id}>{group.name}</option>
          {/each}
        </select>
      </label>
      <label>
        {t("setup.opt_option_label")}
        <input type="text" placeholder={t("setup.opt_option_placeholder")} bind:value={optionLabel} />
      </label>
      <label>
        {t("setup.opt_sort_label")}
        <input type="number" min="0" step="1" bind:value={optionOrder} />
      </label>
      <div class="form-actions">
        <button class="primary-action" type="submit">{t("setup.opt_save")}</button>
        {#if editingOptionId}
          <button class="ghost-action" type="button" onclick={resetOptionForm}>{t("setup.opt_cancel")}</button>
        {/if}
      </div>
    </form>
  </div>

  <div class="option-board">
    {#each noteGroups as group (group.id)}
      <article class="group-card">
        <div class="group-head">
          <div><strong>{group.name}</strong><span>{group.prompt}</span></div>
          <div class="row-actions">
            <button class="secondary-action" type="button" onclick={() => editGroup(group)}>{t("setup.group_edit")}</button>
            <button class="danger-action" type="button" onclick={() => archiveNoteGroup(group.id)}>{t("setup.group_close")}</button>
          </div>
        </div>
        <div class="option-list">
          {#each group.options as option (option.id)}
            <span>
              {option.label}
              <button type="button" onclick={() => editOption(group, option)}>{t("setup.opt_edit")}</button>
              <button type="button" onclick={() => archiveNoteOption(option.id)}>{t("setup.opt_close")}</button>
            </span>
          {/each}
        </div>
      </article>
    {/each}
  </div>
</section>

<style>
  .setup-board {
    padding: 24px;
  }

  .section-head,
  .setup-stats,
  .group-head,
  .setup-row,
  .row-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .section-head {
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 18px;
  }

  .setup-stats div {
    min-width: 132px;
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 13px;
    background: rgba(255, 255, 255, 0.58);
  }

  .setup-stats strong,
  .setup-stats span,
  .setup-row strong,
  .setup-row span,
  .group-head strong,
  .group-head span {
    display: block;
  }

  .setup-stats strong {
    font-size: 30px;
    line-height: 1;
  }

  .setup-stats span,
  .setup-row span,
  .group-head span {
    margin-top: 4px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 800;
  }

  .setup-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    align-items: start;
  }

  .setup-card,
  .group-card {
    border: 1px solid var(--line);
    border-radius: 24px;
    padding: 18px;
    background: rgba(255, 255, 255, 0.62);
  }

  .setup-card {
    display: grid;
    gap: 12px;
  }

  h3 {
    margin: 0;
    color: var(--ink);
    font-size: 22px;
    letter-spacing: -0.03em;
  }

  label {
    display: grid;
    gap: 6px;
    color: var(--muted);
    font-size: 12px;
    font-weight: 900;
  }

  input,
  select {
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
  }

  .list-stack,
  .option-board {
    display: grid;
    gap: 10px;
  }

  .setup-row,
  .group-head {
    justify-content: space-between;
  }

  .setup-row {
    border-top: 1px solid var(--line);
    padding-top: 12px;
  }

  .option-board {
    margin-top: 14px;
  }

  .option-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 14px;
  }

  .option-list span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 7px 8px 7px 11px;
    color: var(--ink);
    font-size: 12px;
    font-weight: 900;
    background: #fff1d2;
  }

  .option-list button {
    border: 0;
    border-radius: 999px;
    padding: 4px 7px;
    color: var(--muted);
    font-size: 11px;
    font-weight: 900;
    background: rgba(255, 255, 255, 0.72);
  }

  @media (max-width: 1120px) {
    .setup-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 740px) {
    .setup-board {
      padding: 16px;
      border-radius: 22px;
    }

    .section-head,
    .setup-stats,
    .setup-row,
    .group-head {
      align-items: flex-start;
      flex-direction: column;
    }

    .setup-stats,
    .setup-stats div {
      width: 100%;
    }

    .row-actions {
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
  }
</style>

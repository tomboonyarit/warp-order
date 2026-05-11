<script>
	import { onMount } from "svelte";
	import { t } from "../lib/i18n.svelte.js";

	let orders = $state([]);
	let loading = $state(true);
	let error = $state(null);

	async function loadHistory() {
		try {
			loading = true;
			error = null;
			const res = await fetch("/api/orders?status=completed");
			if (!res.ok) throw new Error(`HTTP ${res.status}`);
			const data = await res.json();
			
			// Process orders to add calculated totals and formatted items
			orders = data.map(order => {
				// Calculate total amount
				const total = order.items.reduce((sum, item) => {
					return sum + (item.price * item.quantity);
				}, 0);
				
				// Format items list for display
				const itemsText = order.items.map(item => 
					`${item.name} × ${item.quantity}`
				).join(", ");
				
				// Combine notes
				const notes = [
					order.distinctive_notes,
					order.remark
				].filter(note => note && note.trim() !== "").join(" ");
				
				return {
					...order,
					total,
					itemsText,
					notes: notes || "-"
				};
			});
		} catch (err) {
			error = err.message;
			console.error("Failed to load history:", err);
		} finally {
			loading = false;
		}
	}

	onMount(() => {
		loadHistory();
	});
</script>

<section class="history-tab">
	<header class="history-header">
		<h1>{t("tab.history_title")}</h1>
		{#if loading}
			<p class="loading">{t("app.loading")}</p>
		{:else if error}
			<p class="error">{t("app.error")}: {error}</p>
		{:else if orders.length === 0}
			<p class="empty">{t("app.no_history")}</p>
		{/if}
	</header>

	{#if !loading && orders.length > 0}
	<div class="history-grid">
		{#each orders as order}
		<div class="history-card">
			<div class="history-header">
				<h2>{t("app.order")} #{order.queue_number}</h2>
				<span class="status completed">{t("app.status_completed")}</span>
			</div>
			
			<div class="history-body">
				<div class="history-row">
					<span class="label">{t("app.customer")}:</span>
					<span class="value">{order.customer_name || "-"} </span>
				</div>
				
				<div class="history-row">
					<span class="label">{t("app.items")}:</span>
					<span class="value">{order.itemsText}</span>
				</div>
				
				<div class="history-row">
					<span class="label">{t("app.total")}:</span>
					<span class="value">{order.total.toLocaleString()} {t("app.currency")}</span>
				</div>
				
				<div class="history-row">
					<span class="label">{t("app.notes")}:</span>
					<span class="value notes">{order.notes}</span>
				</div>
			</div>
		</div>
		{/each}
	</div>
	{/if}
</section>

<style>
	.history-tab {
		padding: 20px;
	}
	
	.history-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 24px;
		flex-wrap: wrap;
		gap: 12px;
	}
	
	.history-header h1 {
		margin: 0;
		font-size: 1.5rem;
	}
	
	.loading, .error, .empty {
		text-align: center;
		padding: 40px 20px;
		color: var(--text-muted);
	}
	
	.error {
		color: var(--error);
	}
	
	.history-grid {
		display: grid;
		gap: 16px;
	}
	
	@media (min-width: 640px) {
		.history-grid {
			grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		}
	}
	
	.history-card {
		background: var(--background);
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		border: 1px solid var(--border);
	}
	
	.history-header {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		margin-bottom: 16px;
		flex-wrap: wrap;
		gap: 8px;
	}
	
	.history-header h2 {
		margin: 0 0 8px 0;
		font-size: 1.25rem;
	}
	
	.status {
		font-size: 0.875rem;
		font-weight: 600;
		padding: 2px 8px;
		border-radius: 4px;
	}
	
	.status.completed {
		background: var(--success-light);
		color: var(--success);
		border: 1px solid var(--success);
	}
	
	.history-body {
		display: grid;
		gap: 12px;
	}
	
	.history-row {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		align-items: baseline;
	}
	
	.label {
		font-weight: 600;
		min-width: 80px;
		color: var(--text-muted);
	}
	
	.value {
		flex: 1;
		min-width: 120px;
		word-break: break-word;
	}
	
	.value.notes {
		font-style: italic;
		color: var(--text);
	}
	
	/* Dark mode adjustments */
	.dark .history-card {
		background: var(--background-dark);
		border-color: var(--border-dark);
	}
	
	.dark .label {
		color: var(--text-muted-dark);
	}
	
	.dark .value.notes {
		color: var(--text-muted-dark);
	}
</style>
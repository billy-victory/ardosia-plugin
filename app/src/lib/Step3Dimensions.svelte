<script>
	// @ts-nocheck

	let {
		availableSizeDetails,
		selectedSizeDetail,
		pavingTypeDetails,
		sizeOptionDetails,
		length,
		width,
		area,
		isStep3NextDisabled,
		onUpdateLength,
		onUpdateWidth,
		onSelectSizeDetail,
		onNext,
		onBack,
	} = $props();

	// Automatically select the size detail if only one is available
	$effect(() => {
		if (
			availableSizeDetails &&
			availableSizeDetails.length === 1 &&
			selectedSizeDetail === null
		) {
			onSelectSizeDetail(0);
		}
	});
</script>

<div class="apc-step-container" id="step-3">
	<div class="apc-summary-box" id="apc-summary-step-3">
		<div class="apc-summary-title">Your Selections</div>
		<div class="apc-summary-content">
			<div class="apc-summary-item">
				<span>Paving Type:</span>
				<span class="apc-summary-value"
					>{pavingTypeDetails?.name ?? "No selection"}</span
				>
			</div>
			<div class="apc-summary-item">
				<span>Size Option:</span>
				<span class="apc-summary-value"
					>{sizeOptionDetails?.name ?? "No selection"}</span
				>
			</div>
			<div class="apc-summary-item">
				<span>Size Detail:</span>
				<span class="apc-summary-value">
					{#if selectedSizeDetail !== null && availableSizeDetails[selectedSizeDetail]}
						{availableSizeDetails[selectedSizeDetail].size} (£{availableSizeDetails[
							selectedSizeDetail
						].price.toFixed(2)} per m²)
					{:else}
						No selection
					{/if}
				</span>
			</div>
			<div class="apc-summary-item">
				<span>Dimensions:</span>
				<span id="apc-dimensions-summary" class="apc-summary-value"
					>{area > 0 ? `${area.toFixed(2)} m²` : "Not specified"}</span
				>
			</div>
		</div>
	</div>

	<div class="apc-step-header">
		<h2 class="apc-step-title">Enter Dimensions</h2>
		<p class="apc-step-description">Specify size details and dimensions</p>
	</div>

	<div class="apc-options-grid apc-size-details-grid" id="apc-size-details">
		{#each availableSizeDetails as detail, index (index)}
			<button
				type="button"
				class="apc-option-card apc-size-detail-card"
				class:apc-option-selected={index === selectedSizeDetail}
				onclick={() => onSelectSizeDetail(index)}
			>
				<div>
					<div class="apc-option-name">{detail.size}</div>
					<div class="apc-option-description">
						£{detail.price.toFixed(2)} per m²
					</div>
				</div>
			</button>
		{:else}
			<p class="apc-no-options-message">Please select a size option first.</p>
		{/each}
	</div>

	<div class="apc-dimensions-input-section">
		<div class="apc-dimensions-grid">
			<div class="apc-input-group">
				<label for="length" class="apc-input-label">Length (m)</label>
				<input
					type="number"
					id="length"
					class="apc-input-field"
					placeholder="Enter length in meters"
					min="0"
					step="0.1"
					value={length}
					oninput={(event) => onUpdateLength(event.target.valueAsNumber || 0)}
				/>
			</div>

			<div class="apc-input-group">
				<label for="width" class="apc-input-label">Width (m)</label>
				<input
					type="number"
					id="width"
					class="apc-input-field"
					placeholder="Enter width in meters"
					min="0"
					step="0.1"
					value={width}
					oninput={(event) => onUpdateWidth(event.target.valueAsNumber || 0)}
				/>
			</div>

			<div class="apc-area-display-group">
				<span class="apc-input-label">Total Area</span>
				<div class="apc-area-display" id="apc-area-display">
					{area.toFixed(2)} m²
				</div>
			</div>
		</div>
	</div>

	<div class="apc-step-navigation">
		<div>
			<button class="apc-button apc-button-secondary" onclick={onBack}
				>Back</button
			>
		</div>
		<div>
			<button
				class="apc-button apc-button-primary"
				onclick={onNext}
				disabled={isStep3NextDisabled}>Next</button
			>
		</div>
	</div>
</div>

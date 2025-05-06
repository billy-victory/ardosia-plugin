<script>
	let {
		availableSizeOptions,
		selectedSizeOption,
		sizeOptionDetails,
		pavingTypeDetails,
		isStep2NextDisabled,
		onSelectSizeOption,
		onNext,
		onBack,
	} = $props();

	$effect(() => {
		if (
			availableSizeOptions &&
			availableSizeOptions.length === 1 &&
			selectedSizeOption === null
		) {
			onSelectSizeOption(availableSizeOptions[0].id);
		}
	});
</script>

<div class="apc-step-container" id="step-2">
	<div class="apc-summary-box" id="apc-summary-step-2">
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
				<span id="apc-size-selection-summary" class="apc-summary-value"
					>{sizeOptionDetails?.name ?? "No selection"}</span
				>
			</div>
		</div>
	</div>

	<div class="apc-step-header">
		<h2 class="apc-step-title">Select Size Option</h2>
		<p class="apc-step-description">Choose your preferred size configuration</p>
	</div>

	<div class="apc-options-grid apc-size-options-grid" id="apc-size-options">
		{#each availableSizeOptions as option (option.id)}
			<button
				type="button"
				class="apc-option-card apc-size-option-card"
				class:apc-option-selected={option.id === selectedSizeOption}
				onclick={() => onSelectSizeOption(option.id)}
			>
				<div>
					<div class="apc-option-name">{option.name}</div>
					<div class="apc-option-description">
						{option.description}
					</div>
				</div>
			</button>
		{:else}
			<p class="apc-no-options-message">Please select a paving type first.</p>
		{/each}
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
				disabled={isStep2NextDisabled}>Next</button
			>
		</div>
	</div>
</div>

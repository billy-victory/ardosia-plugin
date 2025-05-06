<script>
	let {
		pavingTypes,
		selectedPavingType,
		pavingTypeDetails,
		isStep1NextDisabled,
		onSelectPavingType,
		onNext,
	} = $props();
</script>

<div class="apc-step-container" id="step-1">
	<div class="apc-summary-box" id="apc-summary-step-1">
		<div class="apc-summary-title">Your Selection</div>
		<div id="apc-paving-selection-summary" class="apc-summary-content">
			{pavingTypeDetails?.name ?? "No selection made"}
		</div>
	</div>

	<div class="apc-step-header">
		<h2 class="apc-step-title">Select Paving Type</h2>
		<p class="apc-step-description">Choose the type of paving you require</p>
	</div>

	<div class="apc-options-grid apc-paving-types-grid" id="apc-paving-types">
		{#each pavingTypes as type (type.id)}
			<button
				type="button"
				class="apc-option-card apc-paving-type-card"
				class:apc-option-selected={type.id === selectedPavingType}
				onclick={() => onSelectPavingType(type.id)}
			>
				<img
					src={type.image}
					alt={type.name}
					class="apc-option-image"
					loading="lazy"
				/>
				<div>
					<div class="apc-option-name">{type.name}</div>
					<div class="apc-option-description">{type.description}</div>
				</div>
			</button>
		{/each}
	</div>

	<div class="apc-step-navigation">
		<div>
			<!-- No back button on step 1 -->
		</div>
		<div>
			<button
				class="apc-button apc-button-primary"
				onclick={onNext}
				disabled={isStep1NextDisabled}>Next</button
			>
		</div>
	</div>
</div>

<script>
	// @ts-nocheck

	let {
		quote,
		showEmailModal,
		recipientEmail,
		onBack,
		onOpenEmailModal,
		onCloseEmailModal,
		onSendEmail,
		onRestart,
		onUpdateEmail,
	} = $props();

	// Add state for postcode
	let postcode = $state("");

	function updatePostcode(value) {
		postcode = value;
	}

	// Update onSendEmail to include postcode
	function handleSendEmail() {
		onSendEmail(postcode);
	}

	console.log("Quote prop:", quote); // Log the quote prop whenever it changes
</script>

<div class="apc-step-container" id="step-4">
	{#if quote}
		<div class="apc-card apc-quote-summary-card">
			<div class="apc-card-header apc-quote-header">
				<h2 class="apc-quote-title">Your Paving Quote</h2>
				<p class="apc-quote-subtitle">Review your selections and total cost</p>
			</div>

			<div class="apc-card-body apc-quote-body">
				<div class="apc-quote-section">
					<h3 class="apc-quote-section-title">Project Details</h3>
					<div class="apc-quote-details" id="apc-quote-details">
						<div class="apc-quote-item">
							<span class="apc-quote-label">Paving Type</span>
							<span class="apc-quote-value">{quote.pavingType}</span>
						</div>
						<div class="apc-quote-item">
							<span class="apc-quote-label">Size Option</span>
							<span class="apc-quote-value">{quote.sizeOption}</span>
						</div>
						<div class="apc-quote-item">
							<span class="apc-quote-label">Size Details</span>
							<span class="apc-quote-value">{quote.sizeDetail}</span>
						</div>
						<div class="apc-quote-item">
							<span class="apc-quote-label">Area</span>
							<span class="apc-quote-value">
								{#if typeof quote.area === "number" && !isNaN(quote.area)}
									{quote.area.toFixed(2)} m²
								{:else}
									<span class="apc-error-text">Invalid Area</span>
								{/if}
							</span>
						</div>
					</div>
				</div>

				<div class="apc-quote-section">
					<h3 class="apc-quote-section-title">Cost Breakdown</h3>
					<div class="apc-quote-details" id="apc-cost-details">
						<div class="apc-quote-item">
							<span class="apc-quote-label">Price per m²</span>
							<span class="apc-quote-value">
								{#if typeof quote.pricePerSqm === "number" && !isNaN(quote.pricePerSqm)}
									£{quote.pricePerSqm.toFixed(2)}
								{:else}
									<span class="apc-error-text">Invalid Price</span>
								{/if}
							</span>
						</div>
						<div class="apc-quote-total">
							<span class="apc-quote-total-label">Total Cost</span>
							<span class="apc-quote-total-value">
								{#if typeof quote.totalCost === "number" && !isNaN(quote.totalCost)}
									£{quote.totalCost.toFixed(2)}
								{:else}
									<span class="apc-error-text">Calculation Error</span>
								{/if}
							</span>
						</div>
						<div class="apc-quote-vat-delivery">
							<span class="apc-quote-vat-delivery-label apc-disclaimer-text"
								>Disclaimer: Prices before VAT and delivery</span
							>
						</div>
					</div>
				</div>
			</div>

			<div class="apc-card-footer apc-quote-footer">
				<button class="apc-button apc-button-secondary" onclick={onBack}
					>Back</button
				>
				<div class="apc-quote-actions">
					<button
						class="apc-button apc-button-primary"
						onclick={onOpenEmailModal}>Email Quote</button
					>
					<a href="tel:01271831039" class="apc-button apc-button-primary"
						>Call Us Now</a
					>
					<button class="apc-button apc-button-danger" onclick={onRestart}
						>Start Over</button
					>
				</div>
			</div>
		</div>

		{#if showEmailModal}
			<div class="apc-modal-overlay">
				<div class="apc-modal-content">
					<h3 class="apc-modal-title">Email Your Quote</h3>
					<p class="apc-modal-description">
						Enter your email address below to receive a copy of your quote.
					</p>
					<div class="apc-modal-input-group">
						<label for="email" class="apc-input-label">Email Address</label>
						<input
							type="email"
							id="email"
							class="apc-input-field"
							placeholder="you@example.com"
							value={recipientEmail}
							oninput={(event) => onUpdateEmail(event.target.value)}
						/>
					</div>
					<div class="apc-modal-input-group">
						<label for="postcode" class="apc-input-label">Postcode</label>
						<input
							type="text"
							id="postcode"
							class="apc-input-field"
							placeholder="Enter your postcode"
							value={postcode}
							oninput={(event) => updatePostcode(event.target.value)}
						/>
					</div>
					<div class="apc-modal-actions">
						<button
							class="apc-button apc-button-secondary"
							onclick={onCloseEmailModal}>Cancel</button
						>
						<button
							class="apc-button apc-button-primary"
							onclick={handleSendEmail}>Send Email</button
						>
					</div>
				</div>
			</div>
		{/if}
	{:else}
		<div class="apc-quote-unavailable">
			<p class="apc-quote-unavailable-message">
				Quote details are not available. Please go back and complete the
				previous steps.
			</p>
			<div>
				<div>
					<button class="apc-button apc-button-secondary" onclick={onBack}
						>Back</button
					>
				</div>
			</div>
		</div>
	{/if}
</div>

<style>
	.apc-disclaimer-text {
		color: #94a3b8; /* Custom gray color */
		font-size: 0.9em; /* Slightly smaller text */
		font-style: italic; /* Italicize for disclaimer style */
		margin-top: 8px; /* Add some space above */
		display: block; /* Ensure it takes its own line if needed */
		text-align: left; /* Align to the left */
	}

	/* Add any other styles you need for this component below */
</style>

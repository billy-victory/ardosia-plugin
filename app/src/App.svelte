<script>
	// @ts-nocheck

	// Svelte 5 Runes logic will go here

	// Extend Window interface for TypeScript

	import { pavingTypes, sizeOptions, sizeDetails } from "./lib/data.js";

	// Import step components
	import Step1PavingType from "./lib/Step1PavingType.svelte";
	import Step2SizeOptions from "./lib/Step2SizeOptions.svelte";
	import Step3Dimensions from "./lib/Step3Dimensions.svelte";
	import Step4QuoteSummary from "./lib/Step4QuoteSummary.svelte";

	let currentStep = $state(1);
	let selectedPavingType = $state(null);
	let selectedSizeOption = $state(null);
	let selectedSizeDetail = $state(null); // Index of the detail
	let length = $state(0);
	let width = $state(0);
	let directArea = $state(0); // New state for direct area input
	let isDirectAreaMode = $state(false); // New state to track input mode

	let showEmailModal = $state(false);
	let recipientEmail = $state("");

	// Calculate area based on current input mode
	let area = $derived(isDirectAreaMode ? directArea : length * width);

	// Derived state for selected items details
	const pavingTypeDetails = $derived(
		pavingTypes.find((type) => type.id === selectedPavingType),
	);
	const availableSizeOptions = $derived(sizeOptions[selectedPavingType] || []);
	const sizeOptionDetails = $derived(
		availableSizeOptions.find((option) => option.id === selectedSizeOption),
	);
	const availableSizeDetails = $derived(
		sizeDetails[selectedPavingType]?.[selectedSizeOption] || [],
	);
	const sizeDetailData = $derived(availableSizeDetails[selectedSizeDetail]);

	// Function to calculate quote
	function calculateQuote() {
		const isStep4 = currentStep === 4;

		// 1. Check prerequisites: selections must be made
		if (!pavingTypeDetails || !sizeOptionDetails || !sizeDetailData) {
			if (isStep4) console.log("Quote calc: Prerequisites failed (selections)");
			return null;
		}

		// 2. Validate area: must be a positive number
		const currentArea = area; // Capture derived value
		if (
			typeof currentArea !== "number" ||
			isNaN(currentArea) ||
			currentArea <= 0
		) {
			if (isStep4) console.log("Quote calc: Invalid area", currentArea);
			return null;
		}

		// 3. Validate price: must be a number
		const pricePerSqm = sizeDetailData.price;
		if (typeof pricePerSqm !== "number" || isNaN(pricePerSqm)) {
			if (isStep4) console.log("Quote calc: Invalid pricePerSqm", pricePerSqm);
			return null;
		}

		// 4. Calculate total cost
		const totalCost = currentArea * pricePerSqm;

		// 5. Validate total cost: must be a number
		if (typeof totalCost !== "number" || isNaN(totalCost)) {
			if (isStep4)
				console.log("Quote calc: Invalid totalCost", totalCost, {
					currentArea,
					pricePerSqm,
				});
			return null;
		}

		// All checks passed, create and return the quote object
		const result = {
			pavingType: pavingTypeDetails.name,
			sizeOption: sizeOptionDetails.name,
			sizeDetail: sizeDetailData.size,
			area: currentArea, // Use the validated area
			pricePerSqm: pricePerSqm,
			totalCost: totalCost,
		};

		if (isStep4) {
			console.log("Quote calculated successfully:", result);
		}
		return result;
	}

	// Derived state for quote - calls the calculation function
	const quote = $derived(calculateQuote());

	// Also log when step changes

	// --- Functions ---
	function goToStep(stepNumber) {
		currentStep = stepNumber;
	}

	// Update handlers to accept payload directly
	function handleSelectPavingType(id) {
		selectedPavingType = id;
		selectedSizeOption = null; // Reset subsequent selections
		selectedSizeDetail = null;
	}

	function handleSelectSizeOption(id) {
		selectedSizeOption = id;
		selectedSizeDetail = null; // Reset subsequent selections

		// If this is a mix option that includes all sizes, auto-select the first size detail
		const selectedOption = availableSizeOptions.find(
			(option) => option.id === id,
		);
		if (selectedOption?.isMix && availableSizeDetails.length > 0) {
			// Auto-select the first size detail for mix options
			selectedSizeDetail = 0;
		}
	}

	function handleSelectSizeDetail(index) {
		selectedSizeDetail = index;
	}

	function handleUpdateLength(value) {
		length = value;
	}

	function handleUpdateWidth(value) {
		width = value;
	}

	function handleUpdateDirectArea(value) {
		directArea = value;
	}

	function toggleAreaInputMode() {
		isDirectAreaMode = !isDirectAreaMode;
	}

	function handleUpdateEmail(value) {
		recipientEmail = value;
	}

	function handleNext() {
		if (currentStep < 4) {
			goToStep(currentStep + 1);
		}
	}

	function handleBack() {
		if (currentStep > 1) {
			goToStep(currentStep - 1);
		}
	}

	function handleRestart() {
		currentStep = 1;
		selectedPavingType = null;
		selectedSizeOption = null;
		selectedSizeDetail = null;
		length = 0;
		width = 0;
		showEmailModal = false; // Close modal if open
		recipientEmail = "";
	}

	// Email Modal Logic (kept in App.svelte for simplicity)
	function openEmailModal() {
		recipientEmail = ""; // Clear previous email
		showEmailModal = true;
	}

	function closeEmailModal() {
		showEmailModal = false;
	}

	async function sendEmailFromModal(postcode) {
		if (quote && recipientEmail) {
			// Basic email validation (optional, enhance as needed)
			if (!/\S+@\S+\.\S+/.test(recipientEmail)) {
				alert("Please enter a valid email address.");
				return;
			}

			try {
				// Use FormData for WordPress AJAX compatibility
				const formData = new FormData();
				formData.append("action", "pps_send_quote_email");
				formData.append("quote", JSON.stringify(quote));
				formData.append("email", recipientEmail);
				formData.append("postcode", postcode || ""); // Add postcode to form data

				const response = await fetch(
					// @ts-ignore
					window.ppsData?.ajax_url || "/wp-admin/admin-ajax.php",
					{
						method: "POST",
						body: formData,
					},
				);
				const result = await response.json();
				if (result.success) {
					alert("Quote email sent successfully!");
					closeEmailModal();
				} else {
					alert(result.data?.message || "Failed to send quote email.");
				}
			} catch (error) {
				console.error("Failed to send quote email:", error);
				alert("Failed to send quote email. Please try again.");
				// Optionally keep the modal open on failure
			}
		} else if (!recipientEmail) {
			alert("Please enter an email address.");
		}
	}

	// Button disabled states (derived)
	const isStep1NextDisabled = $derived(selectedPavingType === null);
	const isStep2NextDisabled = $derived(selectedSizeOption === null);
	const isStep3NextDisabled = $derived(
		selectedSizeDetail === null || area <= 0,
	);
</script>

<main class="apc-app-container">
	<div class="apc-content-wrapper">
		<div class="apc-card apc-main-card">
			<!-- Progress Bar -->
			<div class="apc-progress-bar">
				{#each [1, 2, 3, 4] as step}
					<div
						class="apc-step-indicator"
						class:apc-step-active={currentStep === step}
						class:apc-step-inactive={currentStep < step}
						class:apc-step-complete={currentStep > step}
						data-step={step}
					>
						{step}
					</div>
				{/each}
			</div>

			<div class="apc-step-content">
				<!-- Render current step component -->
				{#if currentStep === 1}
					<Step1PavingType
						{pavingTypes}
						{selectedPavingType}
						{pavingTypeDetails}
						{isStep1NextDisabled}
						onSelectPavingType={handleSelectPavingType}
						onNext={handleNext}
					/>
				{:else if currentStep === 2}
					<Step2SizeOptions
						{availableSizeOptions}
						{selectedSizeOption}
						{sizeOptionDetails}
						{pavingTypeDetails}
						{isStep2NextDisabled}
						onSelectSizeOption={handleSelectSizeOption}
						onNext={handleNext}
						onBack={handleBack}
					/>
				{:else if currentStep === 3}
					<Step3Dimensions
						{availableSizeDetails}
						{selectedSizeDetail}
						{pavingTypeDetails}
						{sizeOptionDetails}
						{length}
						{width}
						{area}
						{isDirectAreaMode}
						onUpdateDirectArea={handleUpdateDirectArea}
						{toggleAreaInputMode}
						{isStep3NextDisabled}
						onSelectSizeDetail={handleSelectSizeDetail}
						onUpdateLength={handleUpdateLength}
						onUpdateWidth={handleUpdateWidth}
						onNext={handleNext}
						onBack={handleBack}
					/>
				{:else if currentStep === 4}
					<Step4QuoteSummary
						{quote}
						{showEmailModal}
						{recipientEmail}
						onBack={handleBack}
						onOpenEmailModal={openEmailModal}
						onCloseEmailModal={closeEmailModal}
						onSendEmail={sendEmailFromModal}
						onRestart={handleRestart}
						onUpdateEmail={handleUpdateEmail}
					/>
				{/if}
			</div>
		</div>
	</div>
</main>

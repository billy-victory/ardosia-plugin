/**
 * Data structure for paving types, sizes, and pricing
 */

// Paving types with descriptions
export const pavingTypes = [
	{
		id: "brazilian-graphite",
		name: "Brazilian Graphite Black Riven Slate",
		description: "Rich, dark graphite tones with natural textures",
		image: "/wp-content/uploads/2024/07/Slate-Riven-Graphite-3-scaled.jpg",
	},
	{
		id: "brazilian-blue-grey",
		name: "Brazilian Blue Grey Riven Slate",
		description: "Elegant blue-grey tones with natural veining",
		image: "/wp-content/uploads/2024/07/Slate-Riven-Blue-Grey-2-scaled.jpg",
	},
	{
		id: "tumbled-slate",
		name: "Tumbled Slate Cobbles",
		description: "Rustic cobbles with weathered finish",
		image:
			"/wp-content/uploads/2024/07/Tumbled-Slate-30mm-Cobbles-Dry-scaled.jpg",
	},
	{
		id: "tumbled-limestone",
		name: "Tumbled Black Limestone Cobbles",
		description: "Elegant dark cobbles with tumbled finish",
		image:
			"/wp-content/uploads/2024/07/Tumbled-Limestone-Cobbles-Dry-300x150-150x150-150x75-1-scaled.jpg",
	},
	{
		id: "yellow-limestone",
		name: "Yellow Limestone",
		description: "Warm, golden tones with natural variations",
		image: "/wp-content/uploads/2024/07/Yellow-Limestone-Dry-1-scaled.jpg",
		requiresEnquiry: true,
	},
	{
		id: "lime-ash",
		name: "Lime Ash",
		description: "Light, contemporary finish with subtle texture",
		image: "/wp-content/uploads/2024/07/Lime-Ash-Dry-scaled.jpg",
	},
	{
		id: "juparana-granite",
		name: "Juparana Granite",
		description: "Striking pattern with warm beige and burgundy tones",
		image: "/wp-content/uploads/2024/07/Juparana-Dry-scaled.jpg",
	},
	{
		id: "cathedral-ashlar",
		name: "Cathedral Ashlar",
		description: "Traditional cut stone with elegant appearance",
		image: "/wp-content/uploads/2024/07/Cathedral-Ashlar-Dry-scaled.jpg",
	},
];

// Size options available for each paving type
export const sizeOptions = {
	"brazilian-graphite": [
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
		{
			id: "three-sizes",
			name: "Three Sizes Mix",
			description: "Varied sizes for traditional layouts",
			isMix: true, // Mark as a mix that includes all sizes
		},
	],
	"brazilian-blue-grey": [
		// Mapped from 'brazilian-blue' in PAVING_DATA
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
	],
	"tumbled-slate": [
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
		{
			id: "three-sizes",
			name: "Three Sizes Mix",
			description: "Varied sizes for traditional layouts",
			isMix: true, // Mark as a mix that includes all sizes
		},
	],
	"tumbled-limestone": [
		// Mapped from 'tumbled-black-limestone-cobbles' in PAVING_DATA
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
	],
	"yellow-limestone": [
		{
			id: "four-sizes",
			name: "Four Sizes Mix",
			description: "Varied sizes for traditional layouts",
			isMix: true, // Mark as a mix that includes all sizes
		},
	],
	"lime-ash": [
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
		{
			id: "four-sizes",
			name: "Four Sizes Mix",
			description: "Varied sizes for traditional layouts",
			isMix: true, // Mark as a mix that includes all sizes
		},
	],
	"juparana-granite": [
		{
			id: "single-size",
			name: "Single Size Sizes",
			description: "Uniform size for consistent patterns",
		},
	],
	"cathedral-ashlar": [
		// Mapping 'mixed' from PAVING_DATA to 'three-sizes' based on existing sizeDetails logic
		{
			id: "three-sizes",
			name: "Various Lengths", // Updated name
			description: "Varied lengths for traditional layouts",
			isMix: true, // Mark as a mix that includes all sizes
		},
	],
};

// Size specific options with prices
export const sizeDetails = {
	"brazilian-graphite": {
		"single-size": [{ size: "900 x 600 x 20 mm", price: 37.0 }],
		"three-sizes": [
			{ size: "900 x 600 x 20 mm", price: 37.0 },
			{ size: "600 x 600 x 20 mm", price: 37.0 },
			{ size: "600 x 295 x 20 mm", price: 37.0 },
		],
	},
	"brazilian-blue-grey": {
		"single-size": [{ size: "900 x 600 x 20 mm", price: 37.0 }],
	},
	"tumbled-slate": {
		"single-size": [
			{ size: "100 x 100 x 7-10 mm", price: 45.0 },
			{ size: "300 x 150 x 20 mm", price: 35.0 },
		],
		"three-sizes": [
			{ size: "100 x 150 x 20 mm", price: 65.0 },
			{ size: "150 x 150 x 20 mm", price: 65.0 },
			{ size: "200 x 150 x 20 mm", price: 65.0 },
		],
	},
	"tumbled-limestone": {
		"single-size": [
			{ size: "150 x 150 x 20 mm", price: 24.0 },
			{ size: "300 x 75 x 20 mm", price: 24.0 },
			{ size: "300 x 150 x 20 mm", price: 24.0 },
		],
	},
	"yellow-limestone": {
		// Note: Message "For price please submit enquiry form..." is not stored here.
		"four-sizes": [
			{ size: "900 x 600 x 20 mm", price: 0.0 }, // Price requires inquiry
			{ size: "600 x 600 x 20 mm", price: 0.0 }, // Price requires inquiry
			{ size: "600 x 290 x 20 mm", price: 0.0 }, // Price requires inquiry
			{ size: "290 x 290 x 20 mm", price: 0.0 }, // Price requires inquiry
		],
	},
	"lime-ash": {
		"single-size": [{ size: "900x600 x 20 mm", price: 45.0 }],
		"four-sizes": [
			{ size: "900 x 600 x 22 mm", price: 45.0 },
			{ size: "600 x 600 x 22 mm", price: 45.0 },
			{ size: "600 x 290 x 22 mm", price: 45.0 },
			{ size: "290 x 290 x 22 mm", price: 45.0 },
		],
	},
	"juparana-granite": {
		"single-size": [{ size: "900 x 600 x 20 mm", price: 45.0 }],
	},
	"cathedral-ashlar": {
		// Mapping 'mixed' from new data to 'three-sizes' as it's an existing option for this type.
		"three-sizes": [{ size: "400 x 900 x 600 x 20 mm", price: 40.0 }],
	},
};

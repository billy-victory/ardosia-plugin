import { mount } from "svelte";
import App from "./App.svelte";
import "./style.css";

// Handle mounting the application with DOM checking
function mountApp() {
	const targetElement = document.getElementById("app");

	if (targetElement) {
		return mount(App, {
			target: targetElement,
		});
	} else {
		// If app element isn't available yet, try again shortly
		console.log("App element not found, retrying...");
		setTimeout(mountApp, 100);
	}
}

// Initialize app
const app = mountApp();

export default app;

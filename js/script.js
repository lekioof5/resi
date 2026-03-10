document.addEventListener("DOMContentLoaded", function () {
	// Letakkan ini di bagian paling atas DOMContentLoaded di script.js
	if (
		window.location.search.indexOf("error=") > -1 ||
		window.location.search.indexOf("success=") > -1
	) {
		// Tunggu 1 detik agar user sempat baca, lalu bersihkan URL tanpa refresh
		setTimeout(function () {
			const cleanUrl = window.location.pathname;
			window.history.replaceState({}, document.title, cleanUrl);
		}, 1000);
	}

	const resiInput = document.getElementById("resiInput");
	const manualModal = document.getElementById("manualEntryModal");
	const loginModal = document.getElementById("loginModal");
	const notificationArea = document.getElementById("notification-area");

	// 1. Logika Fokus Input
	if (resiInput) {
		resiInput.focus();
		document.addEventListener("click", () => {
			if (
				!manualModal.classList.contains("show") &&
				!loginModal.classList.contains("show")
			) {
				resiInput.focus();
			}
		});
	}

	// 2. LOGIKA MEMBERSIHKAN NOTIFIKASI & URL (Solusi masalah Anda)
	if (notificationArea && notificationArea.innerText.trim() !== "") {
		// Hapus parameter di URL browser agar tidak muncul lagi saat auto-refresh
		if (window.history.replaceState) {
			const cleanUrl =
				window.location.protocol +
				"//" +
				window.location.host +
				window.location.pathname;
			window.history.replaceState({ path: cleanUrl }, "", cleanUrl);
		}

		// Hilangkan notifikasi secara visual setelah 3 detik
		setTimeout(() => {
			const alerts = notificationArea.querySelectorAll(".alert");
			alerts.forEach((alert) => {
				alert.classList.remove("show");
				setTimeout(() => alert.remove(), 500);
			});
		}, 3000);
	}

	// 3. Logika Auto-Refresh 5 Detik
	setInterval(function () {
		const isTyping =
			document.activeElement.tagName === "INPUT" &&
			document.activeElement.value !== "";
		const isModalOpen =
			manualModal.classList.contains("show") ||
			loginModal.classList.contains("show");

		if (!isTyping && !isModalOpen) {
			window.location.reload();
		}
	}, 5000);
});

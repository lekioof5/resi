document.addEventListener("DOMContentLoaded", function () {
	const resiInput = document.getElementById("resiInput");
	const manualModal = document.getElementById("manualEntryModal");
	const loginModal = document.getElementById("loginModal");
	const notificationArea = document.getElementById("notification-area");

	// --- 1. LOGIKA AUTO-FOCUS AGRESIF ---
	if (resiInput) {
		resiInput.focus();

		// Klik di mana saja, fokus balik ke input (kecuali jika modal buka)
		document.addEventListener("click", () => {
			const isModalOpen = document.querySelector(".modal.show");
			if (!isModalOpen) resiInput.focus();
		});

		// Kembalikan fokus SETELAH modal ditutup
		const modals = [
			manualModal,
			loginModal,
			document.getElementById("modalProses"),
		];
		modals.forEach((modalEl) => {
			if (modalEl) {
				modalEl.addEventListener("hidden.bs.modal", () => {
					setTimeout(() => resiInput.focus(), 100);
				});
			}
		});
	}

	// --- 2. PEMBERSIHAN URL & NOTIFIKASI ---
	if (
		window.location.search.includes("error=") ||
		window.location.search.includes("success=") ||
		window.location.search.includes("status=")
	) {
		setTimeout(() => {
			const cleanUrl = window.location.pathname;
			window.history.replaceState({}, document.title, cleanUrl);

			if (notificationArea) {
				const alerts = notificationArea.querySelectorAll(".alert");
				alerts.forEach((alert) => {
					alert.classList.remove("show");
					setTimeout(() => alert.remove(), 500);
				});
			}
		}, 3000);
	}

	// --- 3. LOGIKA AUTO-REFRESH KHUSUS KURIR ---
	if (typeof IS_INDEX_KURIR !== "undefined" && IS_INDEX_KURIR === true) {
		setInterval(function () {
			const isModalOpen = document.querySelector(".modal.show");

			// Anggap "sedang mengetik" jika input ada isinya
			const isTyping = resiInput && resiInput.value.length > 0;

			if (!isModalOpen && !isTyping) {
				window.location.reload();
			}
		}, 5000);
	}
});

// --- FUNGSI PENCARIAN GLOBAL ---
function handleSearchKeyPress(e) {
	if (e.key === "Enter") {
		executeGlobalSearch();
	}
}

function executeGlobalSearch() {
	const input = document.getElementById("globalSearchInput");
	if (!input) return;

	const keyword = input.value.trim();

	if (keyword === "") {
		alert("Silakan masukkan nomor resi atau URN.");
		return;
	}

	// Tampilkan feedback loading pada tombol jika ada
	const btn = document.getElementById("btnSearchGlobal");
	if (btn) btn.disabled = true;

	fetch(`get_search_resi.php?keyword=${encodeURIComponent(keyword)}`)
		.then((response) => response.json())
		.then((data) => {
			if (btn) btn.disabled = false;

			if (data.error) {
				alert("Data tidak ditemukan di database.");
			} else {
				// KUNCINYA: Memanggil fungsi yang ada di includes/modal_detail.php
				if (typeof lihatDetailHistory === "function") {
					lihatDetailHistory(data);
					input.value = "";
				} else {
					console.error(
						"Fungsi lihatDetailHistory tidak ditemukan. Pastikan includes/modal_detail.php sudah di-include.",
					);
				}
			}
		})
		.catch((err) => {
			if (btn) btn.disabled = false;
			console.error("Search Error:", err);
		});
}

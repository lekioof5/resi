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

// --- 1. Fungsi Handle Keypress (Enter) ---
function handleSearchKeyPress(e) {
	if (e.key === "Enter") {
		executeGlobalSearch();
	}
}

// --- 2. Fungsi Eksekusi Pencarian ---
function executeGlobalSearch() {
	const input = document.getElementById("globalSearchInput");
	const btn = document.getElementById("btnSearchGlobal");
	const keyword = input.value.trim().toUpperCase();

	if (keyword === "") return;

	// Loading State
	if (btn) btn.disabled = true;

	fetch(`get_search_resi.php?keyword=${encodeURIComponent(keyword)}`)
		.then((response) => response.json())
		.then((data) => {
			if (btn) btn.disabled = false;

			if (data.error) {
				alert("Data tidak ditemukan di database!");
			} else {
				// Tentukan target tab (waiting, pending, history)
				let targetTab = "waiting";
				if (data.status == 1) targetTab = "waiting";
				else if (data.status == 3) targetTab = "pending";
				else if ([2, 4, 5].includes(parseInt(data.status)))
					targetTab = "history";

				// Cari tombol tab yang sesuai di index2.php
				const tabButton = document.querySelector(
					`[onclick*="filterAntrian('${targetTab}'"]`,
				);

				// Panggil fungsi dari realtime-antrian.js
				if (typeof filterAntrian === "function") {
					filterAntrian(targetTab, tabButton);

					// Highlight baris setelah tabel terisi (jeda 1.2 detik karena ada spinner)
					setTimeout(() => {
						highlightRow(keyword);
					}, 1200);

					input.value = "";
				} else {
					alert(
						"Sistem gagal memindahkan tab (Fungsi filterAntrian tidak ditemukan).",
					);
				}
			}
		})
		.catch((err) => {
			if (btn) btn.disabled = false;
			console.error("Search Error:", err);
		});
}

// --- 3. Fungsi Memberi Warna pada Baris ---
function highlightRow(keyword) {
	const container = document.getElementById("tabel-antrian-live");
	if (!container) return;

	const rows = container.querySelectorAll("tbody tr");
	let found = false;

	rows.forEach((row) => {
		if (row.innerText.toUpperCase().includes(keyword)) {
			row.style.transition = "all 0.5s ease";
			row.style.backgroundColor = "#fff3cd"; // Kuning
			row.style.borderLeft = "5px solid #ffc107";

			row.scrollIntoView({ behavior: "smooth", block: "center" });
			found = true;

			setTimeout(() => {
				row.style.backgroundColor = "";
				row.style.borderLeft = "";
			}, 5000);
		}
	});
}

function generateUrnInputs() {
	const jumlah = document.getElementById("input_jumlah_dokumen").value;
	const container = document.getElementById("container_urn_dynamic");

	// Simpan nilai URN yang sudah diketik agar tidak hilang saat jumlah berubah
	const existingUrns = Array.from(
		container.querySelectorAll('input[name="urn[]"]'),
	).map((input) => input.value);

	container.innerHTML = ""; // Kosongkan container

	for (let i = 1; i <= jumlah; i++) {
		const val = existingUrns[i - 1] ? existingUrns[i - 1] : "";
		container.innerHTML += `
            <div class="mb-2">
                <label class="small fw-bold">Nomor URN ${i}</label>
                <input type="text" name="urn[]" class="form-control"
                       placeholder="Masukkan URN ${i}" value="${val}" required>
            </div>
        `;
	}
}

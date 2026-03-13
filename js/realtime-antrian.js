// 1. Variabel Global
let refreshInterval;
let isUserEditing = false;
let currentFilter = "waiting"; // Default tab saat pertama kali muat

function filterAntrian(filter, element) {
	currentFilter = filter;

	// Pindahkan kelas 'active' secara manual
	if (element) {
		document
			.querySelectorAll("#pills-tab .nav-link")
			.forEach((btn) => btn.classList.remove("active"));
		element.classList.add("active");
	}

	loadAntrian();
}

function loadAntrian() {
	const anyModalOpen = document.querySelector(".modal.show");
	if (isUserEditing || anyModalOpen) return;

	// Munculkan Spinner
	const loader = document.getElementById("refresh-loader");
	if (loader) {
		loader.classList.remove("opacity-0");
		loader.classList.add("opacity-100");
	}

	fetch(`get_antrian.php?filter=${currentFilter}`)
		.then((res) => res.text())
		.then((data) => {
			document.getElementById("tabel-antrian-live").innerHTML = data;

			// Sembunyikan Spinner setelah 500ms agar transisinya terlihat halus
			setTimeout(() => {
				if (loader) {
					loader.classList.remove("opacity-100");
					loader.classList.add("opacity-0");
				}
			}, 500);
		})
		.catch((err) => {
			console.error("Fetch error:", err);
			if (loader) loader.classList.add("opacity-0");
		});

	updateCounters();
}

// 4. Fungsi Update Angka Badge (Counter)
function updateCounters() {
	fetch("get_counts.php")
		.then((res) => res.json())
		.then((data) => {
			if (document.getElementById("count-waiting"))
				document.getElementById("count-waiting").innerText = data.waiting;
			if (document.getElementById("count-received"))
				document.getElementById("count-received").innerText = data.received;
			if (document.getElementById("count-pending"))
				document.getElementById("count-pending").innerText = data.pending;
		});

	// Reminder khusus PIC login
	fetch("get_reminder_data.php")
		.then((res) => res.json())
		.then((data) => {
			const badge = document.getElementById("my-pending-badge");
			if (badge) {
				if (data.my_pending > 0) {
					badge.classList.remove("d-none");
					badge.innerHTML = `<i class="bi bi-bell-fill"></i> ${data.my_pending}`;
				} else {
					badge.classList.add("d-none");
				}
			}
		});
}

// 5. Fungsi Kontrol Refresh
function startRefresh() {
	isUserEditing = false;
	clearInterval(refreshInterval);
	refreshInterval = setInterval(loadAntrian, 5000); // Refresh tiap 5 detik
}

function stopRefresh() {
	isUserEditing = true;
	clearInterval(refreshInterval);
}

// 7. Tahap 2: Buka Modal Verifikasi
function bukaModalVerifikasi(id, resi) {
	stopRefresh();

	document.getElementById("modal_id_verif").value = id;
	document.getElementById("display_resi_verif").innerText = resi;

	const modalEl = document.getElementById("modalVerifikasi");
	const myModal = new bootstrap.Modal(modalEl);
	myModal.show();

	modalEl.addEventListener(
		"hidden.bs.modal",
		() => {
			startRefresh();
		},
		{ once: true },
	);
}

// 8. Update Ekspedisi Inline
function updateEkspedisi(id, nilaiBaru, element) {
	if (!element) return;

	// Beri tanda warna biru saat sedang proses
	element.classList.add("text-primary");

	fetch("proses_update_ekspedisi.php", {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: `id=${id}&ekspedisi=${encodeURIComponent(nilaiBaru.trim())}`,
	})
		.then((response) => response.text())
		.then((data) => {
			if (data.trim() === "success") {
				// Beri feedback hijau jika sukses
				element.classList.replace("text-primary", "text-success");
				setTimeout(() => {
					element.classList.remove("text-success");
					startRefresh(); // Mulai refresh kembali
				}, 1000);
			} else {
				alert("Gagal update ekspedisi!");
				element.classList.remove("text-primary");
				startRefresh();
			}
		})
		.catch(() => {
			element.classList.remove("text-primary");
			startRefresh();
		});
}

// 9. Inisialisasi Pertama
document.addEventListener("DOMContentLoaded", () => {
	loadAntrian();
	startRefresh();
});

function bukaModalProsesAwal(id, resi) {
	stopRefresh(); // Berhenti refresh agar inputan tidak hilang
	document.getElementById("modal_id_proses").value = id;
	document.getElementById("display_resi_proses").innerText = resi;

	const modalEl = document.getElementById("modalProsesAwal");
	const myModal = new bootstrap.Modal(modalEl);
	myModal.show();

	modalEl.addEventListener("hidden.bs.modal", () => startRefresh(), {
		once: true,
	});
}

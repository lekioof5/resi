let refreshInterval;
let isUserEditing = false;

// Fungsi utama mengambil data tabel
function loadAntrian() {
	// PROTEKSI: Jangan refresh jika user sedang edit dropdown ATAU ada modal apapun yang terbuka
	const anyModalOpen = document.querySelector(".modal.show");

	if (isUserEditing || anyModalOpen) {
		console.log("Auto-update ditunda: User sedang berinteraksi.");
		return;
	}

	fetch("get_antrian.php")
		.then((response) => response.text())
		.then((data) => {
			const tableBody = document.getElementById("tabel-antrian-live");
			if (tableBody) {
				tableBody.innerHTML = data;
			}
		})
		.catch((err) => console.error("Koneksi ke server terputus:", err));
}

// Fungsi kontrol refresh
function startRefresh() {
	isUserEditing = false;
	clearInterval(refreshInterval);
	refreshInterval = setInterval(loadAntrian, 5000);
}

function stopRefresh() {
	isUserEditing = true;
	clearInterval(refreshInterval);
}

// Update Ekspedisi tanpa refresh halaman
function updateEkspedisi(id, nilaiBaru, element) {
	if (!element) return;
	stopRefresh(); // Berhenti refresh saat proses simpan

	element.classList.add("text-primary");

	fetch("proses_update_ekspedisi.php", {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: `id=${id}&ekspedisi=${encodeURIComponent(nilaiBaru)}`,
	})
		.then((response) => response.text())
		.then((data) => {
			if (data === "success") {
				element.classList.replace("text-primary", "text-success");
				setTimeout(() => {
					element.classList.remove("text-success");
					startRefresh();
				}, 1000);
			} else {
				alert("Gagal update!");
				startRefresh();
			}
		})
		.catch(() => startRefresh());
}

// Handler Modal Validasi
function bukaModalValidasi(id, resi) {
	stopRefresh();

	document.getElementById("modal_id").value = id;
	document.getElementById("modal_resi").value = resi;

	const modalEl = document.getElementById("modalProses");
	const myModal = new bootstrap.Modal(modalEl);
	myModal.show();

	// Pastikan refresh jalan lagi HANYA saat modal benar-benar tertutup
	modalEl.addEventListener(
		"hidden.bs.modal",
		() => {
			startRefresh();
		},
		{ once: true },
	); // {once: true} agar event tidak menumpuk
}

// Jalankan saat startup
document.addEventListener("DOMContentLoaded", () => {
	loadAntrian();
	startRefresh();
});

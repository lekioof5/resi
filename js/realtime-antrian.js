// File: js/realtime-antrian.js

let isUserEditing = false;
let refreshInterval;

// Fungsi untuk mengambil data dari server
function loadAntrian() {
	if (isUserEditing) return; // Jangan refresh jika user sedang membuka dropdown

	fetch("get_antrian.php")
		.then((response) => response.text())
		.then((data) => {
			const tableBody = document.getElementById("tabel-antrian-live");
			if (tableBody) {
				tableBody.innerHTML = data;
			}
		})
		.catch((err) => console.error("Gagal memuat data:", err));
}

// Fungsi kontrol refresh (Interval 5 detik)
function startRefresh() {
	isUserEditing = false;
	// Bersihkan interval lama jika ada untuk mencegah double interval
	clearInterval(refreshInterval);
	refreshInterval = setInterval(loadAntrian, 5000);
}

function stopRefresh() {
	isUserEditing = true;
	clearInterval(refreshInterval);
}

// Fungsi Update Ekspedisi (AJAX)
function updateEkspedisi(id, nilaiBaru, element) {
	element.classList.add("text-primary");

	fetch("proses_update_ekspedisi.php", {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: `id=${id}&ekspedisi=${encodeURIComponent(nilaiBaru)}`,
	})
		.then((response) => response.text())
		.then((data) => {
			if (data === "success") {
				element.classList.remove("text-primary");
				element.classList.add("text-success");
				setTimeout(() => {
					element.classList.remove("text-success");
					startRefresh(); // Jalankan kembali auto-refresh setelah simpan
				}, 1000);
			} else {
				alert("Gagal mengupdate ekspedisi.");
				startRefresh();
			}
		});
}

// Inisialisasi saat halaman siap
document.addEventListener("DOMContentLoaded", () => {
	loadAntrian();
	startRefresh();
});

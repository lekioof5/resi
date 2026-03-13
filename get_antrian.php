<?php
session_start();
include "koneksi.php";

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'waiting';
$my_name = $_SESSION['nama_user']; // Ambil nama user dari session

switch ($filter) {
    case 'received':
        // HANYA dokumen yang diterima oleh saya sendiri
        $sql = "SELECT *, waktu_receive as waktu_tampil FROM scans
                WHERE status = 1 AND nama_pic = '$my_name'
                ORDER BY waktu_receive DESC";
        $label_waktu = "DITERIMA";
        break;

    case 'pending':
        // HANYA dokumen yang di-pending oleh saya sendiri
        $sql = "SELECT *, waktu_pending as waktu_tampil FROM scans
                WHERE status = 3 AND nama_pic = '$my_name'
                ORDER BY waktu_pending DESC";
        $label_waktu = "PENDING";
        break;

    case 'history':
        // SEMUA orang bisa melihat riwayat SEMUA orang (Global)
        $sql = "SELECT *, waktu_masuk as waktu_tampil FROM scans
                WHERE status IN (2, 4, 5)
                ORDER BY id DESC LIMIT 50";
        $label_waktu = "MASUK";
        break;

    default: // 'waiting'
        // SEMUA orang bisa melihat antrian baru (Global)
        $sql = "SELECT *, waktu_masuk as waktu_tampil FROM scans
                WHERE status = 0
                ORDER BY waktu_masuk ASC";
        $label_waktu = "MASUK";
        break;
}

$query_antrian = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query_antrian) > 0):
    while ($row = mysqli_fetch_assoc($query_antrian)):
        // Logika penentuan waktu yang akan ditampilkan
        $waktu = (!empty($row['waktu_tampil'])) ? $row['waktu_tampil'] : $row['waktu_masuk'];
        ?>
<tr>
    <td class="ps-4 col-time-fixed">
        <span class="d-block fw-bold"><?= date('H:i', strtotime($waktu)) ?></span>
        <small class="text-muted text-uppercase" style="font-size: 10px;"><?= $label_waktu ?> <?= date('d/m', strtotime($waktu)) ?></small>
    </td>

    <td>
        <div class="fw-bold text-dark"><?= htmlspecialchars($row['nomor_resi']) ?></div>
        <div class="small">
            <?php if ($filter == 'history'): ?>
                <span class="text-success fw-bold">
                    URN: <?= htmlspecialchars($row['nomor_urn'] ?? '-') ?>
                </span>
                <span class="text-muted mx-1">|</span>
                <span class="text-muted">
                    <i class="bi bi-person"></i> <?= htmlspecialchars($row['nama_pic'] ?? 'No PIC') ?>
                </span>
            <?php elseif ($row['status'] == 1): ?>
                <span class="badge bg-secondary">Jml: <?= $row['jumlah'] ?></span>
                <span class="ms-1 text-muted">Vendor: <?= htmlspecialchars($row['nama_vendor'] ?? '-') ?></span>
            <?php endif; ?>
        </div>
    </td>

    <td>
        <div class="fw-bold">
            <?php if ($filter !== 'history'): ?>
                <span class="editable-ekspedisi"
                    contenteditable="true"
                    onfocus="stopRefresh()"
                    onblur="updateEkspedisi(<?= $row['id'] ?>, this.innerText, this)">
                    <?= htmlspecialchars($row['ekspedisi'] ?? '') ?>
                </span>
            <?php else: ?>
                <span class="text-dark"><?= htmlspecialchars($row['ekspedisi'] ?? '') ?></span>
            <?php endif; ?>
        </div>

        <div class="small text-muted">
            <?= htmlspecialchars($row['nomor_hp'] ?? '-') ?>
        </div>
    </td>

    <td class="text-center">
        <?php if ($row['status'] == 0): ?>
            <button class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" onclick="bukaModalProsesAwal(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-gear-fill me-1"></i> PROSES
            </button>
        <?php elseif ($row['status'] == 1 || $row['status'] == 3): ?>
            <button class="btn btn-warning btn-sm px-3 shadow-sm fw-bold" onclick="bukaModalVerifikasi(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-shield-check me-1"></i> VERIFIKASI
            </button>
        <?php else: ?>
            <?php
                $badgeClass = "bg-secondary";
            $statusText = "FINISHED";
            if ($row['status'] == 2) {
                $badgeClass = "bg-success";
                $statusText = "PROCESSED";
            }
            if ($row['status'] == 4) {
                $badgeClass = "bg-danger";
                $statusText = "REJECTED";
            }
            if ($row['status'] == 5) {
                $badgeClass = "bg-info text-dark";
                $statusText = "LINKED";
            }
            ?>
            <span class="badge <?= $badgeClass ?> px-2 shadow-sm" style="font-size: 11px;">
                <?= $statusText ?>
            </span>
        <?php endif; ?>
    </td>
</tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="4" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            Tidak ada dokumen di tab ini.
        </td>
    </tr>
<?php endif; ?>
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
        $waktu = (!empty($row['waktu_tampil'])) ? $row['waktu_tampil'] : $row['waktu_masuk'];

        // Cek apakah ini dokumen susulan (status 5 = LINKED)
        $is_susulan = ($row['status'] == 5);
        $row_style = $is_susulan ? 'style="border-left: 4px solid #0dcaf0; background-color: #f8fdff;"' : '';
        ?>
<tr <?= $row_style ?>>
    <td class="ps-4 col-time-fixed">
        <span class="d-block fw-bold"><?= date('H:i', strtotime($waktu)) ?></span>
        <small class="text-muted text-uppercase" style="font-size: 10px;"><?= $label_waktu ?> <?= date('d/m', strtotime($waktu)) ?></small>
    </td>

    <td>
        <div class="d-flex align-items-center mb-1">
            <div class="fw-bold text-dark me-2">
                <?= htmlspecialchars($row['nomor_resi'] ?? '') ?>
            </div>

            <?php if ($is_susulan): ?>
                <span class="badge bg-info text-dark" style="font-size: 9px;"><i class="bi bi-link-45deg"></i> SUSULAN</span>
            <?php endif; ?>
        </div>

        <div class="small">
            <?php if ($filter == 'history'): ?>
                <div class="d-flex border-top pt-1 mt-1 align-items-center">
                    <div style="min-width: 150px;" class="text-success fw-bold">
                        <i class="bi bi-hash"></i> <?= htmlspecialchars($row['nomor_urn'] ?? '-') ?>
                    </div>
                    <div class="text-muted border-start ps-2">
                        <i class="bi bi-person-fill"></i> <?= htmlspecialchars($row['nama_pic'] ?? 'No PIC') ?>
                    </div>
                </div>
            <?php elseif ($row['status'] == 1 || $row['status'] == 3): ?>
                <span class="badge bg-secondary">Jml: <?= $row['jumlah'] ?? 0 ?></span>
                <span class="ms-1 text-muted">Vendor: <?= htmlspecialchars($row['nama_vendor'] ?? '-') ?></span>
            <?php endif; ?>
        </div>
    </td>

    <td>
        <div class="fw-bold">
            <?php if ($filter !== 'history'): ?>
                <span class="editable-ekspedisi text-uppercase" contenteditable="true"
                    onfocus="stopRefresh()"
                    onblur="updateEkspedisi(<?= $row['id'] ?>, this.innerText.toUpperCase(), this)">
                    <?= strtoupper(htmlspecialchars($row['ekspedisi'] ?? '')) ?>
                </span>
            <?php else: ?>
                <span class="text-dark text-uppercase"><?= strtoupper(htmlspecialchars($row['ekspedisi'] ?? '')) ?></span>
            <?php endif; ?>
        </div>
        <div class="small text-muted"><?= htmlspecialchars($row['nomor_hp'] ?? '') ?></div>
    </td>

    <td class="text-center">
        <?php if ($row['status'] == 0): ?>
            <button class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" onclick="bukaModalProsesAwal(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-gear-fill"></i> PROSES
            </button>
        <?php elseif ($row['status'] == 1 || $row['status'] == 3): ?>
            <button class="btn btn-warning btn-sm px-3 shadow-sm fw-bold" onclick="bukaModalVerifikasi(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-shield-check"></i> VERIFIKASI
            </button>
        <?php else: ?>
            <button class="btn btn-outline-dark btn-sm px-2 shadow-sm fw-bold mb-1 w-100"
                    onclick='lihatDetailHistory(<?= json_encode($row) ?>)' style="font-size: 10px;">
                <i class="bi bi-info-circle"></i> DETAIL
            </button>
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
            <span class="badge <?= $badgeClass ?> px-2 d-block" style="font-size: 9px;"><?= $statusText ?></span>
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
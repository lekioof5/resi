<?php
session_start();
include "koneksi.php";

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'waiting';
$my_name = $_SESSION['nama_user'];

switch ($filter) {
    case 'received':
        // Tab PROSES: Hanya tampilkan dokumen yang DITERIMA oleh saya sendiri
        // Agar PIC lain tidak menginput URN pada dokumen yang sedang dipegang PIC ini
        $sql = "SELECT *, waktu_receive as waktu_tampil FROM scans
                WHERE status = 1 AND nama_pic = '$my_name'
                ORDER BY waktu_receive ASC";
        $label_waktu = "DITERIMA";
        break;

    case 'pending':
        $sql = "SELECT *, waktu_pending as waktu_tampil FROM scans
                WHERE status = 3 AND nama_pic = '$my_name'
                ORDER BY waktu_pending DESC";
        $label_waktu = "PENDING";
        break;

    case 'history':
        // Tab HISTORY: Global (Semua orang bisa melihat hasil akhir semua orang)
        $sql = "SELECT *,
                COALESCE(waktu_proses, waktu_reject, waktu_receive, waktu_masuk) as waktu_tampil
                FROM scans
                WHERE status IN (2, 4, 5)
                ORDER BY id DESC LIMIT 100";
        $label_waktu = "SELESAI";
        break;

    default: // 'waiting'
        // Tab MASUK: Global (Semua orang bisa melihat antrian baru untuk segera diambil/diterima)
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
        <div class="fw-bold text-dark mb-1">
            <?= htmlspecialchars($row['nomor_resi'] ?? '') ?>
            <?php if ($is_susulan): ?>
                <span class="badge bg-info text-dark ms-1" style="font-size: 9px;">SUSULAN</span>
            <?php endif; ?>
        </div>
        <div class="small">
            <?php if ($row['status'] == 0): ?>
                <span class="text-muted italic">Menunggu keputusan...</span>
            <?php else: ?>
                <span class="badge bg-light text-dark border">Qty: <?= $row['jumlah'] ?? 0 ?></span>
                <span class="ms-1 text-muted">Vendor: <?= htmlspecialchars($row['nama_vendor'] ?? '-') ?></span>
            <?php endif; ?>
        </div>
    </td>

    <td>
        <div class="fw-bold text-uppercase">
            <?php if ($row['status'] == 0 || $row['status'] == 3): ?>
                <span class="editable-ekspedisi" contenteditable="true"
                    onfocus="stopRefresh()"
                    onblur="updateEkspedisi(<?= $row['id'] ?>, this.innerText.toUpperCase(), this)">
                    <?= strtoupper(htmlspecialchars($row['ekspedisi'] ?? '')) ?>
                </span>
            <?php else: ?>
                <?= strtoupper(htmlspecialchars($row['ekspedisi'] ?? '')) ?>
            <?php endif; ?>
        </div>
    </td>

    <td class="text-center">
        <?php if ($row['status'] == 0): ?>
            <button class="btn btn-success btn-sm px-3 shadow-sm fw-bold"
                    onclick="bukaModalProsesAwal(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-check-circle"></i> TERIMA / REJECT
            </button>

        <?php elseif ($row['status'] == 1): ?>
            <button class="btn btn-primary btn-sm px-3 shadow-sm fw-bold"
                    onclick="bukaModalVerifikasi(<?= $row['id'] ?>, '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-pencil-square"></i> INPUT URN
            </button>

        <?php elseif ($row['status'] == 3): ?>
            <button class="btn btn-sm btn-info text-white fw-bold"
                    onclick="openModalPending('<?= $row['id'] ?>', '<?= $row['nomor_resi'] ?>')">
                <i class="bi bi-check2-circle"></i> SELESAIKAN
            </button>

        <?php else: ?>
            <button class="btn btn-outline-dark btn-sm px-2 w-100"
                    onclick='lihatDetailHistory(<?= json_encode($row) ?>)' style="font-size: 10px;">
                DETAIL
            </button>
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
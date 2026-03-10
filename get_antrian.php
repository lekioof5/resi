<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['status_login'])) {
    die("Unauthorized");
}

$query_antrian = mysqli_query($koneksi, "SELECT * FROM scans WHERE is_validated = 0 ORDER BY waktu_masuk ASC");

if (mysqli_num_rows($query_antrian) > 0):
    while ($row = mysqli_fetch_assoc($query_antrian)): ?>
    <tr>
        <td class="ps-4 col-time-fixed">
            <?php
            $waktu_db = strtotime($row['waktu_masuk']);
        if (date('Y-m-d', $waktu_db) == date('Y-m-d')) {
            echo '<span class="text-muted small">Hari ini, </span>';
            echo '<span class="fw-bold">' . date('H:i', $waktu_db) . '</span>';
        } else {
            echo '<span class="text-date-old small">' . date('d M', $waktu_db) . '</span>';
            echo '<span class="text-date-old ms-1">(' . date('H:i', $waktu_db) . ')</span>';
        }
        ?>
        </td>
        <td><span class="fw-bold text-dark d-block"><?= htmlspecialchars($row['nomor_resi']) ?></span></td>
        <td>
        <select class="form-select form-select-sm border-0 bg-light fw-semibold text-secondary"
                onchange="updateEkspedisi(<?= $row['id'] ?>, this.value, this)"
                onfocus="stopRefresh()"
                onblur="startRefresh()"
                style="cursor: pointer; width: 160px;">
                <?php
            $list_kurir = ["J&T Express", "JNE", "SiCepat", "Shopee Xpress", "ID Express", "Ninja Xpress", "Anteraja", "Lion Parcel", "TIKI", "POS Indonesia", "Wahana", "Lainnya / Manual"];
        foreach ($list_kurir as $kurir) {
            $selected = ($row['ekspedisi'] == $kurir) ? 'selected' : '';
            echo "<option value='$kurir' $selected>$kurir</option>";
        }
        if (!in_array($row['ekspedisi'], $list_kurir)) {
            echo "<option value='" . $row['ekspedisi'] . "' selected>" . $row['ekspedisi'] . "</option>";
        }
        ?>
            </select>
        </td>
        <td class="text-center">
            <a href="proses_validasi.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm fw-bold px-3">VERIFIKASI</a>
        </td>
    </tr>
    <?php endwhile;
else: ?>
    <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada antrian barang.</td></tr>
<?php endif; ?>
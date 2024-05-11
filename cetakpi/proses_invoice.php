<?php
// Memeriksa apakah nomor dokumen telah dikirimkan dari halaman sebelumnya
if(isset($_POST['nomor_dokumen'])) {
    // Memuat koneksi ke database
    require_once('./data/mysql.php');

    // Mendapatkan nomor dokumen dari inputan
    $nomor_dokumen = $_POST['nomor_dokumen'];

    // Query untuk mendapatkan data invoice berdasarkan nomor dokumen
    $sql_invoice = "SELECT * FROM invoice_pembelian_header WHERE nofaktur = '$nomor_dokumen'";
    $result_invoice = $conn->query($sql_invoice);

    // Memeriksa apakah hasil query tidak kosong
    if ($result_invoice->num_rows > 0) {
        // Mendapatkan data invoice
        $row_invoice = $result_invoice->fetch_assoc();
        $tanggal_pembelian = $row_invoice['Tanggal'];
        $no_po = $row_invoice['NoPO'];
        $no_rg = $row_invoice['NoPenerimaan'];
        $top = $row_invoice['JatuhTempo'];
        $supplier = '';

        // Query untuk mendapatkan nama supplier berdasarkan KdSupplier
        $sql_supplier = "SELECT Nama FROM supplier WHERE KdSupplier = '{$row_invoice['KdSupplier']}'";
        $result_supplier = $conn->query($sql_supplier);

        // Memeriksa apakah hasil query tidak kosong
        if ($result_supplier->num_rows > 0) {
            // Mendapatkan nama supplier
            $row_supplier = $result_supplier->fetch_assoc();
            $supplier = $row_supplier['Nama'];
        }
        
        // Query untuk mendapatkan nama user yang melakukan edit
        $sql_edit_user = "SELECT EditUser FROM invoice_pembelian_header WHERE nofaktur = '$nomor_dokumen'";
        $result_edit_user = $conn->query($sql_edit_user);

        // Memeriksa apakah hasil query tidak kosong
        if ($result_edit_user->num_rows > 0) {
            // Mendapatkan nama user yang melakukan edit
            $row_edit_user = $result_edit_user->fetch_assoc();
            $edit_user = $row_edit_user['EditUser'];
        }

        // Query untuk mendapatkan data barang berdasarkan nomor dokumen
        $sql_barang = "SELECT
                    ipd.NoUrut,
                    mb.NamaLengkap AS NamaBarang,
                    ipd.Qty,
                    ipd.Harga,
                    (ipd.Qty * ipd.Harga) AS SubTotal
                FROM
                    invoice_pembelian_detail AS ipd
                JOIN
                    masterbarang AS mb ON ipd.kdbarang = mb.PCode
                WHERE
                    ipd.nofaktur = '$nomor_dokumen'";


        $result_barang = $conn->query($sql_barang);

        // Memeriksa apakah hasil query tidak kosong
        if ($result_barang->num_rows > 0) {
            // Menyiapkan placeholder untuk tabel data barang
            $data_barang = "";

            // Mendapatkan data barang
            while($row_barang = $result_barang->fetch_assoc()) {
                // Menggunakan number_format untuk menghilangkan digit nol di belakang koma
                $harga = number_format($row_barang['Harga'], 2);
                $sub_total = number_format($row_barang['SubTotal'], 2);

                // Menambahkan data barang ke placeholder
                $data_barang .= "<tr>
                                    <td>{$row_barang['NoUrut']}</td>
                                    <td>{$row_barang['NamaBarang']}</td>
                                    <td>{$row_barang['Qty']}</td>
                                    <td>{$harga}</td>
                                    <td>{$sub_total}</td>
                                </tr>";

                // Menghitung total
                $total += $row_barang['SubTotal'];
            }
        } else {
            // Jika hasil query barang kosong
            $data_barang = "<tr><td colspan='5'>Tidak ada data barang</td></tr>";
        }

        // Tutup koneksi ke database
        $conn->close();
    } else {
        // Jika nomor dokumen tidak ditemukan
        echo "Nomor dokumen tidak ditemukan";
        exit; // Menghentikan eksekusi skrip jika nomor dokumen tidak ditemukan
    }
} else {
    // Jika nomor dokumen tidak dikirimkan dari halaman sebelumnya
    echo "Nomor dokumen tidak tersedia";
    exit; // Menghentikan eksekusi skrip jika nomor dokumen tidak tersedia
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Cetak Invoice</title>
    <style>
        .breadcrumb-item {
            display: flex;
            align-items: center;
        }
        .breadcrumb-item.active {
            font-weight: 500;
            color: #6c757d;
            display: flex;
            align-items: center;
        }
        .breadcrumb-item.active i {
            margin-right: 0.3rem;
            font-size: 1.1rem;
            color: #6c757d;
        }
    
        table {
            width: 100%;
            border: 0;
            font-family: TimesNewRoman, Times New Roman, Times, Baskerville, Georgia, serif;
            font-size: 14px;
            font-style: normal;
            font-variant: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .btn_print img {
            opacity: 0.4;
            filter: alpha(opacity=40);
            /* For IE8 and earlier */
            cursor: pointer;
        }

        .btn_print img:hover {
            opacity: 1.0;
            filter: alpha(opacity=100);
            /* For IE8 and earlier */
        }

        .garis_putus {
            border-bottom: 1px dotted;
            margin: 10px 0px;
        }
    </style>
</head>

<body onload="window.print()">
    <table>
        <tr>
            <td colspan="100%"><hr size='2' noshade></td>
        </tr>
        <tr>
            <td colspan="100%">
                <b>
                    PT. BALI BOGA NATURA<br>
                    JL. LETJEN . S.PARMAN KAV.28, DESA TANJUNG DUREN SELATAN, KEC GROGOL<br>
                    PETAMBURAN, KOTA ADMINISTRASI JAKARTA BARAT<br>
                    Phone : +62 21 2933 9389
                </b>
            </td>
        </tr>
        <tr>
            <td colspan="100%" align="center">
                <b><u>Invoice Pembelian</u></b><br>
                <b>No : <?php echo $nomor_dokumen; ?></b>
            </td>
        </tr>   
        <tr>
            <td colspan="100%">&nbsp;</td>
        </tr>
        <tr>
            <td width="120">Tanggal</td>
            <td width="10">:</td>
            <td width="30%"><?php echo $tanggal_pembelian; ?></td>

            <td width="120">No PO</td>
            <td width="10">:</td>
            <td><?php echo $no_po; ?></td>
        </tr>
        <tr>
            <td width="120">Mata Uang</td>
            <td width="10">:</td>
            <td width="30%">Rupiah</td>

            <td width="120">No RG</td>
            <td width="10">:</td>
            <td><?php echo $no_rg; ?></td>
        </tr>

        <tr>
            <td width="120">Supplier</td>
            <td width="10">:</td>
            <td width="30%"><?php echo $supplier; ?></td>


            <td width="120">Jatuh Tempo</td>
            <td width="10">:</td>
            <td><?php echo $top; ?></td>
        </tr>
        <tr>
            <td colspan="100%">
                <table>
                    <thead>
                        <tr>
                            <td colspan="100%"><div class="garis_putus"></div></td>
                        </tr>
                        <tr>
                            <td width="30">No</td>
                            <!--<td width="100">PCode</td>-->
                            <td>Nama Barang</td>
                            <td width="75">Qty</td>
                            <td width="75">Harga</td>
                            <td width="75">SubTotal</td>
                        </tr>
                        <tr>
                            <td colspan="100%"><div class="garis_putus"></div></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $data_barang; ?>
                        <tr>
                            <td colspan="3" rowspan="4">Note : Note Dokumen</td>
                            <td>Jumlah</td>
                            <td align="right"><?php echo number_format($total, 2); ?></td>
                        </tr>

                        <tr>
                            <td>Disc</td>
                            <td align="right">0</td>
                        </tr>

                        <tr>
                            <td>PPn</td>
                            <td align="right">0</td>
                        </tr>

                        <tr>
                            <td>Total</td>
                            <td align="right"><?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="100%">&nbsp;</td>
        </tr>

        <!-- Tanda Tangan -->
        <tr>
            <td colspan="100%" align="center">
                <table>
                    <tr>
                        <td align="center">
                            Dibuat Oleh,
                            <br>
                            <br>
                            <br>
                            <br>
                            <?php echo $edit_user; ?><br>
                            <!-- Tanggal -->
                        </td>
                        <td align="center">
                            Disetujui Oleh,
                            <br>
                            <br>
                            <br>
                            <br>
                            windy<br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br><br><br>
    <h6>*)DOKUMEN INI TELAH DIAPPROVE OLEH SISTEM TIDAK DIPERLUKAN TANDA TANGAN</h6>
</body>
</html>

<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_kripto';

$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// Ambil data dari database
$sql = "SELECT * FROM data_user";
$result = $conn->query($sql);

// Fungsi dekripsi DES
function des_decrypt($data, $key, $iv)
{
    $cipher = "DES-CBC";
    $decryptedData = openssl_decrypt(base64_decode($data), $cipher, $key, OPENSSL_RAW_DATA, $iv);

    // Menghapus padding
    $padding = ord($decryptedData[strlen($decryptedData) - 1]);
    $decryptedData = substr($decryptedData, 0, -$padding);

    return $decryptedData;
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Data User</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <style>
        body {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .custom-container {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #ffffff;
            overflow-x: auto;
        }

        .custom-container table {
            width: 100%;
            table-layout: fixed;
        }

        .custom-container th,
        .custom-container td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container custom-container">
        <h2 class="text-center mb-4">Data User</h2>
        <?php
        if ($result->num_rows > 0) {
            echo '<table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>IV</th>
                        </tr>
                    </thead>
                    <tbody>';

            while ($row = $result->fetch_assoc()) {
                // Dekripsi data sebelum menampilkan
                $decryptedNama = des_decrypt($row['nama'], "secretpassword", hex2bin($row['iv']));
                $decryptedEmail = des_decrypt($row['email'], "secretpassword", hex2bin($row['iv']));
                $decryptedPassword = des_decrypt($row['password'], "secretpassword", hex2bin($row['iv']));

                echo '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . $decryptedNama . '</td>
                        <td>' . $decryptedEmail . '</td>
                        <td>' . $decryptedPassword . '</td>
                        <td>' . $row['iv'] . '</td>
                    </tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p class="text-center">Tidak ada data user.</p>';
        }
        ?>
    </div>

    <div class="fixed-bottom text-center mt-4 p-3">
        <a href="home.php" class="btn btn-secondary">Kembali ke Halaman Utama</a>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>

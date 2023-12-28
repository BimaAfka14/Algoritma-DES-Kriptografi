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

function generateRandomIV()
{
    // Menghasilkan vektor inisialisasi secara acak dengan panjang 8 byte
    return openssl_random_pseudo_bytes(8);
}

function des_encrypt($data, $key, $iv)
{
    $cipher = "DES-CBC"; // DES in CBC mode
    $blockSize = openssl_cipher_iv_length($cipher);

    // Padding
    $pad = $blockSize - (strlen($data) % $blockSize);
    $data .= str_repeat(chr($pad), $pad);

    // Enkripsi
    $encryptedData = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);

    return base64_encode($encryptedData);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data dari formulir
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kunci
    $key = "secretpassword"; // Ganti dengan kunci rahasia yang lebih kuat

    // Menghasilkan IV secara otomatis
    $iv = generateRandomIV();

    // Menampilkan IV dalam format heksadesimal
    $ivHex = bin2hex($iv);

    // Enkripsi data
    $encryptedNama = des_encrypt($nama, $key, $iv);
    $encryptedEmail = des_encrypt($email, $key, $iv);
    $encryptedPassword = des_encrypt($password, $key, $iv);

    // Simpan data ke database
    $sql = "INSERT INTO data_user (nama, email, password, iv) VALUES ('$encryptedNama', '$encryptedEmail', '$encryptedPassword', '$ivHex')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Data berhasil disimpan ke database.');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Form Pengisian Data User</title>
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
        }
    </style>
</head>

<body>
    <div class="container custom-container">
        <h2 class="text-center mb-4">Form Pengisian Data User</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" required />
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required />
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">Show</button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mx-auto d-block">
                Submit
            </button>
        </form>
    </div>

    <div class="fixed-bottom text-center mt-4 p-3">
        <a href="home.php" class="btn btn-secondary">Kembali ke Halaman Utama</a>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('togglePassword');

            togglePasswordButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePasswordButton.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        });
    </script>
</body>

</html>

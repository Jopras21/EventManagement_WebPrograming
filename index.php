<?php
$dsn = "mysql:host=localhost;dbname=umn_genap2122_pemweb_w6";
$conn = new PDO($dsn, "root", "");

$sql = "SELECT * FROM mahasiswa";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        a {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            margin-bottom: 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        img {
            border-radius: 10px;
        }

        td a {
            color: #ffffff;
            text-decoration: none;
            margin-right: 10px;
        }

        td a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table {
                font-size: 14px;
            }

            td, th {
                padding: 8px;
            }

            a {
                padding: 8px 15px;
            }
        }
    </style>
</head>
<body>
    <h1>Data Mahasiswa</h1>
    <a href="add.php">Tambah Mahasiswa</a>
    <table border="1">
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Foto</th>
            <th>Tindakan</th>
        </tr>
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?php echo $row['nim']; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['prodi']; ?></td>
            <td><img src="Foto/<?php echo $row['foto']; ?>" width="100" /></td>
            <td>
                <a href="edit.php?nim=<?php echo $row['nim']; ?>">Edit</a> 
                
                <a href="index.php?delete=<?php echo $row['nim']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
    if (isset($_GET['delete'])) {
        $nim = $_GET['delete'];
        
        $sql = "DELETE FROM mahasiswa WHERE nim = :nim";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nim' => $nim]);
        
        header('Location: index.php');
    }
    ?>
</body>
</html>

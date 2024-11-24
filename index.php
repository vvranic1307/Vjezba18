<?php
// Povezivanje s bazom podataka
$host = "localhost";
$username = "root"; // Vaše korisničko ime za MySQL
$password = ""; // Lozinka (prazno za XAMPP bez lozinke)
$database = "vjezba17"; // Naziv baze

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Pogreška pri povezivanju s bazom: " . $conn->connect_error);
}

// Ažuriranje korisnika
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $country_id = $conn->real_escape_string($_POST['country_id']);

    $sql = "UPDATE users SET name='$name', lastname='$lastname', country_id='$country_id' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<div class ='success'>Korisnik je uspješno ažuriran!</div>";
    } else {
        echo "<div class ='error'>Pogreška pri ažuriranju: " . $conn->error . "</div>";
    }
}

// Dohvat korisnika i država
$sql_users = "SELECT users.id, users.name, users.lastname, users.country_id, countries.country_name 
              FROM users
              INNER JOIN countries ON users.country_id = countries.id";
$result_users = $conn->query($sql_users);

$sql_countries = "SELECT * FROM countries";
$result_countries = $conn->query($sql_countries);
$countries = [];
while ($row = $result_countries->fetch_assoc()) {
    $countries[] = $row;
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uređivanje korisnika</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        form {
            margin-bottom: 0;
        }
        select, input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            text-align: center;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .sucess {
            margin-top: 20px; 
            font-weight: bold;
            color: green;
            text-align: center;
        }
        .error{
            margin-top: 20px; 
            font-weight: bold; 
            color: red; 
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Uređivanje korisnika</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Država</th>
                <th>Uredi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_users->num_rows > 0): ?>
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                        </td>
                        <td>
                                <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>">
                        </td>
                        <td>
                                <select name="country_id">
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['id']; ?>" <?php echo $user['country_id'] == $country['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['country_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                        </td>
                        <td>
                                <button type="submit">Spremi</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nema korisnika u bazi podataka.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>

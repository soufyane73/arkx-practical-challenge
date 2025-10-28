<?php
// Database connection
$dsn = "mysql:host=localhost;dbname=bookstore";
$username = "root";
$password = "";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// ====== Handle Form Submission ======
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ADD AUTHOR
    if (isset($_POST["add_author"])) {
        $name = $_POST["name"];
        $country = $_POST["country"];
        $stmt = $conn->prepare("INSERT INTO Authors (Name, Country) VALUES (?, ?)");
        $stmt->execute([$name, $country]);
    }

    // ADD BOOK
    if (isset($_POST["add_book"])) {
        $title = $_POST["title"];
        $year = $_POST["year"];
        $author_id = $_POST["author_id"];
        $stmt = $conn->prepare("INSERT INTO Books (Title, published_year, author_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $year, $author_id]);
    }

    // UPDATE BOOK YEAR
    if (isset($_POST["update_year"])) {
        $book_id = $_POST["book_id"];
        $new_year = $_POST["new_year"];
        $stmt = $conn->prepare("UPDATE Books SET published_year = ? WHERE book_id = ?");
        $stmt->execute([$new_year, $book_id]);
    }

    // DELETE BOOK
    if (isset($_POST["delete_book"])) {
        $book_id = $_POST["book_id"];
        $stmt = $conn->prepare("DELETE FROM Books WHERE book_id = ?");
        $stmt->execute([$book_id]);
    }
}

// ====== Fetch Data for Display ======
$authors = $conn->query("SELECT * FROM Authors")->fetchAll(PDO::FETCH_ASSOC);
$books = $conn->query("SELECT Books.book_id, Books.Title, Books.published_year, Authors.Name AS author_name 
                       FROM Books 
                       JOIN Authors ON Books.author_id = Authors.author_id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookstore Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f5f5f5; }
        h2 { color: #333; }
        form { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
        input, select { padding: 5px; margin: 5px 0; width: 100%; }
        button { padding: 8px 15px; background: #007BFF; border: none; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; background: #fff; border-radius: 10px; overflow: hidden; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007BFF; color: white; }
    </style>
</head>
<body>

<h1>Bookstore Management</h1>

<!-- Add Author Form -->
<form method="POST">
    <h2>Add Author</h2>
    <input type="text" name="name" placeholder="Author Name" required>
    <input type="text" name="country" placeholder="Country" required>
    <button type="submit" name="add_author">Add Author</button>
</form>

<!-- Add Book Form -->
<form method="POST">
    <h2>Add Book</h2>
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="number" name="year" placeholder="Published Year" required>
    <select name="author_id" required>
        <option value="">Select Author</option>
        <?php foreach ($authors as $a): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['Name']) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="add_book">Add Book</button>
</form>

<!-- Books Table -->
<h2>All Books</h2>
<table>
    <tr>
        <th>ID</th><th>Title</th><th>Year</th><th>Author</th><th>Actions</th>
    </tr>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= htmlspecialchars($b['Title']) ?></td>
            <td><?= $b['published_year'] ?></td>
            <td><?= htmlspecialchars($b['author_name']) ?></td>
            <td>
                <!-- Update Year -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                    <input type="number" name="new_year" placeholder="New Year" required>
 <button type="submit" name="update_year">Update</button>
                </form>
                <!-- Delete Book -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                    <button type="submit" name="delete_book" style="background:red;">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>

<!-- filepath: c:\xampp\htdocs\sentiment-project\php-frontend\crud.php -->
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "otp";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM sentiment_results WHERE id=$id") or die($conn->error);
    header("Location: crud.php");
    exit();
}

// Handle Update Request
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $text = $_POST['text'];
    $textblob_sentiment = $_POST['textblob_sentiment'];
    $vader_sentiment = $_POST['vader_sentiment'];
    $vader_scores = $_POST['vader_scores'];

    $conn->query("UPDATE sentiment_results SET text='$text', textblob_sentiment='$textblob_sentiment', vader_sentiment='$vader_sentiment', vader_scores='$vader_scores' WHERE id=$id") or die($conn->error);
    header("Location: crud.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment CRUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Sentiment Results Management</h1>

        <!-- Display Sentiment Results -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Text</th>
                    <th>TextBlob Sentiment</th>
                    <th>VADER Sentiment</th>
                    <th>VADER Scores</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM sentiment_results") or die($conn->error);
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['text']; ?></td>
                    <td><?php echo $row['textblob_sentiment']; ?></td>
                    <td><?php echo $row['vader_sentiment']; ?></td>
                    <td><?php echo $row['vader_scores']; ?></td>
                    <td>
                        <a href="crud.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="crud.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Edit Form -->
        <?php if (isset($_GET['edit'])): ?>
        <?php
        $id = $_GET['edit'];
        $result = $conn->query("SELECT * FROM sentiment_results WHERE id=$id") or die($conn->error);
        $row = $result->fetch_assoc();
        ?>
        <div class="card mt-4">
            <div class="card-body">
                <h3 class="text-center">Edit Sentiment Result</h3>
                <form method="post" action="crud.php">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="mb-3">
                        <label for="text" class="form-label">Text</label>
                        <input type="text" class="form-control" id="text" name="text" value="<?php echo $row['text']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="textblob_sentiment" class="form-label">TextBlob Sentiment</label>
                        <input type="text" class="form-control" id="textblob_sentiment" name="textblob_sentiment" value="<?php echo $row['textblob_sentiment']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="vader_sentiment" class="form-label">VADER Sentiment</label>
                        <input type="text" class="form-control" id="vader_sentiment" name="vader_sentiment" value="<?php echo $row['vader_sentiment']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="vader_scores" class="form-label">VADER Scores</label>
                        <textarea class="form-control" id="vader_scores" name="vader_scores" rows="3" required><?php echo $row['vader_scores']; ?></textarea>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
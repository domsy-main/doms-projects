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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .result-card {
            margin-top: 20px;
        }
        .form-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        .form-section .form-container {
            flex: 1;
            min-width: 300px;
        }
        .form-section .image-container {
            flex: 1;
            text-align: center;
        }
        .form-section .image-container img {
            max-width: 80%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Let us know your feedback!</h1>
        <div class="form-section">
            <!-- Form Section -->
            <div class="form-container">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="text" class="form-label">Enter a sentence:</label>
                                <input type="text" class="form-control" id="text" name="text" placeholder="Type your text here..." required>
                            </div>
                            <button type="submit" name="analyze" class="btn btn-primary w-100">Send Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Image Section -->
            <div class="image-container">
                <img src="doms.png" alt="Feedback Illustration">
            </div>
        </div>

        <!-- Message Section -->
        <div class="text-center mt-4">
            <a href="crud.php" class="btn btn-secondary">Manage Sentiments</a>
        </div>

        <?php
        if (isset($_POST['analyze'])) {
            $text = $_POST['text'];
            $data = json_encode(["text" => $text]);

            // Call the API
            $ch = curl_init('http://127.0.0.1:8000/analyze/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            $vader_scores_json = json_encode($result['vader_scores']); // Fix: Store JSON-encoded value in a variable
            $stmt = $conn->prepare("INSERT INTO sentiment_results (text, textblob_sentiment, vader_sentiment, vader_scores) VALUES (?, ?, ?, ?)");
            $stmt->bind_param(
                "ssss",
                $text,
                $result['textblob_sentiment'],
                $result['vader_sentiment'],
                $vader_scores_json // Pass the variable
            );
            $stmt->execute();
            $stmt->close();
            ?>
            <div class="card shadow-sm result-card">
                <div class="card-body">
                    <h3 class="text-center">Analysis Results</h3>
                    <?php
                    // Determine the message based on the VADER sentiment
                    if ($result['vader_sentiment'] === "Negative") {
                        echo '<p class="text-center text-danger"><strong>We’re sorry to hear that. It seems like something might not be going well. Please let us know if there’s anything we can do to help. The sentiment of the text is negative.</strong></p>';
                    } elseif ($result['vader_sentiment'] === "Positive") {
                        echo '<p class="text-center text-success"><strong>That’s wonderful to hear! It sounds like things are going great. Keep up the positivity! The sentiment of the text is positive.</strong></p>';
                    } else {
                        echo '<p class="text-center text-secondary"><strong>Thank you for sharing. The text seems neutral, which might indicate a balanced or factual tone. The sentiment of the text is neutral.</strong></p>';
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

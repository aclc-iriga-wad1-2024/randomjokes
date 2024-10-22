<?php
/**
 * jokes: Jokes page
 */

// configuration
require_once __DIR__ . '/config/database.php';
if(!isset($conn)) exit();

// initialize global data
$joke = '';

// get passed email and code
$email = isset($_GET['email']) ? $_GET['email'] : '';
$code  = isset($_GET['code'])  ? $_GET['code']  : '';

// process passed email and code
if(!empty($email) && !empty($code))
{
    // query email and code from database
    $stmt = $conn->prepare("SELECT * FROM `emails` WHERE `email` = ? AND `code` = ? ORDER BY `id` DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // query a random joke if the code is not yet expired
        if(time() <= strtotime($row['expires_at'])) {
            $stmt = $conn->prepare("SELECT `joke` FROM `jokes` ORDER BY RAND() LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                $row2 = $result->fetch_assoc();
                $joke = $row2['joke'];
            }
        }
    }
}

// if joke is still empty, redirect to homepage
if(empty($joke))
{
    header('location: index.php');
    exit();
}
?>

<!-- html top -->
<?php require_once __DIR__ . '/partials/html-1-top.php'; ?>

<!-- main content -->
<main class="card shadow-sm" style="width: 100%; max-width: 500px;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="opacity-50"><?= $email ?></span>
        <a class="btn btn-link text-decoration-none opacity-75" href="index.php">Home</a>
    </div>
    <div class="card-body">
        <div class="mb-3 text-center">
            <h2 class="card-title"><span class="text-primary">RandomJokes</span></h2>
        </div>
        <p class="display-6" style="font-size: 1.6rem;"><?= htmlspecialchars($joke) ?></p>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center bg-white">
        <a class="btn btn-primary" href="jokes.php?email=<?= $email ?>&code=<?= $code ?>">More Jokes</a>
    </div>
</main>

<!-- html bottom -->
<?php require_once __DIR__ . '/partials/html-2-bot.php'; ?>
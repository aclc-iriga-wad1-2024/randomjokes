<?php
/**
 * index: Homepage
 */

// configuration
require_once __DIR__ . '/config/database.php';
if(!isset($conn)) exit();

// initialize global data
$success = [ 'email' => '' ];
$error   = [ 'email' => '' ];

// when an email address is submitted
if(isset($_POST['email']))
{
    // get submitted data
    $email = trim($_POST['email']);

    // validate submitted email
    if(empty($email)) {
        $error['email'] = 'Email address is required.';
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Invalid email address.';
    }

    // email is valid, proceed..
    else {
        // generate code and link to jokes page
        require_once __DIR__ . '/helpers/generate_code.php';
        $code = generate_code();
        $link = 'http://localhost/randomjokes/jokes.php?email=' . $email . '&code=' . $code;
        $code_expiration = date('Y-m-d H:i:s', time() + (86400 * 30));

        // insert to database
        $stmt = $conn->prepare("INSERT INTO `emails`(`email`, `code`, `expires_at`) VALUES(?, ?, ?)");
        $stmt->bind_param("sss", $email, $code, $code_expiration);
        $stmt->execute();

        // generate subject and html body of email to be sent
        $subject = 'Confirm Your RandomJokes Access';
        $body  = '<p>Hello,</p>';
        $body .= '<p>';
        $body .= 'We noticed your email was used to request access to <b>RandomJokes!</b> ';
        $body .= 'To start enjoying jokes anytime, simply click the link below:';
        $body .= '</p>';
        $body .= '<p><b><a href="' . $link . '">' . $link . '</a></b></p>';
        $body .= '<p><b>Please note:</b> This link is valid until <b>' . $code_expiration . '</b></p>';
        $body .= '<p>If you didn\'t make this request, feel free to ignore this message.</p>';
        $body .= '<p>Enjoy the laughs!</p>';
        $body .= '<br>';
        $body .= '<p>Best regards,<br>The RandomJokes Team</p>';

        // send the email
        require_once __DIR__ . '/helpers/send_email.php';
        $response = send_email($email, $subject, $body);
        if($response['success'])
            $success['email'] = 'We have sent an email to <i><b>' . $email . '</b></i> containing your link to access the jokes.';
        else
            $error['email'] = $response['error'];
    }
}
?>

<!-- html top -->
<?php require_once __DIR__ . '/partials/html-1-top.php'; ?>

<!-- main content -->
<main class="card shadow-sm" style="width: 100%; max-width: 500px;">
    <div class="card-body">
        <div class="mb-3 text-center">
            <h2 class="card-title">Get Access to <span class="text-primary">RandomJokes</span></h2>
            <p class="card-text">Enter your email to access hilarious jokes anytime!</p>
        </div>

        <form action="index.php" method="POST">
            <!-- email input -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="newuser@localhost.net" required>
                <label for="email">Email address</label>
            </div>

            <!-- success message (if any) -->
            <?php if(!empty($success['email'])) { ?>
                <div class="alert alert-success py-2 mb-3">
                    <?= $success['email'] ?>
                </div>
            <?php } ?>

            <!-- error message (if any) -->
            <?php if(!empty($error['email'])) { ?>
                <div class="alert alert-danger py-2 mb-3 d-flex justify-content-start">
                    <?= $error['email'] ?>
                </div>
            <?php } ?>

            <!-- submit button -->
            <button type="submit" class="btn btn-primary btn-lg w-100">Get Jokes</button>
        </form>

        <div class="mt-3 text-center">
            <p class="text-muted small">We'll send you a link to access the jokes!</p>
        </div>
    </div>
</main>

<!-- html bottom -->
<?php require_once __DIR__ . '/partials/html-2-bot.php'; ?>
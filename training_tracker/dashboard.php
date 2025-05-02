<?php include 'includes/header.php'; ?>


<h2>Καλώς ήρθες, <?php echo $_SESSION["username"]; ?>!</h2>
<p>Επίπεδο: <?php echo $_SESSION["level"]; ?></p>
<p>XP: <?php echo $_SESSION["xp"]; ?></p>

<div class="progress-bar">
  <div style="width: <?php echo ($_SESSION["xp"] % 100); ?>%;"></div>
</div>

<?php include 'includes/footer.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
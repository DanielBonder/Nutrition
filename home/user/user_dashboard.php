<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header(header: "Location: ../../login/login.php");
    exit;
}

require_once '../../admin/db.php';
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

$plans = [];
$planResults = $conn->query(query: "SELECT id, name, price FROM payment_plans ORDER BY duration_months ASC");
while ($row = $planResults->fetch_assoc()) {
    $plans[] = $row;
}

$meal_types = ['בוקר', 'ביניים1', 'צהריים', 'ביניים2', 'ערב', 'לפני שינה'];
$days = ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'];

if (isset($_GET['day']) || isset($_GET['meal_type'])) {
    $_SESSION['active_section'] = 'menuSection';
    $_SESSION['selected_day'] = $_GET['day'] ?? '';
    $_SESSION['selected_meal_type'] = $_GET['meal_type'] ?? '';
    header("Location: user_dashboard.php");
    exit;
}

$selected_day = $_SESSION['selected_day'] ?? '';
$selected_meal_type = $_SESSION['selected_meal_type'] ?? '';
unset($_SESSION['selected_day'], $_SESSION['selected_meal_type']);

$active_section = $_SESSION['active_section'] ?? '';
unset($_SESSION['active_section']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plan'])) {
    $plan_id = (int)$_POST['plan_id'];

    // שלוף את הסכום מהתוכנית
    $plan_stmt = $conn->prepare("SELECT price FROM payment_plans WHERE id = ?");
    $plan_stmt->bind_param("i", $plan_id);
    $plan_stmt->execute();
    $plan_stmt->bind_result($price);
    $plan_stmt->fetch();
    $plan_stmt->close();

    if ($price) {
        // הגדר תאריך יעד לתשלום (למשל 7 ימים מהיום)
        $due_date = date('Y-m-d', strtotime('+7 days'));

        $stmt = $conn->prepare("INSERT INTO payments (user_id, plan_id, due_date, amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $user_id, $plan_id, $due_date, $price);
        $stmt->execute();
        $stmt->close();

        $_SESSION['payment_message'] = "✅ בקשת התשלום נרשמה בהצלחה!";
        $_SESSION['active_section'] = 'paymentSection';
        header("Location: user_dashboard.php");
        exit;
    } else {
        $_SESSION['payment_message'] = "❌ שגיאה: לא נמצאה תוכנית.";
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consumed'])) {
    $day = $_POST['day'];
    $meal_type = $_POST['meal_type'];
    $actual = trim($_POST['actual']);

    $stmt = $conn->prepare("REPLACE INTO user_meals_actual (user_id, day_of_week, meal_type, actual) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $day, $meal_type, $actual);
    $stmt->execute();
    $stmt->close();

    $_SESSION['meal_message'] = "✅ הארוחה נשמרה בהצלחה!";
    $_SESSION['active_section'] = 'menuSection';
    header(header: "Location: user_dashboard.php"); 
    exit;
}

$weekly_menu = [];
$sql = "SELECT day_of_week, meal_type, description FROM user_weekly_menus WHERE user_id = $user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $weekly_menu[$row['day_of_week']][$row['meal_type']] = $row['description'];
}

$actual_meals = [];
$sql = "SELECT day_of_week, meal_type, actual, comment, created_at FROM user_meals_actual WHERE user_id = $user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $actual_meals[$row['day_of_week']][$row['meal_type']] = [
        'text' => $row['actual'],
        'comment' => $row['comment'],
        'time' => $row['created_at']
    ];
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>אזור אישי</title>
    <link rel="stylesheet" href="../../assets/css/user_css/user_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Suez+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Attraction&display=swap" rel="stylesheet">
<!-- Bootstrap 5 (RTL אם צריך) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
<!-- Bootstrap Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

</head>
<body data-active-section="<?= $active_section ?>">

<div class="welcome-banner">
    <h2>שלום <?= htmlspecialchars($full_name) ?>, ברוך הבא לאזור האישי שלך</h2>
</div>
<nav class="navbar navbar-expand-lg bg-light shadow-sm px-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">
      <img src="../../assets/images/logo2.png" alt="לוגו" height="80">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="תפריט">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-bold">
        <li class="nav-item"><a class="nav-link" href="../../index.php">בית</a></li>
        <li class="nav-item"><a class="nav-link" href="#">נעים להכיר</a></li>
        <li class="nav-item"><a class="nav-link" href="#">תוכניות</a></li>
        <li class="nav-item"><a class="nav-link" href="#">תשאלו אותם</a></li>
        <li class="nav-item"><a class="nav-link" href="../../home/price/price.php">תפריטים ועוד</a></li>

        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item"><a class="nav-link" href="user_dashboard.php">שלום, <?= htmlspecialchars($_SESSION['username']) ?></a></li>
          <li class="nav-item"><a class="nav-link" href="../../login/logout.php">התנתקות</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../../login/login.html">התחברות</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- סיידבר לדסקטופ בלבד -->
<nav class="d-none d-md-block bg-light sidebar p-4">
  <div class="d-flex flex-row justify-content-center flex-wrap gap-3">
    <button class="btn btn-outline-danger" data-section="appointmentsSection">📅 פגישות</button>
    <button class="btn btn-outline-danger" data-section="menuSection">🍽️ תפריט</button>
    <button class="btn btn-outline-danger" data-section="paymentSection">💳 תשלום</button>
  </div>
</nav>

<!-- ניווט רספונסיבי לנייד בלבד -->
<div class="d-md-none bg-light border-bottom py-2 px-3 text-center sticky-top">
  <div class="btn-group w-100" role="group">
    <button class="btn btn-outline-danger" data-section="appointmentsSection">📅</button>
    <button class="btn btn-outline-danger" data-section="menuSection">🍽️</button>
    <button class="btn btn-outline-danger" data-section="paymentSection">💳</button>
  </div>
</div>




<div id="pageOverlay" class="overlay"></div>

<?php if (isset($_SESSION['meal_message'])): ?>
    <div class="message"><?= $_SESSION['meal_message'] ?></div>
    <?php unset($_SESSION['meal_message']); ?>
<?php endif; ?>





<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<div id="appointmentsSection" style="margin-top: 20px;">
  <section>
    <h3>📅 הפגישות שלך:</h3>

    <div class="appointments-list">
      <?php
      $result = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id ORDER BY available_date ASC");
      if ($result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
              $type = $row['meeting_type'] === 'initial' ? 'פגישה ראשונית' : 'שקילה שבועית';
              $date = date("d/m/Y", strtotime($row['available_date']));
              $time = substr($row['available_time'], 0, 5);
              echo "<div class='appointment-item'>בתאריך $date בשעה $time ($type)</div>";
          endwhile;
      else:
          echo "<div class='no-appointments'>אין פגישות מתוכננות.</div>";
      endif;
      ?>
    </div>

    <div class="appointments-link">
      <a href="user_appointments.php" class="link-button">📅 קבע פגישה נוספת</a>
    </div>
  </section>
</div>


<div id="menuSection" style="margin-top: 20px;">
    <section>
      <h3>🍽️ התפריט השבועי שלך:</h3>
<form method="GET" action="user_dashboard.php" onsubmit="setMenuSection()" class="filter-form">
  <div class="form-group">
    <label for="day-select">סנן לפי יום:</label>
    <select name="day" id="day-select" class="custom-select">
      <option value="">-- הצג הכל --</option>
      <?php foreach ($days as $day): ?>
        <option value="<?= htmlspecialchars($day) ?>" <?= ($day === $selected_day ? 'selected' : '') ?>>
          <?= htmlspecialchars($day) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="meal-type-select">סנן לפי סוג ארוחה:</label>
    <select name="meal_type" id="meal-type-select" class="custom-select">
      <option value="">-- הצג הכל --</option>
      <?php foreach ($meal_types as $type): ?>
        <option value="<?= htmlspecialchars($type) ?>" <?= ($type === $selected_meal_type ? 'selected' : '') ?>>
          <?= htmlspecialchars($type) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="filter-button">🔍 סנן</button>
</form>


<?php foreach ($days as $day):
  if ($selected_day && $day !== $selected_day) continue;
?>
  <div class="day-block">
    <h4>📆 יום <?= $day ?>:</h4>
    <ul class="meal-list">
    <?php foreach ($meal_types as $type):
      if ($selected_meal_type && $type !== $selected_meal_type) continue;

      $desc = $weekly_menu[$day][$type] ?? '';
      $existing = $actual_meals[$day][$type]['text'] ?? '';
      $updated_at = $actual_meals[$day][$type]['time'] ?? null;
      $comment = $actual_meals[$day][$type]['comment'] ?? '';
    ?>
      <li class="meal-item">
        <strong><?= $type ?>:</strong> <?= htmlspecialchars($desc) ?>
        <form method="POST" class="meal-form">
          <input type="hidden" name="day" value="<?= $day ?>">
          <input type="hidden" name="meal_type" value="<?= $type ?>">
          <textarea name="actual" rows="2" placeholder="מה אכלת בפועל?"><?= htmlspecialchars($existing) ?></textarea>
          <?php if ($updated_at): ?>
            <small>עודכן לאחרונה: <?= date("d/m/Y H:i", strtotime($updated_at)) ?></small><br>
          <?php endif; ?>
          <?php if ($comment !== ''): ?>
            <div class="meal-comment">📝 הערת המנהל: <?= htmlspecialchars($comment) ?></div>
          <?php endif; ?>
          <button type="submit" name="consumed" class="submit-button">
            <?= $existing ? '🔄 עדכן' : '📩 שמור' ?>
          </button>
        </form>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
<?php endforeach; ?>

    </section>
</div>

<div id="paymentSection" style="margin-top: 20px;">
  <section>
    <h3>💳 מצב תשלום:</h3>
    <?php
    if (isset($_SESSION['payment_message'])) {
        echo "<div class='message'>" . $_SESSION['payment_message'] . "</div>";
        unset($_SESSION['payment_message']);
    }

    $sql = "SELECT due_date, amount, status, paid_at, request_status 
            FROM payments 
            WHERE user_id = $user_id 
            ORDER BY due_date ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $due_date = date("d/m/Y", strtotime($row['due_date']));
            $amount = number_format($row['amount'], 2) . " ₪";

            if ($row['request_status'] === 'בהמתנה') {
                $status = 'בהמתנה לאישור';
            } elseif ($row['request_status'] === 'נדחה') {
                $status = 'נדחה';
            } elseif ($row['status'] === 'שולם') {
                $status = 'שולם';
            } else {
                $status = 'לא שולם';
            }

            echo "<p>לתשלום עד: $due_date - סכום: $amount - סטטוס: $status";
            if ($row['status'] === 'שולם' && $row['paid_at']) {
                echo " בתאריך: " . date("d/m/Y", strtotime($row['paid_at']));
            }
            echo "</p>";
        endwhile;
    else:
        echo "<p>אין דרישות תשלום כרגע.</p>";
    endif;
    ?>

    <h4>📌 בחר תוכנית תשלום:</h4>
    <form method="POST" action="user_dashboard.php">
        <select name="plan_id" required>
            <option value="">-- בחר תוכנית --</option>
            <?php foreach ($plans as $plan): ?>
                <option value="<?= $plan['id'] ?>">
                    <?= htmlspecialchars($plan['name']) ?> - <?= number_format($plan['price'], 2) ?> ₪
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="submit_plan">📩 בקש תוכנית</button>
    </form>
  </section>
</div>

</main> <!-- ← סגירת main מגיעה כאן -->
<script src="../../assets/js/user_dashboard.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>


</body>
</html>

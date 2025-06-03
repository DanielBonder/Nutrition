<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>בחירת תוכנית</title>
  <link href="https://fonts.googleapis.com/css2?family=Suez+One&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Suez One', serif;
      background: linear-gradient(to right, blanchedalmond, lightpink);
      margin: 0;
      padding: 0;
      min-height: 100vh;
    }

    .back-home {
      text-align: center;
      margin-top: 2rem;
    }

    .home-button {
      background: white;
      border: 2px solid black;
      padding: 10px 20px;
      border-radius: 50px;
      text-decoration: none;
      color: black;
      font-weight: bold;
    }

    .plan-container {
      background-color: white;
      padding: 2rem;
      border-radius: 2rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      margin: 2rem auto;
      text-align: center;
    }

    h2 {
      color: crimson;
      margin-bottom: 1.5rem;
    }

    .custom-select {
      position: relative;
      width: 100%;
      text-align: right;
    }

    .select-selected {
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 2rem;
      padding: 12px 20px;
      cursor: pointer;
    }

    .select-items {
      position: absolute;
      background-color: white;
      border: 1px solid #ccc;
      border-top: none;
      z-index: 99;
      top: 100%;
      left: 0;
      right: 0;
      border-radius: 0 0 2rem 2rem;
      display: none;
      max-height: 200px;
      overflow-y: auto;
    }

    .select-items div {
      padding: 12px 20px;
      cursor: pointer;
    }

    .select-items div:hover {
      background-color: #f1f1f1;
    }

    #price-result {
      font-size: 18px;
      font-weight: bold;
      margin-top: 1rem;
      margin-bottom: 1rem;
      color: #333;
    }

    #purchase-btn {
      background-color: deeppink;
      color: white;
      font-weight: bold;
      border-radius: 2rem;
      padding: 12px 20px;
      border: none;
      font-size: 16px;
      cursor: pointer;
      display: none;
    }

    #purchase-btn:hover {
      background-color: hotpink;
    }
  </style>
</head>
<body>

  <div class="back-home">
    <a href="../../index.php" class="home-button">חזרה לדף הבית</a>
  </div>

  <div class="plan-container">
    <h2>בחר תוכנית:</h2>

    <div class="custom-select" id="plan-select">
      <div class="select-selected">-- בחר תוכנית --</div>
      <div class="select-items">
        <div data-value="650">ליווי חודש - ₪650</div>
        <div data-value="1350">ליווי 3 חודשים - ₪1350</div>
        <div data-value="2400">ליווי 6 חודשים - ₪2400</div>
        <div data-value="450">תפריט בלבד - ₪450 (חד פעמי, ללא ליווי)</div>
      </div>
    </div>

    <p id="price-result"></p>

    <button id="purchase-btn" onclick="purchase()">המשך לרכישה ב־WhatsApp</button>
  </div>

  <script src="../../assets/js/price.js"></script>
</body>
</html>

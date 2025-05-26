<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 載入 PHPMailer
require 'PHPMailer/PHPMailer-master/src/Exception.php';
require 'PHPMailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/src/SMTP.php';
// require 'PHPMailer/src/PHPMailer.php'; // 手動安裝用這行
// require 'PHPMailer/src/SMTP.php';
// require 'PHPMailer/src/Exception.php';

function sendMailWithImage($to, $name, $imagePath) {
    $mail = new PHPMailer(true);

    try {
        // 設定 SMTP（這邊以 Gmail 為例）
        $mail->CharSet = 'UTF-8';               // 指定信件內容編碼
        $mail->Encoding = 'base64';             // 建議用 base64 編碼中文內容
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'a1123314@mail.nuk.edu.tw'; // 改成你自己的
        $mail->Password = 'cfzi viks wmom nkqu'; // 要用 Gmail 應用程式密碼
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('a1123314@mail.nuk.edu.tw', '網站註冊系統');
        $mail->addAddress($to, $name);

        // 嵌入圖片
        $mail->AddEmbeddedImage($imagePath, 'uploadedImage');

        $mail->isHTML(true);
        $mail->Subject = '🎉 註冊成功通知';
        $mail->Body = "
            <h2>歡迎你，$name！</h2>
            <p>你已成功註冊，以下是你的資訊：</p>
            <ul>
                <li><strong>Email：</strong>$to</li>
            </ul>
            <p>你上傳的照片如下：</p>
            <img src='cid:uploadedImage' width='200'>
        ";

        $mail->send();
        echo "✅ Email 寄送成功！<br>";
    } catch (Exception $e) {
        echo "❌ Email 寄送失敗: {$mail->ErrorInfo}";
    }
}

// 表單處理
if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_FILES["file"])) {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir);
    }

    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "<h2>以下是你的資訊：</h2>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>No</th><th>Name</th><th>Email</th><th>Photo</th></tr>";
        echo "<tr>";
        echo "<td>1</td>";
        echo "<td>$name</td>";
        echo "<td>$email</td>";
        echo "<td><img src='$target_file' width='100'></td>";
        echo "</tr>";
        echo "</table>";

        // 發送帶圖片的 email
        sendMailWithImage($email, $name, $target_file);
    } else {
        echo "<p>📛 照片上傳失敗。</p>";
        // 可選：寄送註冊失敗通知
    }

    echo "<br><a href='sign.php'>← 返回註冊頁面</a>";
} else {
    echo "<p>📛 表單資料不完整，請重新輸入。</p>";
    echo "<br><a href='sign.php'>← 返回註冊頁面</a>";
}
?>

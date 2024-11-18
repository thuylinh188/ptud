<?php
include 'includes/header.php'; // Header chung
include 'includes/db.php'; // Kết nối cơ sở dữ liệu

// Xử lý form liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $errors = [];

    // Kiểm tra dữ liệu đầu vào
    if (empty($name)) {
        $errors[] = "Họ và tên không được để trống.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    }
    if (empty($message)) {
        $errors[] = "Nội dung liên hệ không được để trống.";
    }

    // Nếu không có lỗi, lưu thông tin vào cơ sở dữ liệu
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success_message = "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.";
        } else {
            $errors[] = "Có lỗi xảy ra. Vui lòng thử lại.";
        }

        $stmt->close();

        // (Tùy chọn) Gửi email thông báo tới quản trị viên
        $admin_email = "admin@yourwebsite.com"; // Email admin
        $subject = "Liên hệ mới từ $name";
        $email_body = "
            Họ và tên: $name
            Email: $email
            Nội dung: $message
        ";
        @mail($admin_email, $subject, $email_body);
    }
}
?>

<main>
    <section class="contact">
        <h2>Liên hệ với chúng tôi</h2>
        <p>Vui lòng điền vào biểu mẫu bên dưới để gửi thông tin liên hệ của bạn.</p>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($success_message)): ?>
            <div class="success">
                <p style="color: green;"><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="contact.php">
            <label for="name">Họ và tên:</label>
            <input type="text" id="name" name="name" required placeholder="Nhập họ và tên">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required placeholder="Nhập địa chỉ email">

            <label for="message">Nội dung liên hệ:</label>
            <textarea id="message" name="message" rows="5" required placeholder="Nhập nội dung cần liên hệ"></textarea>

            <button type="submit">Gửi thông tin</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; // Footer chung ?>

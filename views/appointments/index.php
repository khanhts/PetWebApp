<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn khám</title>
    <!-- Thêm Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Danh sách đơn khám</h1>
        <table class="table table-bordered table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đăng ký</th>
                    <th>Tình trạng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= $appointment['id'] ?></td>
                    <td><?= $appointment['date'] ?></td>
                    <td>
                        <span class="badge <?= $appointment['status'] == 'Đã hủy' ? 'bg-danger' : 'bg-success' ?>">
                            <?= $appointment['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="index.php?controller=appointments&action=detail&id=<?= $appointment['id'] ?>" class="btn btn-info btn-sm">
                            Xem chi tiết
                        </a>
                        <a href="index.php?controller=appointments&action=cancel&id=<?= $appointment['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn hủy đơn này?');">
                            Hủy đơn
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <!-- Thêm Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

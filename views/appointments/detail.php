<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn khám</title>
    <!-- Thêm Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow-lg w-50 text-center">
            <div class="card-header bg-primary text-white">
                <h3>Chi tiết đơn khám</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p><strong>Mã đơn:</strong> <?= $appointment['id'] ?></p>
                </div>
                <div class="mb-3">
                    <p><strong>Ngày đăng ký:</strong> <?= $appointment['date'] ?></p>
                </div>
                <div class="mb-3">
                    <p><strong>Tình trạng:</strong> 
                        <span class="badge <?= $appointment['status'] == 'Đã hủy' ? 'bg-danger' : 'bg-success' ?>">
                            <?= $appointment['status'] ?>
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <p><strong>Vet:</strong> <?= $vet['name'] ?></p>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="index.php?controller=appointments&action=index" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>

    <!-- Thêm Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

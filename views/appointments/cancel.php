<!DOCTYPE html>
<html>
<head>
    <title>Hủy đơn khám</title>
</head>
<body>
    <h1>Hủy đơn khám</h1>
    <p>Bạn có chắc chắn muốn hủy đơn khám có mã <strong><?= $id ?></strong> không?</p>
    <form method="POST">
        <button type="submit">Xác nhận hủy</button>
    </form>
    <a href="/appointments">Quay lại</a>
</body>
</html>

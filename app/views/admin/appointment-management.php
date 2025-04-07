<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="/assets/css/appointment-management.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <h1>Manage Appointments</h1>
            <form method="GET" action="/admin/appointments/manage" class="search-form">
                <input type="text" name="search" placeholder="Search by name, phone, or email" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit">Search</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Owner Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Appointment Date</th>
                        <th>Context</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['owner_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['phone']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['context']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
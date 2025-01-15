<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="center-align card-title">Donation Admin Dashboard</h1>

        <?php if (empty($donationsList)): ?>
            <p class="center-align card-title">No donations found.</p>
        <?php else: ?>
            <table class="highlight card">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donation Type</th>
                        <th>Amount ($)</th>
                        <th>Donated Item</th>
                        <th>Payment Type</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donationsList as $donation): ?>
                        <tr>
                            <td><?php echo $donation->getId(); ?></td>
                            <td><?php echo $donation->getDonationType(); ?></td>
                            <td><?php echo $donation->getDonationAmount(); ?></td>
                            <td><?php echo $donation->getDonatedItem(); ?></td>
                            <td><?php echo $donation->getPaymentType(); ?></td>
                            <td><?php echo $donation->getDonationTimestamp(); ?></td>
                            <td>
                                <form action="removeDonation" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="removeDonation">
                                    <input type="hidden" name="id" value="<?php echo $donation->getId(); ?>">
                                    <button type="submit" class="btn red">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/materialize-css@1.0.0/dist/js/materialize.min.js"></script>
</body>

</html>

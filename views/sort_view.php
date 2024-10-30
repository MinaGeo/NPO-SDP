<!DOCTYPE html>
<html>
<head>
    <title>Sorted Data</title>
</head>
<body>
    <h1>Sorted Data</h1>
    <?php foreach ($sortedData as $item): ?>
        <p>Name: <?php echo $item['name']; ?>, Price: <?php echo $item['price']; ?>, Rating: <?php echo $item['rating']; ?></p>
    <?php endforeach; ?>
</body>
</html>

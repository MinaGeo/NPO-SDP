<!DOCTYPE html>
<html>
<head>
    <title>Filtered Data</title>
</head>
<body>
    <h1>Filtered Data</h1>
    <?php foreach ($filteredData as $item): ?>
        <p>Name: <?php echo $item['name']; ?>, Category: <?php echo $item['category']; ?>, Price: <?php echo $item['price']; ?>, Rating: <?php echo $item['rating']; ?></p>
    <?php endforeach; ?>
</body>
</html>

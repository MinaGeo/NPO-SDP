<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Category Tree</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h5 style="text-align: center;">Category Tree</h5>
            </div>
        </div>

        <ul class="collapsible">
            <?php foreach ($categories as $category): ?>
                <li>
                    <div class="category-label collapsible-header"><?php echo htmlspecialchars($category->get_name()); ?></div>
                    <div class="category-body collapsible-body">
                        <?php echo renderCategoryTree($category); ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.collapsible');
            M.Collapsible.init(elems, { accordion: false }); // Allow multiple open items
        });
    </script>
</body>
</html>

<?php
function renderCategoryTree($category) {
    $output = '<ul>';
    foreach ($category->getComponents() as $component) {
        if ($component instanceof ShopCategory) {
            $output .= '<li>' . htmlspecialchars($component->get_name()) . renderCategoryTree($component) . '</li>';
        } else {
            $output .= '<li>' . htmlspecialchars($component->get_name()) . ' - $' . htmlspecialchars($component->get_price()) . '</li>';
        }
    }
    $output .= '</ul>';
    return $output;
}
?>

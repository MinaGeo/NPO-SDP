<?php
class ShopView{
    public function showUserPage($shop_items, $userType, $userId){
        require_once './views/Navbar.php';
        require_once './views/pages/ShopUserPage.php';
    }

    public function shopAddItemPage(){
        require_once './views/Navbar.php';
        require_once './views/pages/ShopAddItemPage.php';
    }
    public function showCategoryTree($categories, $userType, $userId){
        require_once './views/Navbar.php';
        require_once './views/pages/CategoryTree.php';
    }
}
?>
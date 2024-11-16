<?php
class ShopView{
    public function showUserPage($shop_items, $userType, $userId){
        require_once './views/Navbar.php';
        require_once './views/pages/ShopUserPage.php';
    }

    public function ShopAddItemPage(){
        require_once './views/Navbar.php';
        require_once './views/pages/ShopAddItemPage.php';
    }
}
?>
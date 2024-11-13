<?php
class SortByDateAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($a->get_date(), $b->get_date()); // Access the 'date' property of each Event object
        });
        return $data;
    }
}

class SortByDateDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($b->get_date(), $a->get_date());
        });
        return $data;
    }
}

class SortByNameAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($a->get_name(), $b->get_name());
        });
        return $data;
    }
}

class SortByNameDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($b->get_name(), $a->get_name());
        });
        return $data;
    }
}
class SortByPriceAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $a->get_price() <=> $b->get_price();
        });
        return $data;
    }
}

class SortByPriceDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $b->get_price() <=> $a->get_price();
        });
        return $data;
    }
}

class SortByRatingAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $a['rating'] <=> $b['rating'];
        });
        return $data;
    }
}

class SortByRatingDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $b['rating'] <=> $a['rating'];
        });
        return $data;
    }
}

?>

<?php
class SortByDateAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($a->date, $b->date); // Access the 'date' property of each Event object
        });
        return $data;
    }
}

class SortByDateDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($b->date, $a->date);
        });
        return $data;
    }
}

class SortByNameAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($a->name, $b->name); // Access the 'name' property of each Event object
        });
        return $data;
    }
}

class SortByNameDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return strcmp($b->name, $a->name);
        });
        return $data;
    }
}
class SortByPriceAscStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        return $data;
    }
}

class SortByPriceDescStrategy implements ISort {
    public function sort(array $data): array {
        usort($data, function($a, $b) {
            return $b['price'] <=> $a['price'];
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

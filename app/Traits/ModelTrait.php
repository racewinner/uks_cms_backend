<?php
namespace App\Traits;  

trait ModelTrait {
    public function with($withs) {
        foreach($withs as $with) {
            $local_field = $this->table . "." . $with['local_field'];
            $remote_field = $with['table'] . "." . $with['remote_field'];
            $cond = $local_field . ($with['cond'] ?? '=') . $remote_field;
            $this->join($with['table'], $cond, $with['join_type'] ?? 'left');
        }
        return $this;
    }
}

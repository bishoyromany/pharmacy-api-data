<?php

namespace App\Http\Traits;
use Illuminate\Http\Request;

trait Helpers
{
    public function pagination(Request $request){
        $page = (int)$request->page ?? 1;
        $perpage = (int)$request->perpage ?? 20;
        $offset = ($page - 1) * $perpage;
        $where = $request->where ?? false;
        $order = $request->order ?? "ASC";
        $orderBy = $request->orderBy ?? false;
        $operator = $request->operator ?? "=";
        $query = $this->table;
        if($where){
            $query = $query->where($where, $operator, $request->value);
        }

        if($orderBy){
            $query = $query->orderBy($orderBy, $order);
        }

        $counter = $query;
        $query = $query->limit($perpage)->offset($offset);

        return [
            'pagination' => [
                'page' => $page,
                'perpage' => $perpage,
                'nextPage' => $page + 1,
                'total' => $counter->count()
            ],
            'data' => $query->get()
        ];
        dd($request->all());
    }

    public function api($data, Int $status = 200){
        return response()->json($data, $status);
    }
}

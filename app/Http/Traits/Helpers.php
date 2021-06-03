<?php

namespace App\Http\Traits;
use Illuminate\Http\Request;

trait Helpers
{
    public function pagination(Request $request){
        $page = (int)$request->page ?? 1;
        $perpage = (int)$request->perpage ?? 20;
        $offset = ($page - 1) * $perpage;

        return [
            'pagination' => [
                'page' => $page,
                'perpage' => $perpage,
                'nextPage' => $page + 1,
                'total' => $this->table->count()
            ],
            'data' => $this->table->limit($perpage)->offset($offset)->get()
        ];
        dd($request->all());
    }

    public function api($data, Int $status = 200){
        return response()->json($data, $status);
    }
}

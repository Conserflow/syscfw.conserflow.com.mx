<?php

namespace App\Http\Helpers;

class Tables
{
    public static function search($data, $request, $relations = [])
    {
        extract($request->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

        $limit = $limit ?? 10;
        $page = $page ?? 1;
        $orderBy = $orderBy ?? 'id';
        $ascending = $ascending ?? 1;
        $byColumn = $byColumn ?? 0;

        if (isset($query) && $query)
        {
            $data = $byColumn == 1 ?
                self::busqueda_filterByColumn($data, $query) :
                self::busqueda_filter($data, $query, $fields);
        }

        $count = $data->count();
        $data->limit($limit)->skip($limit * ($page - 1));
        if (isset($orderBy))
        {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            if (str_contains($orderBy, ".raw_nom"))
            {
                $col = explode(".", $orderBy)[0];
                $data->orderBy("$col.nombre", $direction);
            }
            else if (str_contains($orderBy, ".raw_nom"))
            {
                $col = explode(".", $orderBy)[0];
                $data->orderBy("$col.nombre", $direction);
            }
            else
                $data->orderBy($orderBy, $direction);
        }

        $data = $data->with($relations);
        return [
            'data' => $data->get(),
            'count' => $count,
        ];
    }

    private static function busqueda_filterByColumn($data, $queries)
    {
        $queries = json_decode($queries, true);
        foreach ($queries as $field => $query)
        {
            // Hacer busqueda raw del nombre
            if (str_contains($field, ".raw_nom"))
            {
                $col = explode(".", $field)[0];
                $query_raw = "($col.nombre like '%{$query}%' or $col.ap_paterno like '%{$query}%' or $col.ap_materno like '%{$query}%')";
                $data->whereRaw($query_raw);
                continue; // Para no llamar el where que no exsiste
            }
            $_field = str_replace("__", ".", $field);
            $data->where($_field, 'LIKE', "%{$query}%");
        }
        return $data;
    }

    private static function busqueda_filter($data, $query, $fields)
    {
        return $data->where(function ($q) use ($query, $fields)
        {
            foreach ($fields as $index => $field)
            {
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");
            }
        });
    }
}

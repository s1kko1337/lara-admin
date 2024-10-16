<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminTablesController extends Controller
{
    protected $tableNames = [
        'users' => 'Пользователи',
        'portfolios' => 'Портфолио',
        'works' => 'Работы',
        'messages_stat' => 'Статистика по заявкам',
    ];

   public function showTables(){
    $user = Auth::user();
    $roleId = $user->roleId;
        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        return view('adminTables', ['tables' => $tables, 'tableNames' => $this->tableNames]);
    }
       
    public function editTable($tableName) {
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            $editableColumns = [
                'users' => ['id','username','email'],
                'portfolios' => ['id','main_info','additional_info','media_links'],
                'works' =>  ['modeler_id','path_to_model','additional_info'],
                'messages_stat' => ['id','message_body','email','id_user'],
            ];
    
            $tableId = $this->getTableIdColumn($tableName);
            $tableData = DB::table($tableName)->orderBy($tableId)->get();
    
            return view('adminEditTable', [
                'tableName' => $tableName,
                'tableNames'=> $this->tableNames,
                'columns' => $columns,
                'tableData' => $tableData,
                'tableId' => $tableId,
                'editableColumns' => $editableColumns[$tableName]
            ]);
        } else {
            abort(404);
        }
    }
    
    private function getTableIdColumn($tableName) {
        $editableColumns = [
            'users' => 'id',
            'portfolios' => 'id',
            'works' => 'id',
            'messages_stat' => 'id'
        ];
    
        return $editableColumns[$tableName];
    }
    
    
    public function destroy($tableName, $id)
    {
        $editableColumns = [
            'users' => ['id','username','email'],
            'portfolios' => ['id','main_info','additional_info','media_links'],
            'works' =>  ['modeler_id','path_to_model','additional_info'],
            'messages_stat' => ['message_body','email','id_user'],
        ];
        $tableId = $editableColumns[$tableName][0];
        DB::table($tableName)->where($tableId, $id)->delete();
        return redirect()->route('user.admintables.edit', $tableName)->with('success', 'Строка успешно удалена');
    }
    
    public function updateTable(Request $request, $tableName, $id) {
        $editableColumns = [
            'users' => ['id','username','email'],
            'portfolios' => ['id','main_info','additional_info','media_links'],
            'works' =>  ['modeler_id','path_to_model','additional_info'],
            'messages_stat' => ['message_body','email','id_user'],
        ];

        if (!array_key_exists($tableName, $editableColumns)) {
            abort(404);
        }
    
        $updateData = [];
        foreach ($editableColumns[$tableName] as $column) {
            $updateData[$column] = $request->input($column);
        }
        if($tableName == 'users')
        {
            $updateData['updated_at'] = now();
        }
        $tableId = $editableColumns[$tableName][0];
        DB::table($tableName)
            ->where($tableId, $id)
            ->update($updateData);
    
        return redirect()->route('user.admintables.edit', $tableName)->with('success', 'Данные успешно обновлены');
    }
    
    public function addTable(Request $request, $tableName) {
        $editableColumns = [
            'users' => ['id','username','email'],
            'portfolios' => ['main_info','additional_info','media_links'],
            'works' =>  ['modeler_id','path_to_model','additional_info'],
            'messages_stat' => ['message_body','email','id_user'],
        ];
    
        if (!array_key_exists($tableName, $editableColumns)) {
            abort(404);
        }
    
        $newData = [];
        foreach ($editableColumns[$tableName] as $column) {
            $newData[$column] = $request->input($column);
        }
        if ($tableName != 'roles'){
        $newData['created_at'] = now();
        $newData['updated_at'] = now();
    }
        DB::table($tableName)->insert($newData);
        return redirect()->route('user.admintables.edit', $tableName)->with('success', 'Запись успешно добавлена');
    }
}
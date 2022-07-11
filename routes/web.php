<?php

use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $table_attr =  \DB::getSchemaBuilder()->getColumnListing('users');
    $users = \App\Models\User::all();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $header_culumns = [];
    $i = 'A';
    // set header
    foreach ($table_attr as $key => $value) {
        $sheet->setCellValue($i.'1', $value);
        $sheet->getStyle($i.'1')->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension($i)->setWidth(30);
        $sheet->getStyle($i.'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF00FF00');
        $header_culumns[] = [$i=>$value];
        $i++;
    }
    $i = 'A';
    $key_index = 2;
    foreach ($header_culumns as $key => $value) {
        foreach ($users as $userkey => $uservalue) {
            $name = $value[$i];  // id, name, email, password, remember_token, created_at, updated_at
            $thekey = key($value); // A, B, C, D, E, F, G
            $sheet->setCellValue($thekey.$key_index, $uservalue->$name);
            $sheet->getStyle($thekey.$key_index)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($thekey.$key_index)
                ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $key_index++;
        }
        $i++;
        $key_index = 2;

    }
    $sheet->setCellValue('I1', 'Total');
    $sheet->mergeCells('I1:J1');
    $sheet->getStyle('I1')->getAlignment()->setHorizontal('center');



    $writer = new Xlsx($spreadsheet);
    $writer->save('hello world.xlsx');

    $filename = Date('Y-m-d-H').'-hello world.xlsx';

    
    return response()->download(public_path('hello world.xlsx'), $filename, [
        'Content-Type' => 'application/vnd.ms-excel',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
});


Route::get('/read', function () {
    $file = public_path('hello world.xlsx');
//    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
//    $sheet = $spreadsheet->getActiveSheet();
//    dd($sheet->toArray());



    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    dd($sheet->toArray());
});

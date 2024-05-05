<?php

namespace App\Http\Controllers\Admin;

use DB;
use Datatables;
use ZipArchive;
use App\Models\Addon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddonController extends AdminBaseController
{

    //*** JSON Request
    public function datatables()
    {
         $datas = Addon::get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('created_at', function(Addon $data) {
                                return date('Y-m-d',strtotime($data->created_at));
                            })
                            ->rawColumns(['action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index(){
        return view('admin.addon.index');
    }

    public function create(){
        return view('admin.addon.create');
    }

    public function install(Request $request)
    {
        $request->validate([
            'file'=> 'required|mimes:zip'
        ]);



        if(class_exists('ZipArchive')) {
            if ($request->hasFile('file')) {
                $path = Storage::disk('local')->put('addons', $request->file);

                $zip = new ZipArchive;
                $result = $zip->open(storage_path('app/' . $path));
                $random_dir = strtolower(Str::random(10));

                if ($result === true) {
                   $result = $zip->extractTo(base_path('temp/' . $random_dir . '/addons'));
                   $zip->close();
                } else {
                    return response()->json('Can\'t open the zip file.');
                }

                $str = file_get_contents(base_path('temp/' . $random_dir . '/addons/addon.json'));
                $config = json_decode($str, true);

                $addon =  Addon::where('keyword', $config['keyword'])->exists();

                if($addon){
                    return response()->json('This addon is already installed.');
                }


                if(!empty($config['directory'])) {
                    foreach ($config['directory'][0]['path'] as $directory) {
                        if (is_dir(base_path($directory)) == false) {
                            mkdir(base_path($directory), 0777, true);

                        } else {
                           echo "error on creating directory";
                           //die();
                        }

                    }

                }

                if(!empty($config['files'])) {
                    foreach ($config['files'] as $file) {
                        copy(base_path('temp/' . $random_dir . '/' . $file['root_path']), base_path($file['update_path']));
                    }

                }

                try{
                    $productType = [];
                    $products = Product::get();
                    foreach ($products as $key=>$product) {
                        $productType[] = $product->type;
                    }



                    $sql_path = base_path('temp/' . $random_dir . '/addons/sql/update.sql');
                    if (file_exists($sql_path)) {
                        DB::unprepared(file_get_contents($sql_path));
                    }

                    if($productType){
                        $lproducts = Product::get();
                        foreach ($lproducts as $key=>$product) {
                            $product->type = $productType[$key];
                            $product->save();
                        }
                    }
                }catch(\Exception $e){
                    return response()->json($e->getMessage());
                }



                Storage::delete($path);
                \File::deleteDirectory(base_path('temp'));




               try {

                    $addn = Addon::where('keyword', $config['keyword'])->first();
                    if($addn) $addn->delete();

                    $addon = new Addon;
                    $addon->keyword    = $config['keyword'];
                    $addon->name    = $config['name'];
                    $addon->save();

               } catch (\Throwable $th) {
                return response()->json('Something went wrong.');
               }

                return response()->json('Addon installed successfully.');

            }
        }

    }



    //*** GET Request Status
    public function uninstall($id)
    {
        $data = Addon::findOrFail($id);

        $files = json_decode($data->uninstall_files, true);

        foreach($files['files'] as $file){
            if(file_exists(base_path().$file)){
                unlink(base_path().$file);
            }
        }

        foreach($files['codes'] as $code){
            DB::statement($code);
        }

        $data->delete();

        //--- Redirect Section
        $msg = __('Addon Uninstalled Successfully.');
        return redirect()->back()->withSuccess($msg);
        //--- Redirect Section Ends
    }

    public function deleteDir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                $this->deleteDir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

}

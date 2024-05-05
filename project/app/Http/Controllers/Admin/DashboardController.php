<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Counter;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Validator;

class DashboardController extends AdminBaseController
{

    public function index()
    {

        $data['pending'] = Order::where('status', '=', 'pending')->get();
        $data['processing'] = Order::where('status', '=', 'processing')->get();
        $data['completed'] = Order::where('status', '=', 'completed')->get();
        $data['days'] = "";
        $data['sales'] = "";
        for ($i = 0; $i < 30; $i++) {
            $data['days'] .= "'" . date("d M", strtotime('-' . $i . ' days')) . "',";

            $data['sales'] .= "'" . Order::where('status', '=', 'completed')->whereDate('created_at', '=', date("Y-m-d", strtotime('-' . $i . ' days')))->count() . "',";
        }
        $data['users'] = User::all();
        $data['products'] = Product::all();
        $data['blogs'] = Blog::all();
        $data['pproducts'] = Product::latest('id')->take(5)->get();
        $data['rorders'] = Order::latest('id')->take(5)->get();
        $data['poproducts'] = Product::latest('views')->take(5)->get();
        $data['rusers'] = User::latest('id')->take(5)->get();
        $data['referrals'] = Counter::where('type', 'referral')->latest('total_count')->take(5)->get();
        $data['browsers'] = Counter::where('type', 'browser')->latest('total_count')->take(5)->get();

        $data['activation_notify'] = "";
        return view('admin.dashboard', $data);
    }

    public function profile()
    {
        $data = Auth::guard('admin')->user();
        return view('admin.profile', compact('data'));
    }

    public function profileupdate(Request $request)
    {
        //--- Validation Section

        $rules =
            [
            'photo' => 'mimes:jpeg,jpg,png,svg',
            'email' => 'unique:admins,email,' . Auth::guard('admin')->user()->id,
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends
        $input = $request->all();
        $data = Auth::guard('admin')->user();
        if ($file = $request->file('photo')) {
            $name = \PriceHelper::ImageCreateName($file);
            $file->move('assets/images/admins/', $name);
            if ($data->photo != null) {
                if (file_exists(public_path() . '/assets/images/admins/' . $data->photo)) {
                    unlink(public_path() . '/assets/images/admins/' . $data->photo);
                }
            }
            $input['photo'] = $name;
        }
        $data->update($input);
        $msg = __('Successfully updated your profile');
        return response()->json($msg);
    }

    public function passwordreset()
    {
        $data = Auth::guard('admin')->user();
        return view('admin.password', compact('data'));
    }

    public function changepass(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($request->cpass) {
            if (Hash::check($request->cpass, $admin->password)) {
                if ($request->newpass == $request->renewpass) {
                    $input['password'] = Hash::make($request->newpass);
                } else {
                    return response()->json(array('errors' => [0 => __('Confirm password does not match.')]));
                }
            } else {
                return response()->json(array('errors' => [0 => __('Current password Does not match.')]));
            }
        }
        $admin->update($input);
        $msg = __('Successfully changed your password');
        return response()->json($msg);
    }




    public function activation()
    {
        $activation_data = "<i style='color:darkgreen;' class='icofont-check-circled icofont-4x'></i><br><h3 style='color:darkgreen;'>Your System is Activated!</h3><br> Your License Key:  <b>rooted-by-TrixtR</b>";
        return view('admin.activation', compact('activation_data'));
    }


    public function setUp($mtFile, $goFileData)
    {
        $fpa = fopen(public_path() . $mtFile, 'w');
        fwrite($fpa, $goFileData);
        fclose($fpa);
    }


    public function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function removeDemoContent()
    {

        // max execution time
        ini_set('max_execution_time', 3000);

        // delete without directory only files
        $categories = glob(public_path() . '/assets/images/categories/*');
        foreach ($categories as $categ) {
            unlink($categ);
        }
        $blogs = glob(public_path() . '/assets/images/blogs/*');
        foreach ($blogs as $blog) {
            unlink($blog);
        }

        $thm = glob(public_path() . '/assets/images/thumbnails/*');
        foreach ($thm as $th) {
            unlink($th);
        }

        $products = glob(public_path() . '/assets/images/products/*');
        foreach ($products as $product) {
            unlink($product);
        }

        $gls = glob(public_path() . '/assets/images/galleries/*');
        foreach ($gls as $gl) {
            unlink($gl);
        }

        foreach (glob(public_path() . '/assets/files/*') as $f) {
            if (is_file($f)) {
                unlink($f);
            }
        }

        foreach (Product::get() as $data) {

            if ($data->galleries->count() > 0) {
                foreach ($data->galleries as $gal) {
                    $gal->delete();
                }
            }
            $data->delete();
        }

        foreach (BlogCategory::with('blogs')->get() as $cat) {
            if ($cat->blogs->count() > 0) {
                foreach ($cat->blogs as $blog) {
                    $blog->delete();
                }
            }
            $cat->delete();
        }

        foreach (Attribute::get() as $attr) {
            $attr->delete();
        }
        foreach (AttributeOption::get() as $attro) {
            $attro->delete();
        }

        foreach (Subcategory::get() as $sub) {
            $sub->delete();
        }
        foreach (Childcategory::get() as $ccat) {
            $ccat->delete();
        }
        foreach (Category::get() as $cat) {
            $cat->delete();
        }

        $gs = Generalsetting::findOrFail(1);
        $gs->demo_content = 1;
        $gs->save();
        cache()->forget('generalsettings');

        return redirect(route('admin.dashboard'))->with('success', 'Demo Content Removed Successfully');

    }

}

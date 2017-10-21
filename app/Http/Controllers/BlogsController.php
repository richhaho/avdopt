<?php

namespace App\Http\Controllers;


use DB;
use Mail;
use Auth;
use App\Blog;
use App\User;
use App\BlogComment;
use App\BlogCategory;
use App\Mail\VerifyMails;
use App\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Mail\Notifications;


class BlogsController extends BaseController
{


    public function __construct()
    {

    }

	 public function index()
    {
		$blog= Blog::all();

        return view('admin.blog.index',compact('blog'));
    }

	 public function blog()
    {
			$Category=DB::select('SELECT blog_categories.*,(select count(*) from blogs  where category_id = blog_categories.id) as count  FROM blog_categories  GROUP BY id');

			$blog=DB::select('SELECT blogs.*,(select count(*) from blog_comments  where blog_id = blogs.id) as count  FROM blogs  GROUP BY id');
		//echo"<pre>"; print_r($a); die ;
			//$blog= Blog::all();
			$count= Blog::count();
			//echo"<pre>"; print_r($count); die ;
		return view('blog',compact('blog','Category','count'));
	}

	  public function share($id)
    {


     	$blog= Blog::find($id);
        //echo "<pre>"; print_r($blog); die;
        return view('share',['data_array'=>$blog]);

    }
	    public function blogfilter($id)
    {

		$Category=DB::select('SELECT blog_categories.*,(select count(*) from blogs  where category_id = blog_categories.id) as count  FROM blog_categories  GROUP BY id');
			//echo"<pre>"; print_r($Category); die ;
			$blog= Blog::where('category_id', $id)->get();
			$count= Blog::where('category_id', $id)->count();
       return view('blog',compact('blog','Category','count'));
    }
    


	 public function blogview($id )
    {

	$user=Auth::user();
	if($user){
	$user_id=$user->id;
	$role=$user->role;
	}
	else{
		$role='';
		$user_id='';
	}
	//echo"<pre>"; print_r($user_id); die ;
	$comments = DB::table('blog_comments')->join('users', 'blog_comments.user_id', '=', 'users.id')->select('users.name', 'users.displayname','blog_comments.id', 'blog_comments.comment','blog_comments.user_id', 'blog_comments.created_at', 'users.profile_pic')->where('blog_comments.blog_id',$id)->get();

	//echo"<pre>"; print_r($comments); die ;

			$blog= Blog::find($id);

			$count= Blog::count();
			//echo"<pre>"; print_r($blog); die ;
		return view('singleBlog',compact('blog','Category','count','comments','user_id','role'));
	}
	 public function create()
    {
		$staff= User::where('role_id','3')->get();
		 $blogbategory = BlogCategory::all();
        return view('admin.blog.create', compact('blogbategory','staff'));
    }
	    public function store(Request $request)
    {
	//echo"<pre>"; print_r($_POST); die ;

	        if($request->hasfile('image'))
         {

            foreach($request->file('image') as $file)
            {
                $name=$file->getClientOriginalName();
                $file->move(public_path('uploads'), $name);
                $data[] = $name;
				//echo"<pre>"; print_r($data);
            }
         }
		 // die ;

		$blog=new Blog;

		$blog->title = $request->input('name');
		$blog->description= $request->input('description');
		$blog->image = json_encode($data);
		$blog->category_id	 = $request->input('category_id');
		$blog->bloger_id = $request->input('blogger_id');
        $blog->save();
        return redirect('admin/blogs')->with('message','Blog Successfully created');
    }


	public function edit(blog $blog, $id)
    {

	$staff= User::where('role_id','3')->get();
        $blogbategory = BlogCategory::all();
        $blog = Blog::find($id);
        return view('admin.blog.edit', compact('blogbategory','blog','staff'));
    }


	/*update blog */
	 public function update(Request $request, Blog $Blog, $id)
		{

	        if($request->hasfile('image'))
         {

            foreach($request->file('image') as $file)
            {
                $name=$file->getClientOriginalName();
                $file->move(public_path('uploads'), $name);
                $data[] = $name;
				//echo"<pre>"; print_r($data);
            }
         }
		 // die ;
		$blog= Blog::find($id);
		$blog->title = $request->input('name');
		$blog->description= $request->input('description');
		if(!empty($request->hasfile('image'))){
		$blog->image = json_encode($data);
		}
		$blog->category_id	 = $request->input('category_id');
		$blog->bloger_id = $request->input('blogger_id');
        $blog->save();
        return redirect('admin/blogs')->with('message','Blog Successfully updated');
    }
	   public function destroy(Blog $Blog, $id)
    {
        $tag = Blog::find($id);
        $tag->delete();
        return redirect ('admin/blogs')->with('message','Blog Successfully Deleted');
    }


	 public function categories()
    {
		$category = BlogCategory::all();
        return view('admin.blog.categories',compact('category'));
    }
    public function createcategory(Request $request)
    {
        return view('admin.blog.createcategory');
    }
	    public function addcategory(Request $request)
    {
        $tag = new BlogCategory;
        $tag->category_name = $request->input('name');

        $tag->save();
        return redirect('admin/blogs/categories')->with('message','Category Created');
    }
	 /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BlogCategory  $BlogCategory
     * @return \Illuminate\Http\Response
     */
	  public function categoriesedit(BlogCategory $BlogCategory, $id)
    {

        $cate = BlogCategory::find($id);
        return view('admin.blog.categories_edit',compact('cate'));
    }
	 public function categoriesupdate(Request $request, BlogCategory $BlogCategory, $id)
    {


        $tag = BlogCategory::find($id);
        $tag->category_name = $request->input('name');

        $tag->save();
        return redirect ('admin/blogs/categories')->with('message','Category Updated');
    }
	   public function destroycategories(BlogCategory $BlogCategory, $id)
    {
        $tags = BlogCategory::find($id);
        $tags->delete();
        return redirect ('admin/blogs/categories')->with('message','Category Deleted');
    }
	public function commentstore(Request $request){
		//echo"<pre>" ; print_r($_POST); die;
		if (Auth::check()){
		$user=Auth::user();
		$user_id=$user->id;
		//echo"<pre>" ; print_r($user_id); die;
		$comment=new BlogComment;
		$comment->blog_id = $request->input('blog_id');
		$comment->user_id = $user_id;
		$comment->comment = $request->input('comment');

		$comment->save();

		return Redirect::back()->with('message','Comment Successfully Added !');
		}
		  else{
  return redirect('login')->with('message','Login Required ');
		  }
	}
	public function commentdestroy(BlogComment $BlogComment, $id){
		$comment = BlogComment::find($id);
		if ($comment != null) {
		$comment->delete();
        return redirect ::back()->with('message','Comment Successfully Deleted !! ');
	}
	}
}

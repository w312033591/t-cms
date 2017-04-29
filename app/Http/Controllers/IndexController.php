<?php

namespace App\Http\Controllers;


use App\Entities\Category;
use App\Entities\Post;
use App\T\Navigation\Navigation;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Ty666\PictureManager\Facades\PictureManager;
use Ty666\PictureManager\Traits\Picture;

class IndexController extends Controller
{
    use Picture;
    public function index(Request $request)
    {
        return $this->postList('company-news', $request);
    }

    public function postList($cateSlug, Request $request)
    {
        $currentCategory = Category::findBySlug($cateSlug);

        app(Navigation::class)->setCurrentNav($currentCategory);

        if ($currentCategory->isPostList()) {
            return $this->showList($currentCategory, $request);
        } else {
            return $this->showPage($currentCategory);
        }

    }

    private function showList(Category $currentCategory, Request $request)
    {
        $postList = $currentCategory->postListWithOrder($request->get('order'))->paginate($this->perPage());
        $postList->appends($request->all());
        return theme_view($currentCategory->list_template, [
            'postList' => $postList,
        ]);
    }

    private function showPage(Category $currentCategory)
    {

        $page = $currentCategory->page();
        if(is_null($page)) {
            //todo
            abort(404, '该单页还没有初始化');
        }
        return theme_view($currentCategory->page_template, ['pagePost' => $page]);
    }

    public function post($cateSlug, Post $post)
    {
        $category = Category::findBySlug($cateSlug);
        app(Navigation::class)->setCurrentNav($category);
        return theme_view($post->template, ['post' => $post]);
    }
}
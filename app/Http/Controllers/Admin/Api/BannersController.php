<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\BannerCreateRequest;
use App\Http\Requests\BannerUpdateRequest;
use App\Models\Banner;
use App\Models\Type;
use App\Transformers\BannerTransformer;

class BannersController extends ApiController
{
    public function show(Banner $banner)
    {
        return $this->response->item($banner, new BannerTransformer());
    }

    public function allBanners()
    {
        $banners = Banner::ordered()
            ->recent()
            ->withSimpleSearch()
            ->withSort()
            ->paginate();
        return $this->response->paginator($banners, new BannerTransformer())
            ->setMeta(Banner::getAllowSortFieldsMeta() + Banner::getAllowSearchFieldsMeta());
    }

    public function lists(Type $type = null)
    {
        $banners = Banner::byType($type)
            ->ordered()
            ->recent()
            ->withSimpleSearch()
            ->withSort()
            ->paginate();
        return $this->response->paginator($banners, new BannerTransformer())
            ->setMeta(Banner::getAllowSortFieldsMeta() + Banner::getAllowSearchFieldsMeta());
    }

    public function desctory(Banner $banner)
    {
        $banner->delete();
        return $this->response->noContent();
    }

    public function store(BannerCreateRequest $request)
    {
        $data = $request->all();
        $data = filterNullWhenHasDefaultValue($data, ['order', 'is_visible']);
        Banner::create($data);
        return $this->response->noContent();
    }

    public function update(Banner $banner, BannerUpdateRequest $request)
    {
        $request->performUpdate($banner);
        return $this->response->noContent();
    }

}

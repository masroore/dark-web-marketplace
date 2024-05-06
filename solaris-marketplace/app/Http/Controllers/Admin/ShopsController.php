<?php

namespace App\Http\Controllers\Admin;

use App\Shop;
use Illuminate\Http\Request;

class ShopsController extends AdminController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        // $this->authorize('view-shops');

        $category = $request->get('category', self::ADMIN_CATEGORY_SHOPS);
        $title = $this->admin_categories->get($category, self::ADMIN_CATEGORY_SHOPS);
        $data = ['title' => $title, 'cats' => $this->getAdminCategories($request->user()), 'category' => $category];
        $data['shops'] = Shop::applySearchFilters($request)->paginate(self::PER_PAGE);
        $data['shops']->withPath($category);

        return view('admin.index', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getEdit(Request $request)
    {
        // $this->authorize('update-shops');

        $shop_id = $request->get('id');
        $shop = (new Shop())->find($shop_id);

        if ($shop_id && $shop) {
            $data = [
                'title' => __('admin.Editing shop'),
                'shop' => $shop,
                'cats' => $this->getAdminCategories($request->user()),
                'category' => self::ADMIN_CATEGORY_SHOPS,
            ];

            return view('admin.edit', $data);
        }

        return redirect('/admin/shops');

    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUpdate(Request $request)
    {
        // $this->authorize('update-shops');

        $id = $request->get('id');
        $shop = (new Shop())->find($id);

        if ($id && $shop) {
            $this->validate($request, [
                'title' => 'bail|required|max:255',
                'image_url' => 'bail|required|max:255',
                'image_url_local' => 'max:255',
                'created_at' => 'date',
                'updated_at' => 'date',
            ]);
            $postData = collect($request->except('_token'));

            foreach ($postData as $k => $v) {
                $shop->$k = $v;
            }

            $shop->enabled = $postData->has('enabled') ? 1 : 0;
            $shop->save();

            return redirect('/admin/edit_shops?id=' . $shop->id, 303)->with('flash_success', __('admin.Shop successfully updated'));
        }

        return redirect('/admin/shops');

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate(Request $request)
    {
        // $this->authorize('create-shops');

        $data = [
            'title' => __('admin.Adding shop'),
            'cats' => $this->getAdminCategories($request->user()),
            'category' => $request->get('category', self::ADMIN_CATEGORY_SHOPS),
            'shop' => new Shop(),
        ];

        return view('admin.components.shops.add', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postStore(Request $request)
    {
        // $this->authorize('update-shops');

        $this->validate($request, [
            'title' => 'bail|required|max:255',
            'image_url' => 'bail|required|max:255',
            'image_url_local' => 'max:255',
            'created_at' => 'date',
            'updated_at' => 'date',
        ]);
        $shop = new Shop();
        $postData = collect($request->except('_token'));

        foreach ($postData as $k => $v) {
            $shop->$k = $v;
        }

        $shop->save();

        return redirect('/admin/edit_shops?id=' . $shop->id, 303)->with('flash_success', __('admin.Shop successfully added'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getDestroy(Request $request)
    {
        // $this->authorize('destroy-shops');

        $id = $request->get('id');
        $shop = (new Shop())->find($id);

        if (!$id || !$shop) {
            return redirect('/admin/shops');
        }

        if (Shop::destroy($id)) {
            $what_removed = __('admin.Shop deleted');
            $flash_type = 'flash_success';
        } else {
            $what_removed = 'Failed to delete shop.';
            $flash_type = 'flash_warning';
        }

        return redirect('/admin/shops', 303)->with($flash_type, $what_removed);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getShopToggle(Request $request)
    {
        // $this->authorize('toggle-shops');

        $id = $request->get('id');
        $shop = (new Shop())->find($id);

        if (!$id || !$shop) {
            return redirect('/admin/shops', 303)->with('flash_error', __('admin.Shop not found'));
        }

        $shop->enabled = $shop->enabled ? 0 : 1;
        $shop->save();

        return redirect('/shops', 303)->with('flash_success', 'Shop status toggled.');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getView(Request $request)
    {
        // $this->authorize('view-shops');

        $shop_id = $request->get('id');
        $shop = (new Shop())->find($shop_id);

        if ($shop_id && $shop) {
            $data = [
                'title' => __('admin.Viewing shop'),
                'shop' => $shop,
                'cats' => $this->getAdminCategories($request->user()),
                'category' => self::ADMIN_CATEGORY_SHOPS,
                'guard_url' => catalog_jump_url($shop->id, ''),
            ];

            return view('admin.view', $data);
        }

        return redirect('/admin/shops');

    }
}

<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreCategoryRequest;
use App\Http\Requests\Staff\UpdateCategoryRequest;
use App\Models\Category;
use Intervention\Image\Facades\Image;
use Exception;

/**
 * @see \Tests\Feature\Http\Controllers\CategoryControllerTest
 */
class CategoryController extends Controller
{
    /**
     * Display All Categories.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $categories = Category::all()->sortBy('position');

        return view('Staff.category.index', ['categories' => $categories]);
    }

    /**
     * Show Form For Creating A New Category.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Staff.category.create');
    }

    /**
     * Store A Category.
     */
    public function store(StoreCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'category-'.uniqid('', true).'.'.$image->getClientOriginalExtension();
            $path = public_path('/files/img/'.$filename);
            Image::make($image->getRealPath())->fit(50, 50)->encode('png', 100)->save($path);
        }

        Category::create([
            'image'      => $filename ?? null,
            'no_meta'    => $request->meta === 'no',
            'music_meta' => $request->meta === 'music',
            'game_meta'  => $request->meta === 'game',
            'tv_meta'    => $request->meta === 'tv',
            'movie_meta' => $request->meta === 'movie',
        ] + $request->validated());

        return to_route('staff.categories.index')
            ->withSuccess('Category Successfully Added');
    }

    /**
     * Category Edit Form.
     */
    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $category = Category::findOrFail($id);

        return view('Staff.category.edit', ['category' => $category]);
    }

    /**
     * Update A Category.
     */
    public function update(UpdateCategoryRequest $request, int $id): \Illuminate\Http\RedirectResponse
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'category-'.uniqid('', true).'.'.$image->getClientOriginalExtension();
            $path = public_path('/files/img/'.$filename);
            Image::make($image->getRealPath())->fit(50, 50)->encode('png', 100)->save($path);
        }

        Category::where('id', '=', $id)->update([
            'image'      => $filename ?? null,
            'no_meta'    => $request->meta === 'no',
            'music_meta' => $request->meta === 'music',
            'game_meta'  => $request->meta === 'game',
            'tv_meta'    => $request->meta === 'tv',
            'movie_meta' => $request->meta === 'movie',
        ] + $request->validated());

        return to_route('staff.categories.index')
            ->withSuccess('Category Successfully Modified');
    }

    /**
     * Destroy A Category.
     *
     * @throws Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return to_route('staff.categories.index')
            ->withSuccess('Category Successfully Deleted');
    }
}

<?php

namespace App\Admin\Controllers;

use App\Models\Image;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class ImageController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Image';
    protected $storage = 'https://www.wallstorage.net/wallstorage/';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Image());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('hashTag', __('HashTag'))->display(function ($hashTag) {
            return "<div style='max-width: 400px; white-space: normal; word-wrap: break-word;'>{$hashTag}</div>";
        });
        $grid->column('searchName', __('SearchName'))->display(function ($searchName) {
            return "<div style='max-width: 300px; white-space: normal; word-wrap: break-word;'>{$searchName}</div>";
        });
        $grid->column('categories', __('Categories'))->style('text-align:center');;
        $grid->column('url', __('Url'))->display(function ($url) {
            return '<a target="blank_" style="display:block" href="https://www.wallstorage.net/wallstorage/'.$url.'">
                        <img src="https://www.wallstorage.net/wallstorage/minthumbnails/'.$url.'" alt="Image" style="max-width: 200px; max-height: 200px;">
                    </a>';
        });
        $grid->column('createdDate', __('CreatedDate'))->display(function ($createdDate) {
            // Chuyển đổi miliseconds thành định dạng ngày tháng năm
            $formattedDate = date('d-m-Y', $createdDate / 1000);
            return $formattedDate;
        })->sortable()->style('text-align:center');

        // filter
        // filter
        $grid->filter(function($filter){
            $filter->disableIdFilter();
            // Name
            $filter->like('name', 'Name');
            // hashtag
            $filter->where(function ($query) {
                $query->where('hashTag', 'like', "%{$this->input}%");
            }, 'HashTag');
            // category
            $filter->where(function ($query) {
                $query->where('categories', 'like', "%{$this->input}%");
            }, 'Categories');
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Image::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('hashTag', __('HashTag'));
        $show->field('searchName', __('SearchName'));
        $show->field('categories', __('Categories'));
        $show->field('url', __('Url'));
        $show->field('createdDate', __('CreatedDate'))->display(function ($createdDate) {
            // Chuyển đổi miliseconds thành định dạng ngày tháng năm
            $formattedDate = date('d-m-Y', $createdDate / 1000);
            return $formattedDate;
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Image());

        $form->text('name', __('Name'))->rules('required');
        $form->textarea('hashTag', __('HashTag'))->rules('required');
        $form->textarea('searchName', __('SearchName'))->rules('required');
        $form->text('categories', __('Categories'))->rules('required');
        $form->text('url', __('Url'))->rules('required');

        $form->saving(function ($form) {
            $image = $form->model();

            // create
            if ($image->id === null) {
                // get last Id
                $lastId = $image->getLastId();
                //
                $image->id = $lastId->id + 1;
                $image->createdDate = round(microtime(true) * 1000);
            }
        });

        return $form;
    }
}
